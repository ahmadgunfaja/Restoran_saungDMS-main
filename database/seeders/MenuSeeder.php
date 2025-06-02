<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $categoryIds = DB::table('categories')->pluck('id', 'name'); // ['Makanan' => 1, ...]

    DB::table('menus')->insert([
      [
        'name' => 'Nasi Goreng Spesial',
        'description' => 'Nasi goreng dengan telur, ayam, dan kerupuk.',
        'image' => 'nasi_goreng.jpg',
        'category_id' => $categoryIds['Makanan'] ?? null,
        'price' => 25000,
        'tingkatpedas' => 'Sedang',
        'created_at' => now(),
        'updated_at' => now(),

      ],
      [
        'name' => 'Mie Ayam Bakso',
        'description' => 'Mie ayam dengan tambahan bakso sapi dan sayuran segar.',
        'image' => 'mie_ayam_bakso.jpg',
        'category_id' => $categoryIds['Makanan'] ?? null,
        'price' => 22000,
        'tingkatpedas' => 'Rendah',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Sate Ayam',
        'description' => 'Sate ayam bumbu kacang, disajikan dengan lontong.',
        'image' => 'sate_ayam.jpg',
        'category_id' => $categoryIds['Makanan'] ?? null,
        'price' => 20000,
        'tingkatpedas' => 'Sedang',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Es Teh Manis',
        'description' => 'Minuman es teh manis segar.',
        'image' => 'es_teh_manis.jpg',
        'category_id' => $categoryIds['Minuman'] ?? null,
        'price' => 6000,
        'tingkatpedas' => null,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Jus Alpukat',
        'description' => 'Jus alpukat segar dengan susu coklat.',
        'image' => 'jus_alpukat.jpg',
        'category_id' => $categoryIds['Minuman'] ?? null,
        'price' => 15000,
        'tingkatpedas' => null,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Kopi Susu',
        'description' => 'Kopi hitam dengan susu kental manis.',
        'image' => 'kopi_susu.jpg',
        'category_id' => $categoryIds['Minuman'] ?? null,
        'price' => 12000,
        'tingkatpedas' => null,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Keripik Singkong',
        'description' => 'Keripik singkong renyah, cocok untuk cemilan.',
        'image' => 'keripik_singkong.jpg',
        'category_id' => $categoryIds['Cemilan'] ?? null,
        'price' => 8000,
        'tingkatpedas' => 'Rendah',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Pisang Goreng',
        'description' => 'Pisang goreng manis, disajikan hangat.',
        'image' => 'pisang_goreng.jpg',
        'category_id' => $categoryIds['Cemilan'] ?? null,
        'price' => 10000,
        'tingkatpedas' => null,
        'created_at' => now(),
        'updated_at' => now(),
      ]
    ]);
  }
}
