<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreFeedbackRequest;
use App\Http\Resources\V1\FeedbackResource;
use App\Models\Feedback;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedback = Feedback::all();
        return response()->json(FeedbackResource::collection($feedback));
    }
    public function store(StoreFeedbackRequest $request)
    {
        $feedback = DB::transaction(function () use ($request) {
            // Create feedback
            $feedback = Feedback::create($request->validated());

            // Store feedback images (array of strings)
            if ($request->has('images')) {
                foreach ($request->images as $imageName) {
                    $feedback->images()->create([
                        'image' => $imageName,
                    ]);
                }
            }

            return $feedback;
        });

        return response()->json([
            'message' => 'Feedback sent successfully!',
            'feedback' => new FeedbackResource($feedback->load('images')), // include images in response
        ], 201);
    }

    public function storeResponse(Request $request)
    {
        $validated = $request->validate([
            'feedback_id' => ['required', 'exists:feedbacks,id'],
            'response' => ['required', 'string'],
        ]);

        $feedback = Feedback::findOrFail($validated['feedback_id']);
        $feedback->response()->create([
            'feedback_id' => $feedback->id,
            'response' => $validated['response'],
        ]);

        return response()->json([
            'message' => 'Response sent successfully!',
        ], status: 200);
    }
}
