<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Blog;

class WelcomeController extends Controller
{
    public function index()
    {
        $artikels = Blog::latest()->get();
        $specials = Category::where('name', 'specials')->first();
        return view('welcome', compact('specials', 'artikels'));
    }
}
