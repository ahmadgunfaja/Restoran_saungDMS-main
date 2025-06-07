<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserDemoSeeder extends Seeder
{
  public function run()
  {
    User::create([
      'name' => 'Demo User',
      'email' => 'demo@gmail.com',
      'email_verified_at' => now(),
      'password' => Hash::make('password'), // password
      'remember_token' => Str::random(10),
      'is_admin' => 0
    ]);
  }
}
