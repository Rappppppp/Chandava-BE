<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccommodationType;
use App\Http\Resources\V1\AccommodationTypeResource;
use App\Filters\AccommodationTypeFilter;
use App\Http\Requests\V1\UpdateAccommodationTypeRequest;
use App\Http\Requests\V1\StoreAccommodationTypeRequest;


class AccommodationTypeController extends Controller
{
    public function index(Request $request)
    {
        //
        $filters = new AccommodationTypeFilter($request);
        $accommodationTypes = $filters->apply(AccommodationType::query())->get();
        return AccommodationTypeResource::collection($accommodationTypes);
    }

    public function store(StoreAccommodationTypeRequest $request)
    {
        $accommodationType = AccommodationType::create($request->only('accommodation_type_name'));

        return response()->json([
            'message' => 'Accommodation type created successfully',
            'accommodation_type' => new AccommodationTypeResource($accommodationType),
        ], 201);
    }

    public function show(AccommodationType $accommodationType)
    {
        return new AccommodationTypeResource($accommodationType);
    }

    public function update(UpdateAccommodationTypeRequest $request, AccommodationType $accommodationType)
    {
        $accommodationType->update($request->only('accommodation_type_name'));
        return response()->json([
            'message' => 'Accommodation type updated successfully',
            'accommodation_type' => new AccommodationTypeResource($accommodationType),
        ], 200);
    }
}
