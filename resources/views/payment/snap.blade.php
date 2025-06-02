@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Pembayaran Order #{{ $order->id }}</h2>
    <p>Total: <strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong></p>
    <button id="pay-button" class="btn btn-primary">Bayar Sekarang</button>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script type="text/javascript">
    document.getElementById('pay-button').onclick = function () {
        window.snap.pay("{{ $snapToken }}", {
            onSuccess: function(result){
                window.location.href = "{{ route('payment.success') }}";
            },
            onPending: function(result){
                window.location.href = "{{ route('payment.success') }}";
            },
            onError: function(result){
                window.location.href = "{{ route('payment.failed') }}";
            },
            onClose: function(){
                alert('Transaksi belum selesai.');
            }
        });
    };
</script>
@endsection
