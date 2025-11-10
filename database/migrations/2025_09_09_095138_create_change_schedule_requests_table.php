<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('change_schedule_requests', function (Blueprint $table) {
            $table->id();

            // ✅ Keep cascade for user
            $table->foreignId('user_id')
                ->constrained('users', 'id')
                ->onDelete('cascade');

            // ✅ booking_id: NO ACTION (default) to avoid multiple cascade paths
            $table->foreignId('booking_id')
                ->constrained('bookings', 'id')
                ->onDelete('no action');

            $table->string('status')->default('pending');
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_schedule_requests');
    }
};
