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
                            <h3 class="mb-4 text-xl font-bold text-blue-600">Make Reservation</h3>

                            <div class="w-full flex items-center justify-between mb-8">
                                <div class="flex w-full">
                                    <div class="flex flex-col items-center w-1/4">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold {{ request()->routeIs('reservations.step-one') ? 'bg-blue-600' : 'bg-gray-300' }}">1</div>
                                        <span class="text-xs mt-2 {{ request()->routeIs('reservations.step-one') ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Data Diri</span>
                                    </div>
                                    <div class="flex items-center w-1/4">
                                        <div class="flex-1 h-1 {{ (request()->routeIs('reservations.step-two') || request()->routeIs('reservations.step-three') || request()->routeIs('reservations.step-four')) ? 'bg-blue-400' : 'bg-gray-300' }}"></div>
                                    </div>
                                    <div class="flex flex-col items-center w-1/4">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold {{ request()->routeIs('reservations.step-two') ? 'bg-blue-600' : (request()->routeIs('reservations.step-one') ? 'bg-gray-300' : 'bg-blue-400') }}">2</div>
                                        <span class="text-xs mt-2 {{ request()->routeIs('reservations.step-two') ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Meja/Menu</span>
                                    </div>
                                    <div class="flex items-center w-1/4">
                                        <div class="flex-1 h-1 {{ (request()->routeIs('reservations.step-three') || request()->routeIs('reservations.step-four') ? 'bg-blue-400' : 'bg-gray-300') }}"></div>
                                    </div>
                                    <div class="flex flex-col items-center w-1/4">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold {{ request()->routeIs('reservations.step-three') ? 'bg-blue-600' : (request()->routeIs('reservations.step-four') ? 'bg-blue-400' : 'bg-gray-300') }}">3</div>
                                        <span class="text-xs mt-2 {{ request()->routeIs('reservations.step-three') ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Menu</span>
                                    </div>
                                    <div class="flex items-center w-1/4">
                                        <div class="flex-1 h-1 {{ request()->routeIs('reservations.step-four') ? 'bg-blue-400' : 'bg-gray-300' }}"></div>
                                    </div>
                                    <div class="flex flex-col items-center w-1/4">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold {{ request()->routeIs('reservations.step-four') ? 'bg-blue-600' : 'bg-gray-300' }}">4</div>
                                        <span class="text-xs mt-2 {{ request()->routeIs('reservations.step-four') ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Pembayaran</span>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('reservations.store.step-two') }}">
                                @csrf                                
                              <!-- Memilih Meja -->
                                <div class="sm:col-span-6 pt-5">
                                    <label for="table_id" class="block text-sm font-medium text-gray-700">Select Table</label>
                                    <div class="mt-1">
                                        <select id="table_id" name="table_id" class="form-multiselect block w-full mt-1">
                                            @foreach ($tables as $table)
                                                <option value="{{ $table->id }}">{{ $table->name }} ({{ $table->guest_number }} Guests)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('table_id')
                                        <div class="text-sm text-red-400">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Checkbox for ordering menu -->
                                <div class="sm:col-span-6 pt-5">
                                    <label for="order_menu" class="block text-sm font-medium text-gray-700">Would you like to order menu items?</label>
                                    <div class="mt-1">
                                        <input type="checkbox" id="order_menu" name="order_menu" value="1" class="form-checkbox text-indigo-600 h-5 w-5">
                                    </div>
                                    @error('order_menu')
                                        <div class="text-sm text-red-400">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mt-6 p-4 flex justify-between">
                                    <a href="{{ route('reservations.step-one') }}" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-700 rounded-lg text-white">Previous</a>
                                    <button type="submit" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-700 rounded-lg text-white">Next</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
