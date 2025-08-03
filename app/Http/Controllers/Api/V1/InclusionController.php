<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inclusion;
use App\Http\Resources\V1\InclusionResource;
use App\Filters\InclusionFilter;
use App\Http\Requests\V1\StoreInclusionRequest;
use App\Http\Requests\V1\UpdateInclusionRequest;

class InclusionController extends Controller
{
    //
    public function index(Request $request)
    {
        //
        $filter = new InclusionFilter($request);
        $inclusions = $filter->apply(Inclusion::query())->get();
        return InclusionResource::collection($inclusions);
    }

    public function show(Inclusion $inclusion)
    {
        return new InclusionResource($inclusion);
    }

    public function store(StoreInclusionRequest $request)
    {
        $inclusion = Inclusion::create($request->only('inclusion_name'));

        return response()->json([
            'message' => 'Inclusion created successfully',
            'inclusion' => new InclusionResource($inclusion),
        ], 201);
    }

    public function update(UpdateInclusionRequest $request, Inclusion $inclusion)
    {
        $inclusion->update($request->only('inclusion_name'));
        return response()->json([
            'message' => 'Inclusion updated successfully',
            'inclusion' => new InclusionResource($inclusion),
        ], 200);
    }

    public function destroy($id)
    {
        $inclusion = Inclusion::find($id);

        if (!$inclusion) {
            return response()->json(['message' => 'Inclusion not found.'], 404);
        }

        $inclusion->delete();

        return response()->json(['message' => 'Inclusion deleted successfully.']);
    }
}
