<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();

            // For linking to a specific user (optional if you have authentication)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            // OpenAI-related identifiers
            $table->string('thread_id')->nullable();       // OpenAI thread ID
            $table->string('run_id')->nullable();          // OpenAI run ID
            $table->string('assistant_id')->nullable();    // OpenAI assistant ID

            // Message details
            $table->text('user_message');                  // What the user said
            $table->longText('assistant_reply')->nullable(); // What GPT replied

            // Optional metadata
            $table->string('status')->default('completed'); // completed, failed, pending
            $table->json('raw_response')->nullable();       // for debugging/storing JSON

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_conversations');
    }
};
