<x-guest-layout>
    <div class="container w-full max-w-4xl px-5 py-10 mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8 flex flex-col md:flex-row gap-8">
            <!-- Kiri: Detail Reservasi -->
            <div class="flex-1 min-w-0">
                <svg class="mx-auto mb-4 text-green-500" width="64" height="64" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="#e6fffa"/><path d="M9 12l2 2l4-4" stroke="#38a169" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <h1 class="text-2xl font-bold mb-2 text-green-700 text-center md:text-left">Reservasi Berhasil!</h1>
                <p class="mb-6 text-gray-700 text-center md:text-left">Terima kasih, reservasi Anda telah kami terima.<br>Detail reservasi dan pembayaran Anda:</p>
                @if($reservation)
                <div class="text-left mb-6">
                    <div class="mb-2"><span class="font-semibold">Nama:</span> {{ $reservation['first_name'] }} {{ $reservation['last_name'] }}</div>
                    <div class="mb-2"><span class="font-semibold">Email:</span> {{ $reservation['email'] }}</div>
                    <div class="mb-2"><span class="font-semibold">No. HP:</span> {{ $reservation['tel_number'] }}</div>
                    <div class="mb-2"><span class="font-semibold">Tanggal Reservasi:</span> {{ \Carbon\Carbon::parse($reservation['res_date'])->format('d M Y H:i') }}</div>
                    <div class="mb-2"><span class="font-semibold">Jumlah Tamu:</span> {{ $reservation['guest_number'] }}</div>
                    <div class="mb-2"><span class="font-semibold">Meja:</span> {{ $reservation['table_name'] ?? '-' }}</div>
                    @if(!empty($reservation['menu_names']))
                    <div class="mb-2"><span class="font-semibold">Menu Dipesan:</span>
                        <ul class="list-disc ml-6">
                            @foreach($reservation['menu_names'] as $menu)
                                <li>{{ $menu }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                @endif
                @if($order)
                @php
                    // Ambil breakdown dari order_items
                    $subtotal = 0;
                    $tax = 0;
                    $fee = 0;
                    $feeLabel = '';
                    if($order->orderItems) {
                        foreach($order->orderItems as $item) {
                            if($item->sku === 'TAX10') {
                                $tax += $item->price * $item->quantity;
                            } else {
                                $subtotal += $item->price * $item->quantity;
                            }
                        }
                    }
                    // Ambil fee dari payment transaction
                    if($payment) {
                        $fee = $payment->fee_customer + $payment->fee_merchant;
                        if($payment->fee_customer > 0) {
                            $feeLabel = 'Customer';
                        } elseif($payment->fee_merchant > 0) {
                            $feeLabel = 'Merchant';
                        }
                        if($payment->payment_channel && $feeLabel) {
                            $feeLabel .= ' ('.$payment->payment_channel.')';
                        }
                    } else {
                        $fee = 0;
                        $feeLabel = '';
                    }
                    $total = $subtotal + $tax + $fee;
                @endphp
                <div class="mb-4">
                    <div class="mb-2"><span class="font-semibold">Status Pembayaran:</span>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $order->payment_status == 'paid' ? 'Lunas' : 'Belum Dibayar' }}
                        </span>
                    </div>
                    <div class="mb-2"><span class="font-semibold">Subtotal:</span> Rp {{ number_format($subtotal,0,',','.') }}</div>
                    <div class="mb-2"><span class="font-semibold">Pajak 10%:</span> Rp {{ number_format($tax,0,',','.') }}</div>
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
                    <div class="mb-2"><span class="font-semibold text-orange-700">Biaya Tambahan Orang:</span> <span class="text-orange-700">Rp {{ number_format($overPeopleFee,0,',','.') }}</span></div>
                    @endif
                    <div class="mb-2"><span class="font-semibold">Biaya Channel{{ $feeLabel ? ' ('.$feeLabel.')' : '' }}:</span> Rp {{ number_format($fee,0,',','.') }}</div>
                    <div class="mb-2 font-bold text-indigo-700 text-lg"><span class="font-semibold">Total Bayar:</span> Rp {{ number_format($total,0,',','.') }}</div>
                </div>
                @endif
                <div class="mt-8 flex justify-center md:justify-start">
                    <a href="/" class="inline-block px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">Kembali ke Beranda</a>
                </div>
            </div>
            <!-- Kanan: Pembayaran & Instruksi -->
            <div class="flex-1 min-w-0">
                @php $trx = null; @endphp
                @if(!empty($payment_instructions))
                    <div class="bg-gray-50 rounded-lg p-4 shadow mb-4">
                        <h3 class="font-bold text-lg mb-2 text-indigo-700">Instruksi Pembayaran</h3>
                        @foreach($payment_instructions as $instruksi)
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
                    @if($payment)
                        @php
                            $trx = $payment->payment_response;
                            if(is_string($trx)) $trx = json_decode($trx, true);
                        @endphp
                        @if(isset($trx['data']['instructions']))
                            <div class="bg-gray-50 rounded-lg p-4 shadow mb-4">
                                <h3 class="font-bold text-lg mb-2 text-indigo-700">Instruksi Pembayaran</h3>
                                @foreach($trx['data']['instructions'] as $instruksi)
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
                        @endif
                    @endif
                @endif
                @if(isset($trx['data']['qris_url']) && $trx['data']['qris_url'])
                    <div class="bg-white rounded-lg p-4 shadow flex flex-col items-center mb-4">
                        <h4 class="font-semibold text-base text-gray-700 mb-2">QRIS Pembayaran</h4>
                        <img src="{{ $trx['data']['qris_url'] }}" alt="QRIS" class="w-48 h-48 object-contain mb-2">
                        <div class="text-xs text-gray-500">Scan QRIS di aplikasi pembayaran Anda</div>
                    </div>
                @elseif($order && $order->qris_screenshot)
                    <div class="bg-white rounded-lg p-4 shadow flex flex-col items-center mb-4">
                        <h4 class="font-semibold text-base text-gray-700 mb-2">QRIS Pembayaran</h4>
                        <img src="{{ asset($order->qris_screenshot) }}" alt="QRIS" class="w-48 h-48 object-contain mb-2">
                        <div class="text-xs text-gray-500">Scan QRIS di aplikasi pembayaran Anda</div>
                    </div>
                @endif
                @if(isset($checkout_url) && $order && $order->payment_status != 'paid')
                    <div class="mt-4 flex justify-center">
                        <a href="{{ $checkout_url }}" target="_blank" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded transition">Lanjutkan Pembayaran</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>