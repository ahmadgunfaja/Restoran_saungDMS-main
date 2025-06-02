<x-koki-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Koki') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cards Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Penjualan -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <img src="https://img.icons8.com/ios-filled/50/000000/box.png" class="w-12 h-12 mr-4" alt="Penjualan">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Total Penjualan</h3>
                            <p class="text-2xl font-bold text-indigo-600">Rp 15,000,000</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Tersedia -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <img src="https://img.icons8.com/ios-filled/50/000000/restaurant-menu.png" class="w-12 h-12 mr-4" alt="Menu">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Jumlah Menu</h3>
                            <p class="text-2xl font-bold text-indigo-600">35</p>
                        </div>
                    </div>
                </div>

                <!-- Pesanan Masuk -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <img src="https://img.icons8.com/ios-filled/50/000000/order-received.png" class="w-12 h-12 mr-4" alt="Status Pesanan">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Pesanan Masuk</h3>
                            <p class="text-2xl font-bold text-indigo-600">12 Pesanan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Menu Section -->
            <div class="mt-8">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800">Menu yang Tersedia</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                        @foreach ($menus as $menu)
                            <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                                <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="w-full h-40 object-cover rounded-lg mb-4">
                                <h4 class="text-lg font-semibold text-gray-800">{{ $menu->name }}</h4>
                                <p class="text-gray-600">{{ $menu->description }}</p>
                                <p class="text-xl font-bold text-indigo-600 mt-2">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-koki-layout>
