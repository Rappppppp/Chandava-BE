<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MyBookingResource;
use App\Http\Requests\V1\StoreMyBookingRequest;
use App\Models\MyBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyBookingController extends Controller
{
    //

    public function index()
    {
        return MyBookingResource::collection(MyBooking::with(['user', 'room'])->get());
    }

    public function userIndex()
    {
        $userId = Auth::id();

        $bookings = MyBooking::where('user_id', $userId)
            ->with(['room']) // optionally eager load related models
            ->get();

        return MyBookingResource::collection($bookings);
    }

    public function store(StoreMyBookingRequest $request)
    {

    
        $booking = MyBooking::create($request->validated());

        return response()->json([
            'message' => 'Booking created successfully!',
            'data' => $booking,
        ], 201);
    }
}
