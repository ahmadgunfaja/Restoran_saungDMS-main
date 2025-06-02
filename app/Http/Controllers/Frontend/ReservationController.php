<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\Menu;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use App\Rules\DateBetween;
use App\Rules\TimeBetween;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\TripayPaymentService;

class ReservationController extends Controller
{
    public function stepOne(Request $request)
    {
        $reservation = $request->session()->get('reservation');
        $min_date = Carbon::today();
        $max_date = Carbon::now()->addWeek();
        return view('reservations.step-one', compact('reservation', 'min_date', 'max_date'));
    }

    // storeStepOne - Pengalihan ke step-two
    public function storeStepOne(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email'],
            'res_date' => ['required', 'date', new DateBetween, new TimeBetween],
            'tel_number' => ['required'],
            'guest_number' => ['required'],
        ]);

        $reservation = $request->session()->get('reservation', new Reservation());
        $reservation->fill($validated);
        $request->session()->put('reservation', $reservation);

        return to_route('reservations.step-two');
    }

    // Step 2: Memilih Meja & Opsi Memesan Menu
    public function stepTwo(Request $request)
    {
        $reservation = $request->session()->get('reservation');
        if (!$reservation) {
            return redirect()->route('reservations.step-one')->with('error', 'Please complete the reservation form first.');
        }
        $res_table_ids = Reservation::where('res_date', $reservation->res_date)
            ->where('status', '!=', 'cancelled')
            ->pluck('table_id');
        $tables = Table::where('status', 'available')
            ->where('guest_number', '>=', $reservation->guest_number)
            ->whereNotIn('id', $res_table_ids)
            ->get();
        $menus = Menu::all();
        return view('reservations.step-two', compact('tables', 'reservation', 'menus'));
    }

    public function storeStepTwo(Request $request)
    {
        $validated = $request->validate([
            'table_id' => ['required'],
            'order_menu' => ['nullable', 'boolean'],
        ]);
        $reservation = $request->session()->get('reservation');
        $reservation->table_id = $validated['table_id'];
        $reservation->order_menu = $request->has('order_menu') ? 1 : 0;
        $request->session()->put('reservation', $reservation);
        if ($reservation->order_menu) {
            return to_route('reservations.step-three');
        }
        return to_route('reservations.step-four');
    }

    // Step 3: Memilih Menu
    public function stepThree(Request $request)
    {
        $reservation = $request->session()->get('reservation');
        if (!$reservation) {
            return redirect()->route('reservations.step-one')->with('error', 'Please complete the reservation form first.');
        }
        $menus = Menu::all();
        return view('reservations.step-three', compact('menus', 'reservation'));
    }

    public function storeStepThree(Request $request)
    {
        $validated = $request->validate([
            'menu_items' => ['required', 'array'],
        ]);
        $reservation = $request->session()->get('reservation');
        $reservation->menu_items = $validated['menu_items'];
        $request->session()->put('reservation', $reservation);
        return to_route('reservations.step-four');
    }

    // Step 4: Pembayaran
    public function stepFour(Request $request)
    {
        $reservation = $request->session()->get('reservation');
        if (!$reservation) {
            return redirect()->route('reservations.step-one')->with('error', 'Please complete the reservation form first.');
        }
        $order = null;
        $totalCost = 0;
        if (!empty($reservation->menu_items)) {
            $totalCost = Menu::whereIn('id', $reservation->menu_items)->sum('price');
        } else {
            $totalCost = $reservation->guest_number * 50; // contoh biaya per orang
        }

        $channelsPayment = app(\App\Services\TripayService::class)->getPaymentChannels();
        return view('reservations.step-four', compact('totalCost', 'reservation', 'order', 'channelsPayment'));
    }

    public function storeStepFour(Request $request)
    {
        Log::info('Mulai proses storeStepFour reservasi', ['input' => $request->all()]);
        $reservation = $request->session()->get('reservation');
        // Simpan ke database
        $reservationModel = new Reservation();
        $reservationModel->fill((array) $reservation);
        $reservationModel->status = 'pending';
        $reservationModel->first_name = $reservation->first_name;
        $reservationModel->last_name = $reservation->last_name;
        $reservationModel->email = $reservation->email;
        $reservationModel->tel_number = $reservation->tel_number;
        $reservationModel->res_date = $reservation->res_date;
        $reservationModel->table_id = $reservation->table_id;
        $reservationModel->guest_number = $reservation->guest_number;
        $reservationModel->save();
        Log::info('Reservasi berhasil disimpan', ['reservation_id' => $reservationModel->id]);
        $order = null;
        $paymentTransaction = null;
        $checkoutUrl = null;
        $paymentInstructions = null;
        $menuNames = [];

        // Jika ada menu, buat order dan payment Tripay
        if (!empty($reservation->menu_items)) {
            $amount = $this->calculateTotalPriceWithTax($reservation->menu_items);
            $total_price = $this->calculateTotalPrice($reservation->menu_items);
            $order = new Order();
            $order->reservation_id = $reservationModel->id;
            $order->menu_items = json_encode($reservation->menu_items);
            $order->total_price = $total_price;
            $order->amount = $amount;
            $order->customer_name = $reservation->first_name . ' ' . $reservation->last_name;
            $order->phone = $reservation->tel_number;
            $order->email = $reservation->email;
            $order->table_id = $reservation->table_id;
            $order->note = null;
            $order->tax = $this->calculateTax($total_price);
            $order->payment_status = 'pending';
            $order->qris_screenshot = null;
            $order->save();
            Log::info('Order berhasil dibuat', ['order_id' => $order->id]);
            $tripayService = new TripayPaymentService();
            $menuItems = [];
            $menus = \App\Models\Menu::whereIn('id', $reservation->menu_items)->get();
            foreach ($menus as $menu) {
                $menuItems[] = [
                    'sku' => 'DMS' . $menu->id,
                    'name' => $menu->name,
                    'price' => $menu->price,
                    'quantity' => 1,
                ];
            }
            $payload = [
                'name' => $order->customer_name,
                'email' => $order->email,
                'phone' => $order->phone,
                'table_id' => $order->table_id,
                'payment_channel' => $request->input('payment_channel', 'BRIVA'),
                'fee_flat' => 0,
                'fee_percent' => 0,
                'note' => $order->note,
                'amount' => $order->total_price,
                'from_reservation' => true,
            ];
            Log::info('Payload Tripay yang dikirim', $payload);
            $result = $tripayService->handleOrder($payload, collect($menuItems)->keyBy('sku')->map(function ($item) {
                return [
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ];
            })->toArray(), $order);
            Log::info('Response Tripay', ['result' => $result]);
            if (isset($result['transaction']['data']['checkout_url'])) {
                $checkoutUrl = $result['transaction']['data']['checkout_url'];
            }
            if (isset($order->id)) {
                $paymentTransaction = \App\Models\PaymentTransaction::where('order_id', $order->id)->orderByDesc('created_at')->first();
            }
            // Ambil instruksi pembayaran jika ada
            if (isset($result['transaction']['data']['instructions'])) {
                $paymentInstructions = $result['transaction']['data']['instructions'];
            } elseif ($paymentTransaction && isset($paymentTransaction->payment_response['data']['instructions'])) {
                $paymentInstructions = $paymentTransaction->payment_response['data']['instructions'];
            }
            $menuNames = $menus->pluck('name')->toArray();
        }
        // ...jika tidak ada menu, tetap isi menuNames dari reservation
        if (empty($menuNames) && !empty($reservation->menu_items)) {
            $menuNames = Menu::whereIn('id', $reservation->menu_items)->pluck('name')->toArray();
        }
        Log::info($paymentTransaction, ['paymentTransaction' => $paymentTransaction]);
        // Hapus session reservation
        $request->session()->forget('reservation');
        // Redirect ke halaman thankyou berbasis order id
        return redirect()->route('thankyou', ['order' => $order->id]);
    }

    // Tambahkan method thankyou berbasis order id
    public function thankyou($orderId)
    {
        $order = \App\Models\Order::with('orderItems')->findOrFail($orderId);
        $reservation = $order->reservation ?? null;
        // Ambil PaymentTransaction terbaru via relasi order
        $payment = PaymentTransaction::where('order_id', $order->id)->latest()->first();
        $menu_names = method_exists($order, 'menus') ? $order->menus->pluck('name')->toArray() : [];
        if (!$reservation && method_exists($order, 'reservation')) {
            $reservation = $order->reservation;
        }
        $reservationArr = $reservation ? [
            'first_name' => $reservation->first_name ?? '',
            'last_name' => $reservation->last_name ?? '',
            'email' => $reservation->email ?? '',
            'tel_number' => $reservation->tel_number ?? '',
            'res_date' => $reservation->res_date ?? '',
            'guest_number' => $reservation->guest_number ?? '',
            'table_name' => optional($reservation->table)->name ?? optional($order->table)->name ?? '-',
            'menu_names' => $menu_names,
        ] : null;
        // Ambil instruksi pembayaran dan checkout_url
        $payment_instructions = null;
        $checkout_url = null;
        if ($payment) {
            $trx = $payment->payment_response;
            if (is_string($trx)) {
                $trx = json_decode($trx, true);
            }
            if (is_array($trx)) {
                if (isset($trx['data']['instructions'])) {
                    $payment_instructions = $trx['data']['instructions'];
                }
                if (isset($trx['data']['checkout_url'])) {
                    $checkout_url = $trx['data']['checkout_url'];
                }
            }
        }
        Log::info($payment);
        return view('thankyou', [
            'reservation' => $reservationArr,
            'order' => $order,
            'payment' => $payment,
            'payment_instructions' => $payment_instructions,
            'checkout_url' => $checkout_url,
        ]);
    }

    // Helper functions to calculate total price based on menu items selected
    protected function calculateTotalPrice($menuItems)
    {
        $menus = Menu::whereIn('id', $menuItems)->get();
        return $menus->sum('price');
    }

    protected function calculateTax($amount)
    {
        return round($amount * 0.1, 2); // Pajak 10%
    }

    protected function calculateTotalPriceWithTax($menuItems)
    {
        $total = $this->calculateTotalPrice($menuItems);
        $tax = $this->calculateTax($total);
        return $total + $tax;
    }
}
