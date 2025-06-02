<?php

namespace App\Services;

use Midtrans\Snap;
use Midtrans\Config;

class MidtransService
{
  public function __construct()
  {
    Config::$serverKey = config('services.midtrans.server_key');
    Config::$isProduction = config('services.midtrans.is_production', false);
    Config::$isSanitized = true;
    Config::$is3ds = true;
  }

  public function createSnapToken($order, $user)
  {
    $params = [
      'transaction_details' => [
        'order_id' => $order->id . '-' . time(),
        'gross_amount' => $order->total,
      ],
      'customer_details' => [
        'first_name' => $user->name,
        'email' => $user->email,
      ],
    ];
    return Snap::getSnapToken($params);
  }
}
