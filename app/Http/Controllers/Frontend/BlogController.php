<?php
namespace App\Http\Controllers\Frontend;

use App\Models\Blog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class BlogController extends Controller
{
    // public function index()
    // {
    //     // Fetch all blog posts and pass them to the view
    //     $artikels = Blog::latest()->get();
    //     return view('welcome', compact('artikels')); // Display on the home page
    // }

    public function show($slug)
    {
        // Find the blog post by slug
        $artikel = Blog::where('slug', $slug)->firstOrFail();
        return view('blog.detail', compact('artikel')); // Blog detail page view
    }
    
}
