<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    // Menambahkan category_id dan spiciness ke dalam $fillable
    protected $fillable = ['name', 'price', 'description', 'image', 'category_id', 'spiciness'];

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity', 'price');  // Relasi dengan tabel 'Order'
    }
    
}
