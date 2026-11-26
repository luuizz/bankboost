<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('site.pages.home', [
            'posts' => Post::published()->latest('published_at')->limit(3)->get(),
        ]);
    }
}
