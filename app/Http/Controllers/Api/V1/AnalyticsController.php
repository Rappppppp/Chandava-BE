<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\MyBooking;
use Carbon\Carbon;
use App\Models\Feedback;

class AnalyticsController extends Controller
{
    //
    public function index()
    {
        $total = Room::whereNull('deleted_at')->count();
        $available = Room::whereNull('deleted_at')->where('is_already_check_in', false)->count();
        $occupied = Room::whereNull('deleted_at')->where('is_already_check_in', true)->count();

        // Daily sales
        $dailySale = MyBooking::where('status', 'completed')
            ->whereDate('updated_at', Carbon::today())
            ->sum('total_price');

        // Weekly sales
        $weeklySale = MyBooking::where('status', 'completed')
            ->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('total_price');

        // Monthly sales
        $monthlySale = MyBooking::where('status', 'completed')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->sum('total_price');


        $dailyBooking = MyBooking::whereDate('updated_at', Carbon::today())
            ->count();

        // Weekly sales
        $weeklyBooking = MyBooking::whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        // Monthly sales
        $monthlyBooking = MyBooking::whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->count();


        $totalFeedback = Feedback::where('rate', '>', 0)->count();
        $fiveStars = Feedback::where('rate', 5)->count();
        $fourStars = Feedback::where('rate', 4)->count();
        $threeStars = Feedback::where('rate', 3)->count();
        $twoStars = Feedback::where('rate', 2)->count();
        $oneStars = Feedback::where('rate', 1)->count();

        return response()->json([
            'campsites' => [
                'total' => $total,
                'available' => $available,
                'occupied' => $occupied,
            ],
            'sales' => [
                'daily' => $dailySale,
                'weekly' => $weeklySale,
                'monthly' => $monthlySale,
            ],
            'bookings' =>[
                'daily' => $dailyBooking,
                'weekly' => $weeklyBooking,
                'monthly' => $monthlyBooking,
            ],
            'reviews' => [
                'total' => $totalFeedback,
                'fiveStars' => $fiveStars,
                'fourStars' => $fourStars,
                'threeStars' => $threeStars,
                'twoStars' => $twoStars,
                'oneStar' => $oneStars,
            ],
        ]);
    }
}
