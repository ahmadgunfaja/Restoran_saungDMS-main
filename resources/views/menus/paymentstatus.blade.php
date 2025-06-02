<x-guest-layout>
    <div class="container w-full px-5 py-6 mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Payment Status</h2>

        <div class="mb-6">
            <h3>Status Pembayaran: {{ ucfirst($paymentStatus) }}</h3>

            @if ($paymentStatus === 'pending')
                <p>We are still processing your payment. Please check back later.</p>
            @elseif ($paymentStatus === 'completed')
                <p>Your payment has been completed successfully. Thank you!</p>
            @else
                <p>Payment status is unknown. Please try again later.</p>
            @endif
        </div>

        <a href="{{ route('menus.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Go Back to Menu</a>
    </div>
</x-guest-layout>


<!-- <x-guest-layout>
    <div class="container w-full px-5 py-6 mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Payment Status</h2>

        @if(session()->has('success'))
            <p class="text-green-600">{{ session('success') }}</p>
        @endif

        <h3 class="text-xl">Payment Status: {{ $paymentStatus }}</h3>
    </div>
</x-guest-layout>
 -->
