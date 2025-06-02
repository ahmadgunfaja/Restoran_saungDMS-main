@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Pembayaran Order #{{ $order->id }}</h2>
    <p>Total: <strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong></p>
    <p>Silakan selesaikan pembayaran melalui channel yang tersedia di bawah ini:</p>
    @if($payment_url)
        <a href="{{ $payment_url }}" target="_blank" class="btn btn-primary">Bayar Sekarang</a>
    @else
        <div class="alert alert-warning">Gagal mendapatkan link pembayaran Tripay.</div>
    @endif
</div>
@endsection
