<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Filters\ContactUsFormFilter;
use App\Http\Resources\V1\ContactUsFormResource;
use App\Http\Requests\V1\StoreContactUsFormRequest;
use App\Jobs\ContactUsMailJob;
use App\Models\ContactUsForm;
use Illuminate\Http\Request;

class ContactUsFormController extends Controller
{
    //

    public function index(Request $request)
    {
        $filters = new ContactUsFormFilter($request);
        $contactUsForm = $filters->apply(ContactUsForm::query())->get();
        return response()->json(ContactUsFormResource::collection($contactUsForm));
    }

    public function store(StoreContactUsFormRequest $request)
    {
        $contact_us = ContactUsForm::create($request->validated());

        ContactUsMailJob::dispatch(data: $contact_us);

        return response()->json([
            'message' => 'Message sent successfully!',
            'contact_us' => new ContactUsFormResource($contact_us),
        ], 201);
    }
}
