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
        Schema::create('feedback_images', function (Blueprint $table) {
            $table->id(); // creates an auto-incrementing primary key "id"

            $table->foreignId('feedback_id') 
                ->constrained('feedbacks')
                ->onDelete('cascade');    

            $table->string('image'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_images');
    }
};
