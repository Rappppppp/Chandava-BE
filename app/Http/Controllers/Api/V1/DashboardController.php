<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\MyBooking;
use App\Models\User;
use App\Http\Resources\V1\MyBookingResource;


use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        // Count of total bookings where status is NOT 'cancelled' or 'pending'
        $bookingCount = MyBooking::whereNotIn('status', ['cancelled', 'pending'])->count();

        // Count of users where role is NOT 'admin'
        $userCount = User::where('role', '!=', 'admin')->count();

        // Total revenue: sum of total_price where status is 'completed'
        $totalRevenue = MyBooking::where('status', 'completed')->sum('total_price');

        // Count of rooms where is_already_check_in is false
        $availableRoomCount = Room::where('is_already_check_in', false)->where('is_deleted', false)->count();

        $recentBookings = MyBooking::latest()->take(5)->with(['user', 'room'])->get();

        return response()->json([
            'total_bookings' => $bookingCount,
            'total_users' => $userCount,
            'total_revenue' => $totalRevenue,
            'available_rooms' => $availableRoomCount,
            'recent_bookings' => MyBookingResource::collection($recentBookings),
        ]);
    }
}
