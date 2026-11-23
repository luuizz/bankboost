<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function show(Request $request): AccountResource
    {
        return new AccountResource($request->user()->account()->with('user')->firstOrFail());
    }
}
