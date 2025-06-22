<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\V1\UserResource;
use App\Filters\UserFilter;


class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        $filter = new UserFilter($request);
        $users = $filter->apply(User::query())->get();
        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return $user;
    }
}
