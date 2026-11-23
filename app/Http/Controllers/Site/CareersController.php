<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use Illuminate\View\View;

class CareersController extends Controller
{
    public function __invoke(): View
    {
        return view('site.pages.careers', [
            'jobs' => JobOpening::active()->orderBy('team')->orderBy('title')->get()->groupBy('team'),
        ]);
    }
}
