<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

abstract class PanelController extends Controller
{
    protected function account(Request $request): Account
    {
        return $request->user()->account()->with('user')->firstOrFail();
    }
}
