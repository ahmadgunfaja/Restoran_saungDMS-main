<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    public function index()
    {
        return view('admin.blog.index', [
            'artikels' => Blog::all()
        ]);
    }

    public function create()
    {
        return view('admin.blog.create');
    }

        public function store(Request $request)
    {
        $rules = [
            'judul' => 'required',
            'image' => 'required|max:1000|mimes:jpg,jpeg,png,webp',
            'desc' => 'required|min:20',
        ];

        $messages = [
            'judul.required' => 'Judul wajib diisi!',
            'image.required' => 'Image wajib diisi!',
            'desc.required' => 'Deskripsi wajib diisi!',
        ];

        $this->validate($request, $rules, $messages);

        // Handle image upload using Storage
        $fileName = time() . '.' . $request->image->getClientOriginalExtension();
        $filePath = $request->file('image')->storeAs('public/blogs', $fileName); // Store the image in storage/app/public/blogs

        // Create the blog post
        Blog::create([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul, '-'),
            'image' => $fileName,
            'desc' => $request->desc,  // Assuming plain text description
        ]);

        return redirect(route('admin.blog.index'))->with('success', 'Blog berhasil disimpan!');
    }

    public function edit($id)
    {
        // Find the blog post by ID
        $blog = Blog::find($id);

        // If the blog post is not found, redirect with an error
        if (!$blog) {
            return redirect(route('admin.blog.index'))->with('error', 'Blog not found');
        }

        // Pass the blog data to the view
        return view('admin.blog.edit', compact('blog'));
    }


        public function update(Request $request, $id)
    {
        // Find the blog post by ID
        $artikel = Blog::find($id);

        if ($artikel) {
            // Validate the request
            $request->validate([
                'judul' => 'required',
                'desc' => 'required|min:20',
                'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:1000',
            ]);

            // Handle image upload if a new image is provided
            $fileName = $artikel->image; // Keep the old image by default
            if ($request->hasFile('image')) {
                // If there is a new image, delete the old image first
                if (\File::exists(public_path('storage/blogs/' . $artikel->image))) {
                    \File::delete(public_path('storage/blogs/' . $artikel->image));
                }

                // Store the new image and update the file name
                $fileName = time() . '.' . $request->image->getClientOriginalExtension();
                $filePath = $request->file('image')->storeAs('public/blogs', $fileName);
            }

            // Update the blog post
            $artikel->update([
                'judul' => $request->judul,
                'image' => $fileName,
                'desc' => $request->desc,
            ]);

            // Redirect to the blog index page
            return redirect(route('admin.blog.index'))->with('success', 'Data berhasil diupdate');
        } else {
            // If the blog post is not found, return an error
            return redirect(route('admin.blog.index'))->with('error', 'Blog tidak ditemukan');
        }
    }


        public function destroy($id)
    {
        // Find the blog post by ID
        $artikel = Blog::find($id);

        // Check if the blog post exists
        if ($artikel) {
            // If the image exists in the storage, delete it
            if (\File::exists(public_path('storage/blogs/' . $artikel->image))) {
                \File::delete(public_path('storage/blogs/' . $artikel->image));
            }

            // Delete the blog post from the database
            $artikel->delete();

            // Redirect to the blog index page with a success message
            return redirect(route('admin.blog.index'))->with('success', 'Data berhasil dihapus');
        } else {
            // If the blog post does not exist, redirect with an error message
            return redirect(route('admin.blog.index'))->with('error', 'Data tidak ditemukan');
        }
    }
        
    
}