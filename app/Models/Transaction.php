<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
  use HasFactory;

  protected $fillable = [
    'order_id',
    'user_id',
    'amount',
    'payment_type',
    'transaction_status',
    'snap_token',
    'payment_response',
  ];

  protected $casts = [
    'payment_response' => 'array',
  ];

  public function order()
  {
    return $this->belongsTo(Order::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
