<x-guest-layout>
<div class="container mx-auto max-w-4xl py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4 text-center text-green-700">Ringkasan Pesanan Anda</h2>
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Kiri: Detail Pesanan & Ringkasan -->
            <div class="flex-1">
                <div class="mb-4 flex flex-col md:flex-row md:justify-between md:items-center">
                    <div>
                        <div class="text-gray-600">Nama Pemesan:</div>
                        <div class="font-semibold">{{ $order->customer_name }}</div>
                        <div class="text-gray-600 mt-2">Meja:</div>
                        <div class="font-semibold">{{ $order->table->name ?? '-' }}</div>
                    </div>
                    <div class="mt-4 md:mt-0 text-right">
                        <div class="text-gray-600">Tanggal:</div>
                        <div class="font-semibold">{{ $order->created_at->format('d M Y H:i') }}</div>
                        <div class="text-gray-600 mt-2">Status:</div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $order->payment_status == 'paid' ? 'Lunas' : 'Belum Dibayar' }}
                        </span>
                    </div>
                </div>
                <hr class="my-4">
                @php
                    $subtotal = 0;
                    $tax = 0;
                    // Fix: convert $payment to array if stdClass
                    if(isset($payment) && $payment instanceof \stdClass) {
                        $payment = json_decode(json_encode($payment), true);
                    }
                    foreach($order->orderItems as $item) {
                        if($item->sku === 'TAX10') {
                            $tax += $item->price * $item->quantity;
                        } else {
                            $subtotal += $item->price * $item->quantity;
                        }
                    }
                    $fee_customer = isset($payment['data']['fee_customer']) ? $payment['data']['fee_customer'] : (isset($payment['fee_customer']) ? $payment['fee_customer'] : 0);
                    $total = isset($payment['data']['amount']) ? $payment['data']['amount'] : ($subtotal + $tax + $fee_customer);
                @endphp
                <h3 class="text-lg font-semibold mb-2">Detail Pesanan</h3>
                <div class="overflow-x-auto mb-2">
                    <table class="min-w-full text-sm border rounded-lg">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-2 px-3 text-left">SKU</th>
                                <th class="py-2 px-3 text-left">Menu</th>
                                <th class="py-2 px-3 text-center">Qty</th>
                                <th class="py-2 px-3 text-right">Harga</th>
                                <th class="py-2 px-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                            <tr class="border-b">
                                <td class="py-2 px-3 text-xs text-gray-500">{{ $item->sku }}</td>
                                <td class="py-2 px-3">{{ $item->name }}</td>
                                <td class="py-2 px-3 text-center">{{ $item->quantity }}</td>
                                <td class="py-2 px-3 text-right">Rp {{ number_format($item->price,0,',','.') }}</td>
                                <td class="py-2 px-3 text-right">Rp {{ number_format($item->price * $item->quantity,0,',','.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-col gap-2 text-xs text-gray-500 mb-2">
                    <div><b>Kode Merchant:</b> {{ $merchantCode }}</div>
                    @if($payment)
                        @if(isset($payment['data']['merchant_ref']))
                            <div><b>Nomor Referensi Merchant:</b> {{ $payment['data']['merchant_ref'] }}</div>
                        @endif
                        @if(isset($payment['payment_channel']))
                            <div><b>Kode Channel Pembayaran:</b> {{ $payment['payment_channel'] }}</div>
                        @endif
                    @endif
                </div>
                <div class="w-full md:w-3/4 mx-auto mt-4 bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between py-1">
                        <span class="text-gray-700">Subtotal</span>
                        <span>Rp {{ number_format($subtotal,0,',','.') }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-gray-700">Pajak 10%</span>
                        <span>Rp {{ number_format($tax,0,',','.') }}</span>
                    </div>
                    @php
                        $overPeopleFee = 0;
                        if($order->orderItems) {
                            foreach($order->orderItems as $item) {
                                if($item->sku === 'OVERPEOPLE') {
                                    $overPeopleFee += $item->price * $item->quantity;
                                }
                            }
                        }
                    @endphp
                    @if($overPeopleFee > 0)
                    <div class="flex justify-between py-1">
                        <span class="text-orange-700">Biaya Tambahan Orang</span>
                        <span class="text-orange-700">Rp {{ number_format($overPeopleFee,0,',','.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between py-1">
                        <span class="text-blue-700">Biaya Channel</span>
                        <span class="text-blue-700">Rp {{ number_format($fee_customer,0,',','.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-t">
                        <span class="text-gray-700">Total</span>
                        <span class="font-semibold text-green-700">Rp {{ number_format($total,0,',','.') }}</span>
                    </div>
                </div>
                <div class="mt-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <a href="{{ route('menus.orders') }}" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded transition">Lihat Daftar Pesanan</a>
                    @if(isset($payment) && $order->payment_status != 'paid')
                        @php
                            $checkoutUrl = null;
                            $trx = null;
                            if(isset($payment['payment_response'])) {
                                $trx = $payment['payment_response'];
                                if(is_string($trx)) {
                                    $tryDecode = json_decode($trx, true);
                                    if(is_string($tryDecode)) {
                                        $trx = json_decode($tryDecode, true);
                                    } else {
                                        $trx = $tryDecode;
                                    }
                                }
                            } elseif(is_array($payment)) {
                                $trx = $payment;
                            }
                            if(isset($trx['data']['checkout_url'])) {
                                $checkoutUrl = $trx['data']['checkout_url'];
                            }
                        @endphp
                        <a href="{{ $checkoutUrl ?? route('payment.order', $order->id) }}" target="_blank" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded transition">Lanjut Pembayaran</a>
                    @endif
                </div>
            </div>
            <!-- Kanan: Instruksi Pembayaran Sticky -->
            <div class="flex-1 md:sticky md:top-8 h-fit">
                @if(isset($payment))
                    @php
                        // Parsing payment response agar $trx['data'] selalu array jika ada
                        $trx = null;
                        if(isset($payment['payment_response'])) {
                            $trx = $payment['payment_response'];
                            // Handle double encoded JSON string
                            if(is_string($trx)) {
                                $tryDecode = json_decode($trx, true);
                                if(is_string($tryDecode)) {
                                    $trx = json_decode($tryDecode, true);
                                } else {
                                    $trx = $tryDecode;
                                }
                            }
                        } elseif(is_array($payment)) {
                            $trx = $payment;
                        }
                        $instructions = [];
                        $qrUrl = null;
                        if(isset($trx['data']['instructions'])) {
                            $instructions = $trx['data']['instructions'];
                        }
                        if(isset($trx['data']['qr_url'])) {
                            $qrUrl = $trx['data']['qr_url'];
                        }
                    @endphp
                    @if(!empty($instructions))
                        <div class="bg-gray-50 rounded-lg p-4 shadow mb-4">
                            <h3 class="font-bold text-lg mb-2 text-indigo-700">Instruksi Pembayaran</h3>
                            @foreach($instructions as $instruksi)
                                <div class="mb-4">
                                    <div class="font-semibold text-base text-gray-700 mb-1">{{ $instruksi['title'] }}</div>
                                    <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1">
                                        @foreach($instruksi['steps'] as $step)
                                            <li>{!! $step !!}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-sm text-gray-500 italic">Tidak ada instruksi pembayaran tersedia.</div>
                    @endif
                    @if(!empty($qrUrl))
                        <div class="bg-white rounded-lg p-4 shadow flex flex-col items-center">
                            <h4 class="font-semibold text-base text-gray-700 mb-2">QRIS Pembayaran</h4>
                            <img src="{{ $qrUrl }}" alt="QRIS" class="w-48 h-48 object-contain mb-2">
                            <div class="text-xs text-gray-500">Scan QRIS di aplikasi pembayaran Anda</div>
                        </div>
                    @elseif($order->qris_screenshot)
                        <div class="bg-white rounded-lg p-4 shadow flex flex-col items-center">
                            <h4 class="font-semibold text-base text-gray-700 mb-2">QRIS Pembayaran</h4>
                            <img src="{{ asset($order->qris_screenshot) }}" alt="QRIS" class="w-48 h-48 object-contain mb-2">
                            <div class="text-xs text-gray-500">Scan QRIS di aplikasi pembayaran Anda</div>
                        </div>
                    @else
                        <div class="text-sm text-gray-500 italic">QRIS tidak tersedia.</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
</x-guest-layout>