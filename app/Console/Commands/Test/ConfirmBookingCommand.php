<?php

namespace App\Console\Commands\Test;

use App\Mail\ConfirmedBookingMail;
use App\Models\MyBooking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Mail;

class ConfirmBookingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:confirm-booking-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $booking = MyBooking::inRandomOrder()->first();
        Mail::to(config('app.env') === 'production' ? $booking->user->email : 'inputemail')
            ->send(new ConfirmedBookingMail(mailData: [
                'receipt' => $booking->receipt,
                'created_at' => Carbon::parse($booking->created_at)->format('F j, Y'),
                'first_name' => $booking->user->first_name,
                'tour_type' => $booking->tour_type,
                'room_name' => $booking->room->accommodationType->accommodation_type_name,
                'guests_count' => $booking->room->accommodationType->max_guests,
                'amount' => $booking->total_price,
            ]));

        $this->info('Test Confirm Booking Done!');
    }
}
