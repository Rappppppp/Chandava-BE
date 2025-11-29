<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\MyBooking;
use App\Models\User;
use App\Http\Resources\V1\MyBookingResource;


use Carbon\Carbon;
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

    // fetch bookings
    $bookings = MyBooking::getReportDetails($type, $year, $month, $checkIn, $checkOut);
    $bookings = collect($bookings);

    if ($bookings->isEmpty()) {
        return response()->json([
            'week_start' => null,
            'week_end' => null,
            'bookings_count' => 0,
            'checkIns_count' => 0,
            'payments' => '₱0.00',
            'bookings_detail' => [],
        ]);
    }

    // compute summary
    $start = $bookings->map(fn($b) => $b->check_in ? Carbon::parse($b->check_in) : null)
        ->filter()
        ->min()?->toDateTimeString();

    $end = $bookings->map(fn($b) => $b->check_out ? Carbon::parse($b->check_out) : null)
        ->filter()
        ->max()?->toDateTimeString();

    $bookingsCount = $bookings->count();
    $checkInsCount = $bookings->filter(fn($b) => !empty($b->check_in))->count();
    $paymentsTotal = $bookings->sum(fn($b) => (float) ($b->total_price ?? 0));

    $bookingsDetail = $bookings->map(function ($b) {
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
            'payments' => '₱' . number_format($b->total_price ?? 0, 2),
        ];
    })->values();

    return response()->json([
        'week_start' => $start,
        'week_end' => $end,
        'bookings_count' => $bookingsCount,
        'checkIns_count' => $checkInsCount,
        'payments' => '₱' . number_format($paymentsTotal, 2),
        'bookings_detail' => $bookingsDetail,
    ]);
}

}
