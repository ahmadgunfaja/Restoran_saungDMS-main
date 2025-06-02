<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Frontend\MenuController;
use App\Models\Order;
use App\Services\TripayPaymentService;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TripayController extends Controller
{
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string',
            'phone' => 'required|string',
            'table_id' => 'required|integer',
            'note' => 'nullable|string',
            'payment_channel' => 'required|string',
            'fee_flat' => 'nullable|numeric',
            'fee_percent' => 'nullable|numeric',
        ]);
        $cart = session()->get('cart', []);
        $service = new TripayPaymentService();
        try {
            $result = $service->handleOrder($validated, $cart);
            session()->put('customer_email', $validated['email']);
            $order = Order::with(['orderItems', 'menus', 'table'])->findOrFail($result['order']->id);
            $payment = $result['transaction'];
            // Pastikan $payment dalam bentuk array, jika masih string JSON, decode dulu
            if (is_string($payment)) {
                $payment = json_decode($payment, true);
            }
            $merchantCode = config('services.tripay.merchant_code');
            Log::info('Order placed successfully', [
                'order_id' => $order->id,
                'customer_email' => $validated['email'],
                'payment_channel' => $validated['payment_channel'],
                'info_payment' => $payment
            ]);
            return view('menus.summary', compact('order', 'payment', 'merchantCode'));
        } catch (\Exception $e) {
            return redirect()->route('menus.checkout')->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }
}
