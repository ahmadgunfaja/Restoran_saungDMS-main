<x-guest-layout>
    <div class="container w-full px-5 py-6 mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Upload QRIS Screenshot</h2>

        <form action="{{ route('menus.uploadQrisScreenshot') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="qris_screenshot" class="block">Upload QRIS Screenshot</label>
                <input type="file" name="qris_screenshot" id="qris_screenshot" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block">Total Payment</label>
                <span class="text-lg font-semibold">{{ 'Rp. ' . number_format($order->total_price, 0, ',', '.') }}</span>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Upload Screenshot</button>
        </form>
    </div>
</x-guest-layout>
