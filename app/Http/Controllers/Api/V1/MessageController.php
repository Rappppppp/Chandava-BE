<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'user_id' => 'required|exists:users,id',
            'body' => 'required|string',
        ]);

        $conversation = Conversation::with('users')->findOrFail($validated['conversation_id']);

        // Check if the user is part of the conversation
        if (!$conversation->users->contains($validated['user_id'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'conversation_id' => $validated['conversation_id'],
            'user_id' => $validated['user_id'],
            'body' => $validated['body'],
        ]);

        return response()->json($message->load('user'), 201);
    }
}
