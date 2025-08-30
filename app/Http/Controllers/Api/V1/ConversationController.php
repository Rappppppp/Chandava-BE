<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    // Get all conversations for a user
    public function index($user_id)
    {
        $conversations = Conversation::whereHas(
            'users',
            fn($q) =>
            $q->where('user_id', $user_id)
        )->with('users')->latest()->get();

        return response()->json($conversations);
    }

    // Create a new conversation (or return existing)
    public function store(Request $request)
    {
        // Validate required fields
        $request->validate([
            'user_id' => 'required|integer',
            'receiver_id' => 'required|integer',
            'title' => 'required|string|max:255',
        ]);

        // Check if a conversation with the same title already exists
        $existingConversation = Conversation::where('title', $request->title)->first();

        if ($existingConversation) {
            return response()->json([
                'message' => 'Conversation with this title already exists.',
                'conversation' => $existingConversation->load('users'),
            ]);
        }

        // Always create a new conversation with this title
        $conversation = Conversation::create([
            'title' => $request->title,
        ]);

        // Attach both users
        $conversation->users()->attach([$request->user_id, $request->receiver_id]);

        return response()->json($conversation->load('users'));
    }


    // Get messages in a conversation
    public function messages(Conversation $conversation)
    {
        return response()->json(
            $conversation->messages()->with('user')->orderBy('created_at', 'asc')->get()
        );
    }

    // Send a message in a conversation
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'body' => 'required|string',
        ]);

        $message = $conversation->messages()->create([
            'user_id' => $request->user_id,
            'body' => $request->body,
        ]);

        return response()->json($message->load('user'));
    }
}
