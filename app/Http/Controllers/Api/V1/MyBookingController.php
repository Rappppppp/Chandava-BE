<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MyBookingResource;
use App\Http\Requests\V1\StoreMyBookingRequest;
use App\Filters\MyBookingFilter;
use App\Models\MyBooking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyBookingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            $query = MyBooking::with(['user', 'room']);
        } else {
            $query = MyBooking::where(
                'user_id', $user->id
            )->with(['user', 'room']);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%");
                    })
                    ->orWhereHas('room', function ($q3) use ($search) {
                        $q3->where('room_name', 'like', "%$search%");
                    });
            });
        }

        $query->orderBy('created_at', 'desc');

        $perPage = $request->get('per_page', 10);

        $bookings = $query->paginate($perPage);

        return MyBookingResource::collection($bookings);
    }

    public function show(MyBooking $booking)
    {
        return;
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:bookings,id',
            'status' => 'required|string|in:pending,confirmed,cancelled,completed', // Adjust valid statuses if needed
        ]);

        $booking = MyBooking::find($validated['id']);
        $booking->status = $validated['status'];

        if ($booking->status === 'completed') {
            // Assuming your MyBooking model has a `room_id` foreign key
            $room = Room::find($booking->room_id);

            if ($room) {
                $room->is_already_check_in = false;
                $room->save();
            }
        }
        $booking->save();

        return response()->json([
            'message' => 'Booking status updated successfully.',
            'booking' => $booking
        ]);
    }

    public function updateDate(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'exists:bookings,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'], // ensures check_out > check_in
        ]);

        $booking = MyBooking::findOrFail($validated['id']);

        $booking->check_in = $validated['check_in'];
        $booking->check_out = $validated['check_out'];
        $booking->save();

        return response()->json([
            'message' => 'Booking dates updated successfully.',
            'booking' => $booking,
        ]);
    }



    public function store(StoreMyBookingRequest $request)
    {
        $validated = $request->validated();
        $userId = $validated['user_id'];

        // Check if the user already has a pending booking
        $existingPending = MyBooking::where('user_id', $userId)
            ->where('status', 'pending') // Adjust 'pending' based on your actual status value
            ->exists();

        if ($existingPending) {
            return response()->json([
                'message' => 'You have a pending booking, cancel it or wait for tha approval.',
            ], 422); // 422 Unprocessable Entity
        }

        // Create the booking
        $booking = MyBooking::create($validated);

        return response()->json([
            'message' => 'Booking created successfully!',
            'data' => $booking,
        ], 201);
    }
}
