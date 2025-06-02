<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Order') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex m-2 p-2">
                <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-700 rounded-lg text-white">Back to Orders</a>
            </div>
            <div class="m-2 p-2 bg-slate-100 rounded">
                <div class="space-y-8 divide-y divide-gray-200 w-1/2 mt-10">
                    <form method="POST" action="{{ route('admin.orders.update', $order->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Customer Name -->
                        <div class="sm:col-span-6">
                            <label for="customer_name" class="block text-sm font-medium text-gray-700">Customer Name</label>
                            <div class="mt-1">
                                <input type="text" id="customer_name" name="customer_name" class="block w-full bg-white border border-gray-400 rounded-md py-2 px-3 text-base sm:text-sm" value="{{ old('customer_name', $order->customer_name) }}" required />
                            </div>
                            @error('customer_name')
                                <div class="text-sm text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Customer Email -->
                        <div class="sm:col-span-6">
                            <label for="email" class="block text-sm font-medium text-gray-700">Customer Email</label>
                            <div class="mt-1">
                                <input type="email" id="email" name="email" class="block w-full bg-white border border-gray-400 rounded-md py-2 px-3 text-base sm:text-sm" value="{{ old('email', $order->email) }}" required />
                            </div>
                            @error('email')
                                <div class="text-sm text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Customer Phone -->
                        <div class="sm:col-span-6">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Customer Phone</label>
                            <div class="mt-1">
                                <input type="text" id="phone" name="phone" class="block w-full bg-white border border-gray-400 rounded-md py-2 px-3 text-base sm:text-sm" value="{{ old('phone', $order->phone) }}" required />
                            </div>
                            @error('phone')
                                <div class="text-sm text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Select Menu -->
                        <div class="sm:col-span-6">
                            <label for="menu_id" class="block text-sm font-medium text-gray-700">Menu</label>
                            <select id="menu_id" name="menu_id" class="form-select block w-full mt-1" required>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->id }}" @selected(old('menu_id', $order->menu_id) == $menu->id)>
                                        {{ $menu->name }} - {{ 'Rp. ' . number_format($menu->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('menu_id')
                                <div class="text-sm text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div class="sm:col-span-6">
                            <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                            <div class="mt-1">
                                <input type="number" id="quantity" name="quantity" min="1" class="block w-full bg-white border border-gray-400 rounded-md py-2 px-3 text-base sm:text-sm" value="{{ old('quantity', $order->quantity) }}" required />
                            </div>
                            @error('quantity')
                                <div class="text-sm text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Total Price -->
                        <div class="sm:col-span-6">
                            <label for="total_price" class="block text-sm font-medium text-gray-700">Total Price</label>
                            <div class="mt-1">
                                <input type="text" id="total_price" name="total_price" class="block w-full bg-white border border-gray-400 rounded-md py-2 px-3 text-base sm:text-sm" value="{{ old('total_price', $order->total_price) }}" readonly />
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div class="sm:col-span-6">
                            <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment Status</label>
                            <select name="payment_status" id="payment_status" class="form-select block w-full mt-1" required>
                                <option value="pending" @selected(old('payment_status', $order->payment_status) == 'pending')>Pending</option>
                                <option value="completed" @selected(old('payment_status', $order->payment_status) == 'completed')>Completed</option>
                                <option value="failed" @selected(old('payment_status', $order->payment_status) == 'failed')>Failed</option>
                            </select>
                            @error('payment_status')
                                <div class="text-sm text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Qris Screenshot -->
                        <div class="sm:col-span-6">
                            <label for="qris_screenshot" class="block text-sm font-medium text-gray-700">QRIS Screenshot (Optional)</label>
                            <div class="mt-1">
                                <input type="file" id="qris_screenshot" name="qris_screenshot" class="block w-full bg-white border border-gray-400 rounded-md py-2 px-3 text-base sm:text-sm" />
                            </div>
                            @error('qris_screenshot')
                                <div class="text-sm text-red-400">{{ $message }}</div>
                            @enderror
                            @if ($order->qris_screenshot)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/qris_screenshots/' . $order->qris_screenshot) }}" alt="QRIS Screenshot" class="w-20 h-20 object-cover">
                                </div>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 p-4">
                            <button type="submit" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-700 rounded-lg text-white">Update Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>