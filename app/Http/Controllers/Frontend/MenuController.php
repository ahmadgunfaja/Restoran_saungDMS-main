<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Category;
use App\Models\table;
use App\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{

    /**
     * Display a listing of the resource.
     */



    public function index()
    {
        $categories = Category::with('menus')->get();
        $tables = Table::all();
        Log::info('Categories and tables retrieved successfully', [
            'categories' => $categories->toArray(),
            'tables' => $tables->toArray(),
        ]);
        return view('menus.index', compact('categories', 'tables'));
    }

    public function addToCart(Request $request, $menuId)
    {
        $menu = Menu::findOrFail($menuId);

        // Ambil keranjang dari session, jika tidak ada, buat array kosong
        $cart = session()->get('cart', []);

        // Jika menu sudah ada dalam keranjang, tambahkan jumlahnya
        if (isset($cart[$menuId])) {
            $cart[$menuId]['quantity']++;
        } else {
            $cart[$menuId] = [
                'name' => $menu->name,
                'price' => $menu->price,
                'quantity' => 1,
                'image' => $menu->image,
            ];
        }

        // Simpan kembali ke session
        session()->put('cart', $cart);

        return redirect()->route('menus.index')->with('success', 'Menu added to cart!');
    }

    public function updateQuantity(Request $request, $menuId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Ambil menu dari keranjang
        $cart = session()->get('cart', []);

        // Update jumlah item yang dipesan
        if (isset($cart[$menuId])) {
            $cart[$menuId]['quantity'] = $request->quantity;
        }

        // Simpan kembali ke session
        session()->put('cart', $cart);
        return redirect()->route('menus.checkout');
    }

    public function checkout()
    {
        $cart = session()->get('cart');
        if (empty($cart)) {
            return redirect()->route('menus.index')->with('error', 'Your cart is empty!');
        }
        if (auth()->check()) {
            $user = auth()->user();
            Log::info('User is authenticated', ['user_id' => $user->id, 'email' => $user->email]);
        } else {
            Log::info('User is not authenticated');
        }
        $tables = Table::all(); // Mengambil semua meja dari database

        // Ambil daftar channel pembayaran dari TripayService
        $channelsPayment = app(TripayService::class)->getPaymentChannels();
        Log::info('Checkout initiated', [
            'cart' => $cart,
            'tables' => $tables->toArray(),
            'channelsPayment' => $channelsPayment,
        ]);
        return view('menus.checkout', compact('cart', 'tables', 'channelsPayment'));
    }


    public function removeFromCart($menuId)
    {
        // Ambil keranjang dari session
        $cart = session()->get('cart', []);

        // Jika item ada di keranjang, hapus item tersebut
        if (isset($cart[$menuId])) {
            unset($cart[$menuId]);
        }

        // Simpan keranjang yang diperbarui ke session
        session()->put('cart', $cart);

        // Arahkan kembali ke halaman menu dengan pesan sukses
        return redirect()->route('menus.index')->with('success', 'Item removed from cart!');
    }





    // Menampilkan daftar order milik user (atau berdasarkan session/email)
    public function orders(Request $request)
    {
        // Jika user login, bisa pakai Auth::user()->email, jika tidak pakai session/email dari order
        $email = $request->session()->get('customer_email');
        $orders = Order::where('email', $email)->orderByDesc('created_at')->get();
        return view('menus.orders', compact('orders'));
    }


    private function calculateTotalPrice($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        $tax = $total * 0.10; // Pajak 10%
        return $total + $tax;
    }

    public function summary($orderId)
    {
        $order = Order::with(['orderItems', 'menus', 'table'])->findOrFail($orderId);
        $payment = DB::table('payment_transactions')->where('order_id', $order->id)->latest()->first();
        $merchantCode = config('services.tripay.merchant_code');
        return view('menus.summary', compact('order', 'payment', 'merchantCode'));
    }
}
