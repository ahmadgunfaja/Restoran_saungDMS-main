<x-admin-layout>
    <div class="container mx-auto py-8">
        <h2 class="text-2xl font-bold mb-6">Daftar Transaksi Pembayaran</h2>
        <div class="overflow-x-auto bg-white rounded shadow p-4">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-3">Ref</th>
                        <th class="py-2 px-3">Order ID</th>
                        <th class="py-2 px-3">Customer</th>
                        <th class="py-2 px-3">Produk</th>
                        <th class="py-2 px-3">Channel</th>
                        <th class="py-2 px-3">Amount</th>
                        <th class="py-2 px-3">Status</th>
                        <th class="py-2 px-3">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $trx)
                    <tr class="border-b">
                        <td class="py-2 px-3">{{ $trx->merchant_ref ?? $trx->id }}</td>
                        <td class="py-2 px-3">{{ $trx->order_id }}</td>
                        <td class="py-2 px-3">{{ $trx->customer_name }}</td>
                        <td class="py-2 px-3">
                            @php
                                $items = is_string($trx->order_items) ? json_decode($trx->order_items, true) : $trx->order_items;
                            @endphp
                            <ul class="list-disc ml-4">
                                @foreach($items as $item)
                                    @if(!str_contains(strtolower($item['name']), 'pajak') && !str_contains(strtolower($item['name']), 'fee'))
                                        <li>{{ $item['name'] }} <span class="text-xs text-gray-500">x{{ $item['quantity'] }}</span></li>
                                    @endif
                                @endforeach
                            </ul>
                        </td>
                        <td class="py-2 px-3">{{ $trx->payment_channel }}</td>
                        <td class="py-2 px-3">Rp {{ number_format($trx->amount,0,',','.') }}</td>
                        <td class="py-2 px-3">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $trx->status == 'PAID' ? 'bg-green-100 text-green-700' : ($trx->status == 'EXPIRED' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ $trx->status }}
                            </span>
                        </td>
                        <td class="py-2 px-3">{{ $trx->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
