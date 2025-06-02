<?php

namespace App\Services\Admin;

use App\Models\PaymentTransaction;

class PaymentTransactionService
{
  public function getPaginatedTransactions($perPage = 20)
  {
    return PaymentTransaction::with('order')
      ->orderBy('created_at', 'asc')
      ->paginate($perPage);
  }
}
