<x-guest-layout>
    <div class="container w-full px-5 py-6 mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Checkout</h2>
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Kiri: Cart & Total (fixed on desktop) -->
            <div class="flex-1 md:sticky md:top-8 h-fit">
                @if(session()->has('cart'))
                    <div class="mb-6">
                        <h3 class="font-bold text-lg mb-2">Keranjang Kamu</h3>
                        <ul>
                            @php
                                $totalPrice = 0;
                                foreach (session()->get('cart') as $menuId => $item) {
                                    $totalPrice += $item['price'] * $item['quantity'];
                                }
                                $taxRate = 0.10;
                                $taxAmount = $totalPrice * $taxRate;
                                $totalWithTax = $totalPrice + $taxAmount;
                            @endphp
                            @foreach (session()->get('cart') as $menuId => $item)
                                <li class="flex justify-between mb-2 border-b pb-1">
                                    <span>{{ $item['name'] }} <span class="text-xs text-gray-500">(x{{ $item['quantity'] }})</span></span>
                                    <span class="font-semibold">{{ 'Rp. ' . number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mb-6 bg-gray-50 rounded p-4" id="summary-box">
                        <h3 class="text-base font-semibold mb-2">Ringkasan</h3>
                        <div class="flex justify-between mb-1">
                            <span>Subtotal</span>
                            <span id="subtotal">{{ 'Rp. ' . number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span>Tax (10%)</span>
                            <span id="tax">{{ 'Rp. ' . number_format($taxAmount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mt-2 border-t pt-2 font-bold text-green-700">
                            <span>Total Dengan Pajak</span>
                            <span id="totalWithTax">{{ 'Rp. ' . number_format($totalWithTax, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mt-2" id="feeBox" style="display:none;">
                            <span class="text-blue-700">Biaya Channel</span>
                            <span id="feeValue" class="text-blue-700">Rp 0</span>
                        </div>
                        <div class="flex justify-between mt-2 border-t pt-2 font-bold text-indigo-700" id="grandTotalBox" style="display:none;">
                            <span>Total Bayar</span>
                            <span id="grandTotal">Rp 0</span>
                        </div>
                    </div>
                @endif
            </div>
            <!-- Kanan: Form -->
            <div class="flex-1">
                <form action="{{ route('menus.placeOrder') }}" method="POST" class="space-y-4" id="checkoutForm">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Informasi Pemesanan</h3>
                        <p class="text-sm text-gray-600 mb-2">Pastikan semua informasi di bawah ini sudah benar sebelum melanjutkan.</p>
                    </div>
                    @csrf
                    <div>
                        <label for="name" class="block">Nama Kamu</label>
                        <input type="text" id="name" name="name" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label for="email" class="block">Email Kamu</label>
                        <input type="text" id="email" name="email" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label for="phone" class="block">Nomor Handphone</label>
                        <input type="text" id="phone" name="phone" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label for="table_id" class="block">Nomor Meja</label>
                        <select id="table_id" name="table_id" class="w-full border rounded px-3 py-2" required>
                            @foreach ($tables as $table)
                                <option value="{{ $table->id }}">
                                    {{ $table->name }} - {{ $table->location }} ({{ $table->guest_number }} Tamu)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="note" class="block">Catatan (Opsional)</label>
                        <textarea id="note" name="note" class="w-full border rounded px-3 py-2" rows="4" placeholder="Add any special requests or notes"></textarea>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-lg">Pilih Metode Pembayaran</label>
                        <input type="hidden" id="selected_fee_flat" name="fee_flat" value="0">
                        <input type="hidden" id="selected_fee_percent" name="fee_percent" value="0">
                        <select name="payment_channel" id="payment_channel" class="w-full border rounded px-3 py-2" required>
                            <option value="">-- Pilih Channel Pembayaran --</option>
                            @if(isset($channelsPayment['success']) && $channelsPayment['success'] && isset($channelsPayment['data']))
                                @foreach($channelsPayment['data'] as $channel)
                                    <option 
                                        value="{{ $channel['code'] }}"
                                        data-fee-flat="{{ $channel['total_fee']['flat'] }}"
                                        data-fee-percent="{{ $channel['total_fee']['percent'] }}"
                                    >
                                        {{ $channel['name'] }} ({{ $channel['group'] }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="flex space-x-4 mt-6">
                        <a href="{{ route('menus.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Kembali ke Menu</a>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 ">Lanjut Checkout</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('payment_channel');
        const feeBox = document.getElementById('feeBox');
        const feeValue = document.getElementById('feeValue');
        const grandTotalBox = document.getElementById('grandTotalBox');
        const grandTotal = document.getElementById('grandTotal');
        const totalWithTax = {{ $totalWithTax }};
        const selectedFeeFlat = document.getElementById('selected_fee_flat');
        const selectedFeePercent = document.getElementById('selected_fee_percent');
        select.addEventListener('change', function() {
            const selectedOption = select.options[select.selectedIndex];
            const feeFlat = parseFloat(selectedOption.getAttribute('data-fee-flat') || 0);
            const feePercent = parseFloat(selectedOption.getAttribute('data-fee-percent') || 0);
            let fee = feeFlat + (totalWithTax * feePercent / 100);
            if (select.value) {
                feeBox.style.display = 'flex';
                feeValue.textContent = 'Rp ' + fee.toLocaleString('id-ID');
                grandTotalBox.style.display = 'flex';
                grandTotal.textContent = 'Rp ' + (totalWithTax + fee).toLocaleString('id-ID');
            } else {
                feeBox.style.display = 'none';
                grandTotalBox.style.display = 'none';
            }
            selectedFeeFlat.value = feeFlat;
            selectedFeePercent.value = feePercent;
        });
    });
</script>
