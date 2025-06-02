<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Menu;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    // Menampilkan semua pesanan
   public function index()
    {
        $orders = Order::with('menus')->get(); // Mengambil semua pesanan dengan relasi menu
        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $menus = Menu::all(); // Ambil semua menu
        return view('admin.orders.create', compact('menus'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'table_id' => 'required|exists:tables,id',
            'total_price' => 'required|numeric',
            'payment_status' => 'required|in:pending,completed,failed', // Validasi payment_status
            'qris_screenshot' => 'nullable|image|max:2048', // Maksimal 2MB, opsional
        ]);

        $data = $request->all();

        // Upload screenshot jika ada
        if ($request->hasFile('qris_screenshot')) {
            $file = $request->file('qris_screenshot');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/qris_screenshots', $filename);
            $data['qris_screenshot'] = $filename;
        }

        // Simpan order ke database
        Order::create($data);

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully!');
    }

   public function update(Request $request, Order $order)
{
    $request->validate([
        'customer_name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string',
        'table_id' => 'required|exists:tables,id',
        'total_price' => 'required|numeric',
        'payment_status' => 'required|in:pending,completed,failed', // Validasi payment_status
        'qris_screenshot' => 'nullable|image|max:2048', // Maksimal 2MB, opsional
    ]);

    $data = $request->all();

    // Upload screenshot baru jika ada
    if ($request->hasFile('qris_screenshot')) {
        $file = $request->file('qris_screenshot');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/qris_screenshots', $filename);
        $data['qris_screenshot'] = $filename;
    }

    // Update order di database
    $order->update($data);

    return redirect()->route('admin.orders.index')->with('success', 'Order updated successfully.');
}

    // Menampilkan form untuk mengedit pesanan
    public function edit(Order $order)
    {
        $menus = Menu::all();
        return view('admin.orders.index', compact('order', 'menus'));
    }

    // Menghapus pesanan
    public function destroy(Order $order)
    {
        $order->delete();  // Hapus pesanan dari database

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}
