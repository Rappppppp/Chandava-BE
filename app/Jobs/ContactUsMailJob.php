<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class ContactUsMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $message = "
                From: {$this->data->first_name} {$this->data->last_name} ({$this->data->email})
                Message: {$this->data->message}
            ";

            Mail::raw($message, function ($message) {
                $message->to('chandavalakeresortresto@gmail.com', 'Chandava')
                    ->subject($this->data->subject);
            });
        } catch (\Exception $e) {
            info('[Error] Mail Job: ' . $e->getMessage());
        }
    }
}
