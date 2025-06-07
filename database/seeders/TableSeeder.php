<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::table('tables')->insert([
      [
        'name' => 'Meja 1',
        'guest_number' => 4,
        'status' => 'available',
        'location' => 'inside',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Meja 2',
        'guest_number' => 2,
        'status' => 'available',
        'location' => 'outside',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Meja 3',
        'guest_number' => 6,
        'status' => 'available',
        'location' => 'front',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Meja 4',
        'guest_number' => 8,
        'status' => 'available',
        'location' => 'inside',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Meja 10',
        'guest_number' => 12,
        'status' => 'available',
        'location' => 'inside',
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}
