<x-guest-layout>
    <div class="container w-full px-5 py-6 mx-auto">
        <h2 class="text-2xl font-semibold mb-6 text-center">Our Menu</h2>

        @if(session()->has('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if(session()->has('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
        @foreach ($categories as $category)
            <div class="category-section mb-8">
                <h3 class="text-xl font-semibold text-center">{{ $category->name }}</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($category->menus as $menu)
                        <div class="menu-card max-w-xs mx-auto mb-4 rounded-lg shadow-lg bg-white">
                            <img class="w-full h-48 object-cover rounded-t-lg" src="{{ Storage::url($menu->image) }}" alt="{{ $menu->name }}" />
                            <div class="p-4">
                                <h4 class="text-lg font-semibold text-gray-800 mb-2">{{ $menu->name }}</h4>
                                <p class="text-sm text-gray-600 mb-4">{{ $menu->description }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xl font-bold text-green-600">{{ 'Rp. ' . number_format($menu->price, 0, ',', '.') }}</span>
                                    <form action="{{ route('menus.addToCart', $menu->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                                            <i class="fas fa-plus mr-2"></i>
                                         
                                        </button>   
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Keranjang -->
        <div class="fixed bottom-0 left-0 w-full bg-gray-800 text-white p-4" id="cart-display">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 4l1 16h10l1-16H6z" />
                    </svg>
                    <span class="text-lg">Cart ({{ count(session()->get('cart', [])) }})</span>
                </div>
                <a href="{{ route('menus.checkout') }}" class="px-6 py-2 bg-blue-500 rounded-lg hover:bg-blue-600">Checkout</a>
            </div>

            <!-- Menampilkan Item Keranjang -->
            <div class="mt-4" id="cart-items">
                @foreach(session()->get('cart', []) as $menuId => $item)
                    <div class="flex justify-between items-center mb-2" id="cart-item-{{ $menuId }}">
                        <div>
                            <span class="font-semibold">{{ $item['name'] }}</span>
                            <span class="text-sm text-gray-400">{{ 'Rp. ' . number_format($item['price'], 0, ',', '.') }} x {{ $item['quantity'] }}</span>
                        </div>
                        <form action="{{ route('menus.removeFromCart', $menuId) }}" method="POST" id="remove-form-{{ $menuId }}">
                            @csrf
                            <button type="submit" class="flex items-center text-red-500 hover:text-red-700" onclick="removeItemFromCart({{ $menuId }})">
                                <i class="fas fa-minus mr-2"></i>
                               
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- JavaScript untuk Interaksi Keranjang -->
        <script>
            // Update jumlah keranjang saat item ditambahkan atau dihapus
            function updateCartCount() {
                const cartCount = document.getElementById('cart-count');
                const cartItems = document.querySelectorAll('#cart-items > div');
                cartCount.innerText = cartItems.length;
            }

            // Fungsi untuk menghapus item dari keranjang
            function removeItemFromCart(menuId) {
                // Kirim request untuk menghapus item dari keranjang
                document.getElementById('remove-form-' + menuId).submit();

                // Hapus elemen item dari DOM setelah mengirim request
                const itemElement = document.getElementById('cart-item-' + menuId);
                if (itemElement) {
                    itemElement.remove();
                }

                // Update jumlah keranjang setelah item dihapus
                updateCartCount();
            }

            // Update jumlah keranjang di awal ketika halaman dimuat
            window.onload = function() {
                updateCartCount();
            }
        </script>
    </div>
</x-guest-layout>
