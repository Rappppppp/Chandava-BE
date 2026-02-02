<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;

class MailTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mail-test';

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
        Mail::raw('This is a test from Laravel API :D', function ($message) {
            $message->to('chandavalakeresortresto@gmail.com', 'Chandava')
                ->subject('Rap Chandava Test');
        });
    }
}
