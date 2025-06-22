<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Filters\RoomFilter;
use App\Http\Resources\V1\RoomResource;

class RoomController extends Controller
{
    //
    public function index(Request $request)
    {
        //
        $filter = new RoomFilter($request);
        $inclusions = $filter->apply(Room::query())->get();
        return RoomResource::collection($inclusions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_name' => 'required|string|max:255',
            'accommodation_type_id' => 'nullable|exists:accommodation_types,id',
            'description' => 'nullable|string',
            'day_night_tour_price' => 'nullable|numeric',
            'overnight_price' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'is_already_check_in' => 'boolean',
            'inclusion_ids' => 'nullable|array',
            'inclusion_ids.*' => 'exists:inclusions,id',
        ]);

        $room = Room::create($validated);

        if ($request->filled('inclusion_ids')) {
            $room->inclusions()->attach($request->input('inclusion_ids'));
        }

        return response()->json([
            'message' => 'Room created successfully',
            'data' => $room->load(['accommodationType', 'inclusions']),
        ], 201);
    }
}
