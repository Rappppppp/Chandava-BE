<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreFeedbackRequest;
use App\Http\Resources\V1\FeedbackResource;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    //
    public function store(StoreFeedbackRequest $storeFeedbackRequest)
    {
        $feedback = Feedback::create($storeFeedbackRequest->validated());
        return response()->json([
            'message' => 'Feedback sent successfully!',
            'feedback' => new FeedbackResource($feedback),
        ], 201);
    }
}
