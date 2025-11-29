<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Filters\RoomFilter;
use App\Http\Resources\V1\RoomResource;
use App\Http\Requests\V1\StoreRoomRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Filepond;
use App\Http\Requests\V1\UpdateRoomRequest;

class RoomController extends Controller
{
    //
    public function index(Request $request)
    {
        $filter = new RoomFilter($request);

        $baseQuery = $filter->apply(Room::query())

            ->withAvg('feedbacks', 'rate');

        if (auth()->check()) {
            $rooms = $baseQuery
                ->with(['accommodationType', 'inclusions', 'images', 'feedbacks'])
                ->get();
        } else {
            // SQL Server: filter using raw subquery, not the alias
            $rooms = $baseQuery
                ->with(['accommodationType', 'inclusions', 'images'])
                ->whereHas('feedbacks', function ($q) {
                    $q->where('rate', '>=', 4);
                })
                ->limit(10)
                ->get();
        }

        return RoomResource::collection($rooms);
    }

    public function deleteRoom($id)
    {
        $room = Room::findOrFail($id);

        $room->deleted_at = now();
        $room->save();

        return response()->json(['message' => 'Deleted successfully.']);
    }


    public function show(Room $room)
    {
        return new RoomResource(
            $room->load(['accommodationType', 'inclusions', 'images'])
        );
    }

    public function updateIsAlreadyCheckedIn(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:rooms,id',
            'status' => 'required|boolean',
        ]);

        // Find the room by ID
        $room = Room::find($validated['id']);

        // Update the is_already_check_in status
        $room->is_already_check_in = $validated['status'];
        $room->save();

        return response()->json([
            'message' => 'Room check-in status updated successfully.',
            'room' => $room,
        ]);
    }



    public function store(StoreRoomRequest $request)
    {
        try {
            $room = DB::transaction(function () use ($request) {
                // Create the room
                $room = Room::create($request->only([
                    'room_name',
                    'accommodation_type_id',
                    'description',
                    'day_night_tour_price',
                    'overnight_price',
                    'notes',
                    'is_already_check_in',
                ]));

                // Attach inclusions if provided
                if ($request->filled('inclusion_ids')) {
                    $room->inclusions()->attach($request->input('inclusion_ids'));
                }

                if ($request->filled('images')) {
                    foreach ($request->input('images') as $index => $filename) {
                        // Create RoomImage
                        $room->images()->create([
                            'file' => $filename,
                            'is_main_image' => $index === 0,
                        ]);

                        // Delete the file from the Filepond table
                        Filepond::where('name', $filename)->delete();
                    }
                }

                return $room;
            });

            return response()->json([
                'message' => 'Room created successfully',
                'data' => $room->load(['accommodationType', 'inclusions', 'images']),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Room creation failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to create room',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateRoomRequest $request, Room $room)
    {
        try {
            DB::transaction(function () use ($request, $room) {
                // Update basic room fields
                $room->update($request->only([
                    'room_name',
                    'accommodation_type_id',
                    'description',
                    'day_night_tour_price',
                    'overnight_price',
                    'notes',
                    'is_already_check_in',
                ]));

                // Sync inclusions if provided
                if ($request->filled('inclusion_ids')) {
                    $room->inclusions()->sync($request->input('inclusion_ids'));
                }

                // Replace images if provided
                if ($request->filled('images')) {
                    // Delete existing RoomImage records
                    $room->images()->delete();

                    foreach ($request->input('images') as $index => $filename) {
                        $room->images()->create([
                            'file' => $filename,
                            'is_main_image' => $index === 0,
                        ]);

                        Filepond::where('name', $filename)->delete();
                    }
                }
            });

            return response()->json([
                'message' => 'Room updated successfully',
                'data' => $room->load(['accommodationType', 'inclusions', 'images']),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Room update failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to update room',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
