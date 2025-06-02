<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\TripayService;
use Endroid\QrCode\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TripayPaymentService
{
  /**
   * Handle Tripay payment for an order. If $order is provided, use it. Otherwise, create a new order.
   * @param array $validated
   * @param array $cart
   * @param Order|null $order
   * @return array
   */
  public function handleOrder(array $validated, array $cart, Order $order = null)
  {
    $tripay = new TripayService();
    $subtotal = 0;
    $orderItems = [];
    foreach ($cart as $menuId => $item) {
      $orderItems[] = [
        'menu_id' => $menuId,
        'sku' => 'DMS' . $item['name'],
        'name' => $item['name'],
        'price' => $item['price'],
        'quantity' => $item['quantity'],
      ];
      $subtotal += $item['price'] * $item['quantity'];
    }
    $tax = $subtotal * 0.10;
    $feeFlat = $validated['fee_flat'] ?? 0;
    $feePercent = $validated['fee_percent'] ?? 0;
    $totalFee = $feeFlat + ($feePercent / 100 * ($subtotal + $tax));
    $totalPrice = $subtotal + $tax + $totalFee;
    $amount = $subtotal + $tax;
    if ($tax > 0) {
      $orderItems[] = [
        'sku' => 'TAX10',
        'name' => 'Pajak 10%',
        'price' => round($tax),
        'quantity' => 1,
      ];
    }
    DB::beginTransaction();
    try {
      // Only create order if not provided
      if (!$order) {
        $order = Order::create([
          'customer_name' => $validated['name'],
          'phone' => $validated['phone'],
          'email' => $validated['email'],
          'table_id' => $validated['table_id'],
          'total_price' => $totalPrice,
          'amount' => $amount,
          'tax' => $tax,
          'note' => $validated['note'] ?? null,
          'payment_status' => 'pending',
          'qris_screenshot' => null,
        ]);
      }
      // Hapus order_items lama jika ada (agar tidak double insert)
      $order->orderItems()->delete();
      // Insert order_items menu
      foreach ($cart as $menuId => $item) {
        $order->orderItems()->create([
          'menu_id' => $menuId,
          'sku' => 'DMS' . $item['name'],
          'name' => $item['name'],
          'price' => $item['price'],
          'quantity' => $item['quantity'],
        ]);
      }
      // Insert order_item pajak jika ada
      if ($tax > 0) {
        $order->orderItems()->create([
          'menu_id' => null,
          'sku' => 'TAX10',
          'name' => 'Pajak 10%',
          'price' => round($tax),
          'quantity' => 1,
        ]);
      }
      $order->load(['table', 'orderItems', 'menus']);
      $merchant_ref = 'order-' . $order->id;
      $signature = $tripay->makeSignature($merchant_ref, $amount);
      $payload = [
        'method' => $validated['payment_channel'],
        'merchant_ref' => $merchant_ref,
        'amount' => $amount,
        'customer_name' => $validated['name'],
        'customer_email' => $validated['email'],
        'customer_phone' => $validated['phone'],
        'order_items' => $orderItems,
        'expired_time' => now()->addMinutes(30)->timestamp,
        'signature' => $signature,
        'callback_url' => url('/callback/tripay'),
      ];
      // Hapus null agar payload bersih
      $payload = array_filter($payload, function ($v) {
        return $v !== null;
      });
      $transaction = $tripay->createTransaction($payload);
      PaymentTransaction::create([
        'order_id' => $order->id,
        'merchant_ref' => $transaction['data']['merchant_ref'],
        'payment_channel' => $validated['payment_channel'],
        'customer_name' => $validated['name'],
        'customer_email' => $validated['email'],
        'customer_phone' => $validated['phone'],
        'order_items' => json_encode($orderItems),
        'signature' => $signature,
        'amount' => $transaction['data']['amount'],
        'amount_received' => $transaction['data']['amount_received'],
        'fee_merchant' => $transaction['data']['fee_merchant'],
        'fee_customer' => $transaction['data']['fee_customer'],
        'total_fee' => $totalFee,
        'payment_response' => json_encode($transaction),
        'expired_time' => $transaction['data']['expired_time'],
        'checkout_url' => $transaction['data']['checkout_url'],
        'callback_url' => $transaction['data']['callback_url'] ?? null,
        'return_url' => $transaction['data']['return_url'] ?? null,
        'qris_url' => $transaction['data']['qris_url'] ?? null,
        'status' => $transaction['data']['status'],
      ]);
      Log::info('Tripay transaction created', [
        'merchant_ref' => $transaction['data']['merchant_ref'],
        'amount' => $transaction['data']['amount'],
        'checkout_url' => $transaction['data']['checkout_url'],
      ]);
      $qrCode = new QrCode($transaction['data']['checkout_url']);
      if (!file_exists(public_path('qrcodes'))) {
        mkdir(public_path('qrcodes'), 0777, true);
      }
      $qrCodePath = public_path('qrcodes/' . $order->id . '.png');
      $writer = new \Endroid\QrCode\Writer\PngWriter();
      $result = $writer->write($qrCode);
      $result->saveToFile($qrCodePath);
      $order->amount = $transaction['data']['amount'];
      $order->qris_screenshot = 'qrcodes/' . $order->id . '.png';
      $order->save();
      DB::commit();
      return [
        'order' => $order,
        'transaction' => $transaction,
      ];
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Place order failed', ['error' => $e->getMessage()]);
      throw $e;
    }
  }

  public function handleCallback($data)
  {
    $merchantRef = $data->merchant_ref;
    $status = strtoupper((string) $data->status);
    $payment = PaymentTransaction::where('merchant_ref', $merchantRef)->first();
    if (!$payment) {
      Log::error('Tripay Callback: Payment transaction not found', ['merchant_ref' => $merchantRef]);
      return false;
    }
    $order = Order::find($payment->order_id);
    if (!$order) {
      Log::error('Tripay Callback: Order not found', ['order_id' => $payment->order_id]);
      return false;
    }
    switch ($status) {
      case 'PAID':
        $payment->update(['status' => 'PAID']);
        $order->update(['payment_status' => 'completed']);
        break;
      case 'EXPIRED':
        $payment->update(['status' => 'EXPIRED']);
        $order->update(['payment_status' => 'pending']);
        break;
      case 'FAILED':
        $payment->update(['status' => 'FAILED']);
        $order->update(['payment_status' => 'failed']);
        break;
      default:
        Log::warning('Tripay Callback: Unrecognized payment status', ['status' => $status]);
        return false;
    }
    Log::info('Tripay Callback: Callback sukses', ['order_id' => $order->id, 'status' => $status]);
    return true;
  }
}
