<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'phone',
        'email',
        'table_id',
        'total_price',
        'note',
        'payment_status',
        'qris_screenshot',
        'reservation_id',
        'menu_items'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);  // Relasi ke tabel 'Table'
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class)->withPivot('quantity', 'price');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function paymentTransaction()
    {
        return $this->hasOne(PaymentTransaction::class, 'order_id', 'id')->latestOfMany();
    }
}
