<x-guest-layout>
    <div class="container w-full px-5 py-6 mx-auto">
        <div class="flex items-center min-h-screen bg-gray-50">
            <div class="flex-1 h-full max-w-4xl mx-auto bg-white rounded-lg shadow-xl">
                <div class="flex flex-col md:flex-row">
                    <div class="h-32 md:h-auto md:w-1/2">
                        <img class="object-cover w-full h-full" src="{{ asset('images/Restaurant.jpeg') }}" alt="img" />
                    </div>
                    <div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
                        <div class="w-full">
                            <h3 class="mb-4 text-xl font-bold text-blue-600">Payment</h3>

                            <div class="w-full flex items-center justify-between mb-8">
                                <div class="flex w-full">
                                    <div class="flex flex-col items-center w-1/4">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold {{ request()->routeIs('reservations.step-one') ? 'bg-blue-600' : 'bg-gray-300' }}">1
                                        </div>
                                        <span class="text-xs mt-2 {{ request()->routeIs('reservations.step-one') ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Data Diri</span>
                                    </div>
                                    <div class="flex items-center w-1/4">
                                        <div class="flex-1 h-1 {{ (request()->routeIs('reservations.step-two') || request()->routeIs('reservations.step-three') || request()->routeIs('reservations.step-four')) ? 'bg-blue-400' : 'bg-gray-300' }}"></div>
                                    </div>
                                    <div class="flex flex-col items-center w-1/4">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold {{ request()->routeIs('reservations.step-two') ? 'bg-blue-600' : (request()->routeIs('reservations.step-one') ? 'bg-gray-300' : 'bg-blue-400') }}">2
                                        </div>
                                        <span class="text-xs mt-2 {{ request()->routeIs('reservations.step-two') ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Meja/Menu</span>
                                    </div>
                                    <div class="flex items-center w-1/4">
                                        <div class="flex-1 h-1 {{ (request()->routeIs('reservations.step-three') || request()->routeIs('reservations.step-four')) ? 'bg-blue-400' : 'bg-gray-300' }}"></div>
                                    </div>
                                    <div class="flex flex-col items-center w-1/4">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold {{ request()->routeIs('reservations.step-three') ? 'bg-blue-600' : (request()->routeIs('reservations.step-four') ? 'bg-blue-400' : 'bg-gray-300') }}">3
                                        </div>
                                        <span class="text-xs mt-2 {{ request()->routeIs('reservations.step-three') ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Menu</span>
                                    </div>
                                    <div class="flex items-center w-1/4">
                                        <div class="flex-1 h-1 {{ request()->routeIs('reservations.step-four') ? 'bg-blue-400' : 'bg-gray-300' }}"></div>
                                    </div>
                                    <div class="flex flex-col items-center w-1/4">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold {{ request()->routeIs('reservations.step-four') ? 'bg-blue-600' : 'bg-gray-300' }}">4
                                        </div>
                                        <span class="text-xs mt-2 {{ request()->routeIs('reservations.step-four') ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Pembayaran</span>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('reservations.store.step-four') }}">
                                @csrf
                                <div class="sm:col-span-6 pt-5">
                                    <label for="total_cost" class="block text-sm font-medium text-gray-700">Total Cost</label>
                                    <div class="mt-1">
                                        <input type="text" id="total_cost" name="total_cost" value="{{ $totalCost }}" readonly
                                            class="block w-full appearance-none bg-white border border-gray-400 rounded-md py-2 px-3 text-base leading-normal transition duration-150 ease-in-out sm:text-sm sm:leading-5" />
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500">* Belum termasuk pajak 10%</div>
                                </div>
                                <div class="sm:col-span-6 pt-2">
                                    <label class="block text-sm font-medium text-gray-700">Pajak (10%)</label>
                                    <div class="mt-1">
                                        <input type="text" id="tax_value" name="tax_value" value="{{ number_format($totalCost * 0.10, 0, ',', '.') }}" readonly
                                            class="block w-full appearance-none bg-white border border-gray-400 rounded-md py-2 px-3 text-base leading-normal transition duration-150 ease-in-out sm:text-sm sm:leading-5 bg-gray-50" />
                                    </div>
                                </div>
                                @if(isset($overPeopleFee) && $overPeopleFee > 0)
                                <div class="sm:col-span-6 pt-2">
                                    <label class="block text-sm font-medium text-gray-700">Biaya Tambahan Orang ({{ $overPeopleCount }} x {{ number_format($overPeopleFeePerPerson,0,',','.') }})</label>
                                    <div class="mt-1">
                                        <input type="text" id="over_people_fee" name="over_people_fee" value="{{ number_format($overPeopleFee,0,',','.') }}" readonly
                                            class="block w-full appearance-none bg-white border border-orange-400 rounded-md py-2 px-3 text-base leading-normal transition duration-150 ease-in-out sm:text-sm sm:leading-5 bg-orange-50 font-semibold text-orange-700" />
                                    </div>
                                </div>
                                @endif
                                <div class="sm:col-span-6 pt-5">
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
                                    <div class="flex justify-between mt-2" id="feeBox" style="display:none;">
                                        <span class="text-blue-700">Biaya Channel</span>
                                        <span id="feeValue" class="text-blue-700">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between mt-2 border-t pt-2 font-bold text-indigo-700" id="grandTotalBox" style="display:none;">
                                        <span>Total Bayar</span>
                                        <span id="grandTotal">Rp 0</span>
                                    </div>
                                </div>

                                <div class="mt-6 p-4 flex justify-between">
                                    <a href="{{ route('reservations.step-three') }}" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-700 rounded-lg text-white">Previous</a>
                                    <button type="submit" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-700 rounded-lg text-white">complete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
        const totalCost = {{ $totalCost }};
        const tax = totalCost * 0.10;
        const overPeopleFee = {{ isset($overPeopleFee) ? $overPeopleFee : 0 }};
        const selectedFeeFlat = document.getElementById('selected_fee_flat');
        const selectedFeePercent = document.getElementById('selected_fee_percent');
        select.addEventListener('change', function() {
            const selectedOption = select.options[select.selectedIndex];
            const feeFlat = parseFloat(selectedOption.getAttribute('data-fee-flat') || 0);
            const feePercent = parseFloat(selectedOption.getAttribute('data-fee-percent') || 0);
            let fee = feeFlat + ((totalCost + tax + overPeopleFee) * feePercent / 100);
            // Pembulatan fee jika channel DANA, QRIS, ShopeePay, OVO dan fee < 1000
            const channelText = selectedOption.textContent.toLowerCase();
            let pembulatan = false;
            if ((channelText.includes('dana') || channelText.includes('qris') || channelText.includes('shopeepay') || channelText.includes('ovo')) && fee > 0 && fee < 1000) {
                fee = 1000;
                pembulatan = true;
            }
            if (select.value) {
                feeBox.style.display = 'flex';
                feeValue.textContent = 'Rp ' + Math.round(fee).toLocaleString('id-ID');
                grandTotalBox.style.display = 'flex';
                grandTotal.textContent = 'Rp ' + Math.round(totalCost + tax + overPeopleFee + fee).toLocaleString('id-ID');
                // Tambahkan info pembulatan jika ada
                let info = document.getElementById('feeRoundingInfo');
                if (!info) {
                    info = document.createElement('div');
                    info.id = 'feeRoundingInfo';
                    info.className = 'text-xs text-orange-600 mt-1';
                    feeBox.parentNode.insertBefore(info, feeBox.nextSibling);
                }
                if (pembulatan) {
                    info.textContent = 'Biaya channel dibulatkan ke Rp 1.000 sesuai ketentuan payment gateway yang dipakai.';
                    info.style.display = 'block';
                } else {
                    info.style.display = 'none';
                }
            } else {
                feeBox.style.display = 'none';
                grandTotalBox.style.display = 'none';
                let info = document.getElementById('feeRoundingInfo');
                if (info) info.style.display = 'none';
            }
            selectedFeeFlat.value = feeFlat;
            selectedFeePercent.value = feePercent;
        });
    });
</script>
