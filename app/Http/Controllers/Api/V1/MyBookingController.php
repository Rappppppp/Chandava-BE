<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MyBookingResource;
use App\Http\Requests\V1\StoreMyBookingRequest;
use App\Filters\MyBookingFilter;
use App\Models\MyBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyBookingController extends Controller
{
    //

    public function index(Request $request)
    {
        $filters = new MyBookingFilter($request);
        $myBooking = $filters->apply(MyBooking::query())->with(['user', 'room'])->get();
        return MyBookingResource::collection($myBooking);
        // return MyBookingResource::collection(MyBooking::with(['user', 'room'])->get());
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:my_bookings,id',
            'status' => 'required|string|in:pending,confirmed,cancelled', // Adjust valid statuses if needed
        ]);

        $booking = MyBooking::find($validated['id']);
        $booking->status = $validated['status'];
        $booking->save();

        return response()->json([
            'message' => 'Booking status updated successfully.',
            'booking' => $booking
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
