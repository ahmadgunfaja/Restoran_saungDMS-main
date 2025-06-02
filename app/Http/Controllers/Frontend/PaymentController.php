<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function qrisPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Pastikan order ditemukan
        if (!$order) {
            return redirect()->route('menus.index')->with('error', 'Order not found.');
        }

        // Data yang akan digunakan dalam QRIS (misalnya URL untuk pembayaran)
        $qrisData = "https://paymentgateway.com/qris?order_id={$order->id}&amount={$order->total_price}";

        // Membuat QR Code dengan library Endroid
        $qrCode = new QrCode($qrisData);

        // Menentukan path penyimpanan file gambar QRIS di folder public storage
        $qrisImagePath = storage_path('app/public/qris_images/qris_' . $orderId . '.png');

        // Pastikan folder qris_images sudah ada
        $qrisDir = storage_path('app/public/qris_images');
        if (!file_exists($qrisDir)) {
            mkdir($qrisDir, 0777, true);
        }

        // Membuat instance PngWriter
        $writer = new PngWriter();

        // Menulis QR code ke dalam string
        $qrCodeImageString = $writer->write($qrCode); // returns a string

        // Menyimpan gambar QRIS ke file menggunakan file_put_contents
        file_put_contents($qrisImagePath, $qrCodeImageString);

        // Mengembalikan path gambar ke view
        return view('menus.qrisPayment', compact('order', 'qrisImagePath'));
    }

    // Metode lainnya tetap sama
    public function checkPaymentStatus()
    {
        $paymentStatus = 'pending'; // Setel status sesuai dengan status pembayaran sebenarnya
        return view('menus.paymentStatus', compact('paymentStatus'));
    }

    // Metode untuk mengupload screenshot QRIS
    public function uploadQrisScreenshot(Request $request)
    {
        // Validasi file screenshot QRIS
        $request->validate([
            'qris_screenshot' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        // Upload screenshot QRIS
        $path = $request->file('qris_screenshot')->store('qris_screenshots', 'public');

        // Ambil order yang sesuai dengan session
        $order = Order::findOrFail(session('order_id')); // Pastikan order_id disimpan di session

        // Update status pembayaran dan simpan screenshot
        $order->update([
            'qris_screenshot' => $path,
            'payment_status' => 'completed', // Set status menjadi 'completed'
        ]);

        // Redirect ke halaman status pembayaran
        return redirect()->route('payment.status')->with('success', 'Payment verification successful!');
    }

    // Validasi pembayaran oleh admin
    public function validatePayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Ubah status pembayaran menjadi 'completed' jika validasi berhasil
        $order->update([
            'payment_status' => 'completed',
        ]);

        // Kembali ke halaman dengan pesan sukses
        return redirect()->route('admin.orders')->with('success', 'Payment successfully validated!');
    }

    // Menampilkan semua pesanan untuk admin
    public function showAdminOrders()
    {
        $orders = Order::all();  // Ambil semua pesanan
        return view('admin.orders', compact('orders'));
    }

    // Proses pembayaran order dan generate Tripay Transaction
    public function payOrder($orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        $user = Auth::user();

        // Cek jika sudah ada transaksi untuk order ini
        $transaction = \App\Models\Transaction::where('order_id', $order->id)->first();
        if (!$transaction) {
            // Buat transaksi ke Tripay
            $orderItems = $order->orderItems->map(function ($item) {
                return [
                    'sku' => $item->sku,
                    'name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                ];
            })->toArray();
            // Ambil channel pembayaran dari session jika ada
            $method = session('payment_channel', 'BRIVA');
            // Ambil info channel dari TripayService
            $channelsPayment = app(\App\Services\TripayService::class)->getPaymentChannels();
            $channelInfo = null;
            if (isset($channelsPayment['data']) && is_array($channelsPayment['data'])) {
                foreach ($channelsPayment['data'] as $ch) {
                    if ($ch['code'] === $method) {
                        $channelInfo = $ch;
                        break;
                    }
                }
            }
            $baseAmount = $order->total_price;
            $totalWithFee = $baseAmount;
            if ($channelInfo) {
                $totalWithFee = $this->calculateTotalWithFee($baseAmount, $channelInfo);
            }
            $merchantRef = 'ORDER-' . $order->id . '-' . time();
            $payload = [
                'method'         => $method, // channel pembayaran dari session
                'merchant_ref'   => $merchantRef,
                'amount'         => $totalWithFee,
                'customer_name'  => $order->customer_name,
                'customer_email' => $order->email,
                'customer_phone' => $order->phone,
                'order_items'    => $orderItems,
                'return_url'     => route('payment.success'),
                'callback_url'   => url('/payment/tripay-callback'),
                'expired_time'   => now()->addHours(24)->timestamp,
            ];
            // Generate signature sesuai Tripay
            $tripay = app(\App\Services\TripayService::class);
            $payload['signature'] = hash_hmac('sha256', $merchantRef . $totalWithFee . config('services.tripay.merchant_code'), config('services.tripay.private_key'));
            $result = $tripay->createTransaction($payload);
            $reference = $result['data']['reference'] ?? null;
            $payment_url = $result['data']['checkout_url'] ?? null;
            // Simpan transaksi ke tabel payment_transactions
            DB::table('payment_transactions')->insert([
                'order_id' => $order->id,
                'merchant_ref' => $merchantRef,
                'payment_channel' => $method,
                'amount' => $totalWithFee,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->email,
                'customer_phone' => $order->phone,
                'order_items' => json_encode($orderItems),
                'callback_url' => url('/payment/tripay-callback'),
                'return_url' => route('payment.success'),
                'expired_time' => now()->addHours(24)->timestamp,
                'signature' => $payload['signature'],
                'status' => 'pending',
                'payment_response' => json_encode($result),
                'created_at' => now(),
                'updated_at' => now(),
                'fee_flat' => $channelInfo['fee_merchant']['flat'] ?? 0,
                'fee_percent' => $channelInfo['fee_merchant']['percent'] ?? 0,
            ]);
            $transaction = (object)[
                'snap_token' => $reference,
                'payment_response' => json_encode($result),
            ];
        } else {
            $reference = $transaction->snap_token;
            $result = json_decode($transaction->payment_response, true);
            $payment_url = $result['data']['checkout_url'] ?? null;
        }
        return view('payment.tripay', compact('order', 'transaction', 'reference', 'payment_url'));
    }

    // Handle notifikasi/callback dari Midtrans
    public function midtransCallback(\Illuminate\Http\Request $request)
    {
        $notif = json_decode($request->getContent());
        $transaction = \App\Models\Transaction::where('snap_token', $notif->order_id)->first();
        if ($transaction) {
            $transaction->transaction_status = $notif->transaction_status;
            $transaction->payment_type = $notif->payment_type ?? null;
            $transaction->payment_response = json_encode($notif);
            $transaction->save();

            // Update status order jika pembayaran sukses
            if (in_array($notif->transaction_status, ['settlement', 'capture'])) {
                $order = $transaction->order;
                if ($order) {
                    $order->payment_status = 'paid';
                    $order->save();
                }
            }
        }
        return response()->json(['status' => 'ok']);
    }

    // Halaman sukses
    public function success()
    {
        return view('payment.success');
    }

    // Halaman gagal
    public function failed()
    {
        return view('payment.failed');
    }

    public function tripayChannels()
    {
        $channels = app(\App\Services\TripayService::class)->getPaymentChannels();
        return response()->json($channels);
    }

    // Handle notifikasi/callback dari Tripay
    public function tripayCallback(Request $request)
    {
        $json = $request->getContent();
        $signature = $request->header('X-Callback-Signature');
        $tripay = app(\App\Services\TripayService::class);
        $validSignature = $tripay->makeSignature($json);

        if ($signature !== $validSignature) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
        }

        $data = json_decode($json, true);
        $reference = $data['reference'] ?? null;
        $status = $data['status'] ?? null;

        $transaction = \App\Models\Transaction::where('snap_token', $reference)->first();
        if ($transaction) {
            $transaction->transaction_status = $status;
            $transaction->payment_response = $json;
            $transaction->save();
            // Update status order jika pembayaran sukses
            if ($status === 'PAID') {
                $order = $transaction->order;
                if ($order) {
                    $order->payment_status = 'paid';
                    $order->save();
                }
            }
        }
        return response()->json(['success' => true]);
    }



    // Menampilkan halaman QRIS payment (tanpa orderId, fallback ke order dari session atau redirect)
    public function processQrisPayment(Request $request)
    {
        // Coba ambil order_id dari session
        $orderId = session('order_id');
        dd($orderId);
        if (!$orderId) {
            return redirect()->route('menus.index')->with('error', 'Order tidak ditemukan.');
        }
        // Redirect ke qrisPayment dengan orderId
        return redirect()->route('payment.qris.order', ['orderId' => $orderId]);
    }

    /**
     * Hitung total harga order + fee channel pembayaran Tripay
     * @param float $baseAmount
     * @param array $channelInfo
     * @return float
     */
    private function calculateTotalWithFee($baseAmount, $channelInfo)
    {
        $flat = isset($channelInfo['fee_merchant']['flat']) ? floatval($channelInfo['fee_merchant']['flat']) : 0;
        $percent = isset($channelInfo['fee_merchant']['percent']) ? floatval($channelInfo['fee_merchant']['percent']) : 0;
        $fee = $flat + ($baseAmount * $percent / 100);
        return $baseAmount + $fee;
    }
}
