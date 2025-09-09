<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\V1\StoreChangeScheduleRequest;
use App\Http\Requests\V1\ChangeScheduleRequestStatusRequest;
use App\Http\Resources\V1\ChangeScheduleRequestResource;
use App\Models\ChangeScheduleRequest;
use App\Models\MyBooking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;




class ChangeScheduleRequestController extends Controller
{

    public function index()
    {
        $all = ChangeScheduleRequest::orderByRaw("
        CASE WHEN status = 'pending' THEN 0 ELSE 1 END
    ")->get();

        return response()->json(ChangeScheduleRequestResource::collection($all));
    }

    public function getByUserId($userId)
    {
        $all = ChangeScheduleRequest::where('user_id', $userId)
            ->orderByRaw("
        CASE WHEN status = 'pending' THEN 0 ELSE 1 END
    ")->get();

        return response()->json(ChangeScheduleRequestResource::collection($all));
    }


    public function store(StoreChangeScheduleRequest $request)
    {
        $validated = $request->validated();

        $exists = ChangeScheduleRequest::where('user_id', $validated['user_id'])
            ->where('booking_id', $validated['booking_id'])
            ->where('status', 'pending')
            ->exists(); // ✅ more efficient

        if ($exists) {
            return response()->json([
                'message' => 'Change schedule request already exists.',
            ], 409); // maybe 409 Conflict is more appropriate
        }

        ChangeScheduleRequest::create($validated);

        return response()->json([
            'message' => 'Change schedule request created successfully.',
        ], 201);
    }

    public function changeStatus(ChangeScheduleRequestStatusRequest $request)
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($validated) {
            // Ensure request exists
            $changeRequest = ChangeScheduleRequest::findOrFail($validated['request_id']);

            // Update the request status
            $changeRequest->status = $validated['status'];
            $changeRequest->save();

            // Only update booking if approved
            if ($validated['status'] === 'approved') {
                $booking = MyBooking::findOrFail($changeRequest->booking_id);

                // Update booking dates
                $booking->check_in  = $changeRequest->check_in;
                $booking->check_out = $changeRequest->check_out;

                // ✅ Count days (min 1 day if same date)
                $checkIn  = Carbon::parse($changeRequest->check_in);
                $checkOut = Carbon::parse($changeRequest->check_out);
                $days = $checkIn->diffInDays($checkOut) + 1;

                // ✅ Get price based on tour_type
                $room = $booking->room; // relationship
                if ($booking->tour_type === 'day') {
                    $pricePerDay = $room->day_night_tour_price;
                } else {
                    $pricePerDay = $room->overnight_price;
                }

                // ✅ Update total_price
                $booking->total_price = $days * $pricePerDay;

                $booking->save();
            }

            return response()->json([
                'message' => 'Change schedule request status updated successfully.',
                'change_request' => $changeRequest,
            ], 200);
        });
    }
}
