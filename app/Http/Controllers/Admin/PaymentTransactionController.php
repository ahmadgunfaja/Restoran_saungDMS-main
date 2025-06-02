<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\PaymentTransactionService;
use Illuminate\Http\Request;

class PaymentTransactionController extends Controller
{
  public function index(Request $request)
  {
    $service = new PaymentTransactionService();
    $transactions = $service->getPaginatedTransactions(20);
    return view('admin.transactions.index', compact('transactions'));
  }
}
