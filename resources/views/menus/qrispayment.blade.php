<x-guest-layout>
    <div class="container w-full px-5 py-6 mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Complete Your Payment</h2>

        <div class="mb-6">
            <h3>Your Order: {{ $order->id }}</h3>
            <p>Total Pembayaran: Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
        </div>

        <div class="mb-6">
            <h3>Scan the QR Code to complete the payment</h3>
            <img src="{{ asset('storage/qris_images/qris_' . $order->id . '.png') }}" alt="QRIS Code" class="w-40 mx-auto mb-4">
            <!-- Tombol untuk mengunduh QRIS -->
            <a href="{{ asset('storage/qris_images/qris_' . $order->id . '.png') }}" download="qris_image.png" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Download QRIS Code</a>
        </div>

        <h4 class="mt-4">How to Pay:</h4>
        <ol class="list-decimal pl-6">
            <li>Click or screenshot the QRIS code.</li>
            <li>Open QRIS payment in your m-banking or e-wallet app.</li>
            <li>Upload the image or screenshot of the QRIS code.</li>
            <li>Ensure the payment amount is correct and proceed with payment.</li>
            <li>Click "Check Payment Status" to confirm your payment.</li>
        </ol>

        <a href="{{ route('payment.status') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Check Payment Status</a>

        <!-- Add the "Order" link -->
        <div class="mt-6">
            <a href="{{ route('menus.orders') }}" class="text-blue-500">View Orders</a>
        </div>
    </div>
</x-guest-layout>
