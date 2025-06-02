<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'payment_response' => 'array',
        'amount_received' => 'decimal:2', // Ensure amount received is cast to decimal
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
