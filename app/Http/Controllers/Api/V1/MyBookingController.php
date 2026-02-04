<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MyBookingResource;
use App\Http\Requests\V1\StoreMyBookingRequest;
use App\Filters\MyBookingFilter;
use App\Mail\BookingSuccessMail;
use App\Mail\ConfirmedBookingMail;
use App\Models\MyBooking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;

class MyBookingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            $query = MyBooking::with(['user', 'room']);
        } else {
            $query = MyBooking::where(
                'user_id',
                $user->id
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

        if ($booking->status === 'confirmed' && $booking->user) {
            Mail::to(config('app.env') === 'production' ? $booking->user?->email : 'raphaelherreria@gmail.com')
                ->send(new ConfirmedBookingMail(mailData: [
                    'receipt' => $booking->receipt,
                    'created_at' => Carbon::parse($booking->created_at)->format('F j, Y'),
                    'first_name' => $booking->user?->first_name ?? 'Guest',
                    'tour_type' => $booking->tour_type,
                    'room_name' => $booking->room->accommodationType->accommodation_type_name,
                    'guests_count' => $booking->room->accommodationType->max_guests,
                    'amount' => $booking->total_price,
                ]));
        }

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
            'booking' => MyBookingResource::make($booking),
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

        $user = auth();
        $userId = $user->id();

        $room = Room::findOrFail($validated['room_id']);

        // Check existing pending booking
        $existingPending = MyBooking::where('user_id', $userId)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending && auth()->user()->role === 'user') {
            return response()->json([
                'message' => 'You have a pending booking. Cancel it or wait for approval.',
            ], 422);
        }

        // Dates
        $checkIn = Carbon::parse($validated['check_in'])->startOfDay();
        $checkOut = Carbon::parse($validated['check_out'])->startOfDay();

        // Duration (minimum 1)
        $days = max(1, $checkIn->diffInDays($checkOut));

        // Price calculation
        switch ($validated['tour_type']) {
            case 'Day Tour':
            case 'Night Tour':
                $totalPrice = $room->day_night_tour_price * $days;
                break;

            case 'Overnight':
                $totalPrice = $room->overnight_price * $days;
                break;

            default:
                return response()->json([
                    'message' => 'Invalid booking type.',
                ], 422);
        }

        // Attach computed values
        $validated['user_id'] = $userId;
        $validated['total_price'] = $totalPrice;

        // Create booking
        $booking = MyBooking::create($validated);

        try {
            Mail::to($request->email ?? $user->user()->email)
                ->send(new ConfirmedBookingMail(mailData: [
                    'receipt' => $booking->receipt,
                    'created_at' => Carbon::parse($booking->created_at)->format('F j, Y'),
                    'first_name' => $booking->user->first_name ?? 'Guest',
                    'tour_type' => $booking->tour_type,
                    'room_name' => $booking->room->accommodationType->accommodation_type_name,
                    'guests_count' => $booking->room->accommodationType->max_guests,
                    'amount' => $booking->total_price,
                ]));
        } catch (\Exception $e) {
            info('Error creating booking: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Booking created successfully!',
            'data' => $booking,
        ], 201);
    }
}
