<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use Illuminate\Http\Request;
use OpenAI;

class AssistantController extends Controller
{
    public function handle(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $assistantId = env('OPENAI_ASSISTANT_ID');
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        // Create a new thread
        $thread = $client->threads()->create([]);

        // Add a user message to the thread
        $client->threads()->messages()->create($thread->id, [
            'role' => 'user',
            'content' => $validated['message'],
        ]);

        // Run the assistant
        $run = $client->threads()->runs()->create($thread->id, [
            'assistant_id' => $assistantId,
        ]);

        // Poll until the run is completed
        do {
            sleep(1);
            $runStatus = $client->threads()->runs()->retrieve($thread->id, $run->id);
        } while ($runStatus->status !== 'completed' && $runStatus->status !== 'failed');

        // Retrieve the latest message
        $messages = $client->threads()->messages()->list($thread->id);
        $latest = collect($messages->data)->first();

        // Extract only text-based content (filter out reasoning traces)
        $replyText = collect($latest->content)
            ->where('type', 'text')
            ->pluck('text.value')
            ->implode("\n");

        $cleanedReply = trim($this->extractResponseText($replyText), " \t\n\r\0\x0B\"'") ?: 'No response received.';

        // Save to database
        ChatbotConversation::create([
            'user_id' => auth()->id(), // optional, if authentication is active
            'assistant_id' => $assistantId,
            'thread_id' => $thread->id,
            'run_id' => $run->id,
            'user_message' => $validated['message'],
            'assistant_reply' => $cleanedReply,
            'status' => $runStatus->status,
            'raw_response' => json_encode($messages),
        ]);

        return response()->json([
            'reply' => $cleanedReply,
        ]);
    }

     /**
     * Extracts only the assistant's clean response text.
     */
    private function extractResponseText(string $text): string
    {
        // If "Response:" exists, take everything after it
        if (preg_match('/-?\s*Response:\s*(.*)/is', $text, $matches)) {
            return trim($matches[1]);
        }

        // Otherwise, return the original text as fallback
        return trim($text);
    }
}
