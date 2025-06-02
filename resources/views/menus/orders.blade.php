<x-guest-layout>
    <div class="container w-full px-5 py-6 mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Your Orders</h2>
        @if($orders->isEmpty())
            <p>No orders found.</p>
        @else
            <table class="min-w-full bg-white border">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border">Order ID</th>
                        <th class="py-2 px-4 border">Total</th>
                        <th class="py-2 px-4 border">Status</th>
                        <th class="py-2 px-4 border">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="py-2 px-4 border">{{ $order->id }}</td>
                            <td class="py-2 px-4 border">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td class="py-2 px-4 border">{{ $order->payment_status ?? 'pending' }}</td>
                            <td class="py-2 px-4 border">{{ $order->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-guest-layout>
