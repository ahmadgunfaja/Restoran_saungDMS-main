<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Model;

class Koki extends Authenticatable
{
    use HasFactory;
    
    protected $guard = 'kokis';

     protected $fillable = [
        'name', 'email','no_hp', 'password', 'role','status',
    ];

     public function isKoki()
    {
        return $this->role === 'koki';
    }
}
