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
        $availableRoomCount = Room::where('is_already_check_in', false)->whereNull('deleted_at')->count();

        $recentBookings = MyBooking::latest()->take(5)->with(['user', 'room'])->get();

        return response()->json([
            'total_bookings' => $bookingCount,
            'total_users' => $userCount,
            'total_revenue' => $totalRevenue,
            'available_rooms' => $availableRoomCount,
            'recent_bookings' => MyBookingResource::collection($recentBookings),
        ]);
    }

    public function getBookingYears()
    {
        return MyBooking::getBookingYears();
    }

    public function getReports(Request $request)
    {
        $type = $request->input('type');
        $year = $request->input('year');
        return MyBooking::getReports($type, $year);
    }

   public function getReportDetails(Request $request)
{
    $type = $request->input('type', 'daily');
    $year = $request->input('year');
    $month = $request->input('month');
    $checkIn = $request->input('check_in');
    $checkOut = $request->input('check_out');

    if ($type === 'weekly') {
        // Get weekly summary with nested bookings
        $weeks = MyBooking::getReports('weekly', $year);

        $data = $weeks->map(function ($week) use ($year) {
            $bookings = MyBooking::getReportDetails(
                'weekly',
                $year,
                null,
                $week->week_start,
                $week->week_end
            );

            return [
                'week_start' => $week->week_start,
                'week_end' => $week->week_end,
                'bookings_count' => $week->bookings,
                'checkIns_count' => $week->checkIns,
                'payments' => '₱' . number_format($week->payments, 2),
                'bookings_detail' => $bookings->map(function ($b) {
                    return [
                        'id' => $b->id,
                        'user' => $b->user ? [
                            'id' => $b->user->id,
                            'name' => $b->user->first_name . ' ' . $b->user->last_name,
                            'email' => $b->user->email,
                        ] : null,
                        'room' => $b->room ? [
                            'id' => $b->room->id,
                            'name' => $b->room->room_name,
                        ] : null,
                        'status' => $b->status,
                        'check_in' => $b->check_in,
                        'check_out' => $b->check_out,
                        'payments' => '₱' . number_format($b->total_price, 2),
                    ];
                }),
            ];
        });

        return response()->json($data);
    }

    // Daily or yearly: simple list of bookings
    $bookings = MyBooking::getReportDetails($type, $year, $month, $checkIn, $checkOut);

    $data = $bookings->map(function ($booking) use ($type) {
        $period = match($type) {
            'daily' => date('F j, Y', strtotime($booking->check_in)),
            'yearly' => date('Y', strtotime($booking->check_in)),
            default => date('M j', strtotime($booking->check_in)) . ' – ' . date('M j, Y', strtotime($booking->check_out)),
        };

        return [
            'id' => $booking->id,
            'type' => ucfirst($type) . ' Report',
            'period' => $period,
            'bookings' => 1, // each row = 1 booking
            'status' => $booking->status,
            'payments' => '₱' . number_format($booking->total_price, 2),
            'year' => date('Y', strtotime($booking->check_in)),
            'user' => $booking->user ? [
                'id' => $booking->user->id,
                'name' => $booking->user->first_name . ' ' . $booking->user->last_name,
                'email' => $booking->user->email,
            ] : null,
            'room' => $booking->room ? [
                'id' => $booking->room->id,
                'name' => $booking->room->room_name,
            ] : null,
            'check_in' => $booking->check_in,
            'check_out' => $booking->check_out,
        ];
    });

    return response()->json($data);
}

}
