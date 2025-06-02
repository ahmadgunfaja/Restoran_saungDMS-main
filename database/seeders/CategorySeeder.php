<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::table('categories')->insert([
      [
        'name' => 'Makanan',
        'description' => 'Kategori untuk semua jenis makanan yang tersedia di menu.',
        'image' => 'makanan.jpg',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Minuman',
        'description' => 'Kategori untuk semua jenis minuman yang tersedia di menu.',
        'image' => 'minuman.jpg',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Cemilan',
        'description' => 'Kategori untuk semua jenis cemilan yang tersedia di menu.',
        'image' => 'cemilan.jpg',
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}
