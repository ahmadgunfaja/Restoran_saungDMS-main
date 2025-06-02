<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TripayService
{
  protected $apiKey;
  protected $privateKey;
  protected $merchantCode;
  protected $baseUrl;

  public function __construct()
  {
    $this->apiKey = config('services.tripay.api_key');
    $this->privateKey = config('services.tripay.private_key');
    $this->merchantCode = config('services.tripay.merchant_code');
    $this->baseUrl = 'https://tripay.co.id/api-sandbox';
  }

  public function getPaymentChannels()
  {
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $this->apiKey,
    ])->get($this->baseUrl . '/merchant/payment-channel');

    if ($response->successful()) {
      return $response->json();
    }
    return null;
  }

  public function createTransaction($payload)
  {
    // Tripay expects x-www-form-urlencoded, not JSON
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $this->apiKey,
    ])->asForm()->post($this->baseUrl . '/transaction/create', $payload);
    return $response->json();
  }

  public function getTransactionDetail($reference)
  {
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $this->apiKey,
    ])->get($this->baseUrl . '/transaction/detail', [
      'reference' => $reference
    ]);
    return $response->json();
  }

  public function checkTransactionStatus($reference)
  {
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $this->apiKey,
    ])->get($this->baseUrl . '/transaction/check', [
      'reference' => $reference
    ]);
    return $response->json();
  }

  public function calculateFee($amount, $code)
  {
    if (!is_numeric($amount) || $amount <= 0) {
      throw new \InvalidArgumentException('Amount must be a positive number.');
    }
    if (empty($code)) {
      throw new \InvalidArgumentException('Code cannot be empty.');
    }

    Log::info('Calculating fee for amount: ' . $amount . ' with code: ' . $code);
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $this->apiKey,
    ])->get($this->baseUrl . '/merchant/fee-calculator', [
      'amount' => $amount,
      'code' => $code,
    ]);
    Log::info('Fee calculation response: ' . $response->body());
    if (!$response->successful()) {
      Log::error('Fee calculation failed: ' . $response->body());
      throw new \Exception('Failed to calculate fee: ' . $response->body());
    }
    $data = $response->json()['data'] ?? [];
    if (!is_array($data) || count($data) === 0) {
      Log::error('Fee calculation: data array empty');
      throw new \Exception('Fee calculation: data array empty');
    }
    $totalFee = $data[0]['total_fee']['merchant'] ?? 0;
    Log::info('Total fee (merchant) calculated: ' . $totalFee);
    if (!is_numeric($totalFee)) {
      Log::error('Invalid fee total: ' . $totalFee);
      throw new \Exception('Invalid fee total received from Tripay API.');
    }
    return $amount + $totalFee;
  }

  public function calculateTotalWithFee($amount, $code)
  {
    $fee = $this->calculateFee($amount, $code);
    return $amount + $fee;
  }

  public function makeSignature($merchant_ref, $amount)
  {
    return hash_hmac('sha256', $this->merchantCode . $merchant_ref . $amount, $this->privateKey);
  }
}
