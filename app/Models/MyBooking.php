<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyBooking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'room_id',
        'total_price',
        'check_in',
        'check_out',
        'tour_type',
        'status',
        'receipt',
        'admin_note',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'room_id', 'room_id')
            ->where('user_id', $this->user_id)
            ->select(['rate', 'comment']);
    }

    public static function getBookingYears()
    {
        return self::select(DB::raw('DISTINCT YEAR(check_in) as year'))
            ->orderByDesc('year')
            ->pluck('year');
    }

    /**
     * Summary report (counts and totals)
     */
      /**
     * Summary reports
     */
    public static function getReports($type = 'daily', $year = null)
    {
        $year = $year ?? now()->year;
        $query = self::whereYear('check_in', $year);

        $aggregates = [
            DB::raw("COUNT(*) AS bookings"),
            DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS checkIns"),
            DB::raw("SUM(total_price) AS payments"),
            DB::raw("MIN(check_in) AS check_in"),
            DB::raw("MAX(check_out) AS check_out"),
        ];

        switch ($type) {
            case 'daily':
                return $query->select(
                    DB::raw('CAST(check_in AS DATE) AS period'),
                    ...$aggregates
                )
                ->groupBy(DB::raw('CAST(check_in AS DATE)'))
                ->orderBy('period', 'desc')
                ->get();

            case 'weekly':
                return $query->select(
                    DB::raw("DATEADD(WEEK, DATEDIFF(WEEK, 0, check_in), 0) AS week_start"),
                    DB::raw("DATEADD(DAY, 6, DATEADD(WEEK, DATEDIFF(WEEK, 0, check_in), 0)) AS week_end"),
                    ...$aggregates
                )
                ->groupBy(DB::raw("DATEADD(WEEK, DATEDIFF(WEEK, 0, check_in), 0)"))
                ->orderBy(DB::raw("DATEADD(WEEK, DATEDIFF(WEEK, 0, check_in), 0)"), 'desc')
                ->get();

            case 'yearly':
                return $query->select(
                    DB::raw('YEAR(check_in) AS year'),
                    ...$aggregates
                )
                ->groupBy(DB::raw('YEAR(check_in)'))
                ->orderBy('year', 'desc')
                ->get();
        }

        return collect();
    }

    /**
     * Detailed report data with user/room info
     */
    public static function getReportDetails($type = 'daily', $year = null, $month = null, $checkIn = null, $checkOut = null)
    {
        $year = $year ?? now()->year;
        $query = self::with(['user', 'room'])->whereYear('check_in', $year);

        switch ($type) {
            case 'daily':
                if ($checkIn) {
                    $query->whereDate('check_in', $checkIn);
                }
                break;

            case 'weekly':
                if ($checkIn && $checkOut) {
                    $query->whereBetween('check_in', [$checkIn, $checkOut]);
                }
                break;

            case 'yearly':
                if ($month) {
                    $query->whereMonth('check_in', $month);
                }
                break;
        }

        return $query->orderBy('check_in', 'asc')->get();
    }

    /**
     * Weekly summary with nested bookings
     */
    public static function getWeeklySummaryWithDetails($year = null)
    {
        $year = $year ?? now()->year;

        $weeks = self::getReports('weekly', $year);

        return $weeks->map(function ($week) use ($year) {
            $bookings = self::getReportDetails(
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
                'payments' => $week->payments,
                'bookings_detail' => $bookings->map(function ($b) {
                    return [
                        'id' => $b->id,
                        'user' => $b->user ? $b->user->first_name . ' ' . $b->user->last_name : null,
                        'room' => $b->room ? $b->room->room_name : null,
                        'status' => $b->status,
                        'check_in' => $b->check_in,
                        'check_out' => $b->check_out,
                        'total_price' => $b->total_price,
                    ];
                }),
            ];
        });
    }

}
