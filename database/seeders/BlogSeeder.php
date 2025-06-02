<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
      public function run()
    {
        Blog::create([
            'judul' => 'Sample Blog Title',
            'slug' => 'sample-blog-title',
            'desc' => 'This is the description of the sample blog post.',
            'image' => 'sample.jpg', // Make sure this file exists in public/storage/blogs/
        ]);
    }
}

