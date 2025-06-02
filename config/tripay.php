<?php
return [
  'client_id' => env('TRIPAY_CLIENT_ID', 'your_client_id'),
  'client_secret' => env('TRIPAY_CLIENT_SECRET', 'your_client_secret'),
  'merchant_code' => env('TRIPAY_MERCHANT_CODE', 'your_merchant_code'),
  'api_url' => env('TRIPAY_API_URL', 'https://tripay.co.id/api/'),
  'callback_url' => env('TRIPAY_CALLBACK_URL', 'https://yourdomain.com/callback'),
  'success_url' => env('TRIPAY_SUCCESS_URL', 'https://yourdomain.com/success'),
  'error_url' => env('TRIPAY_ERROR_URL', 'https://yourdomain.com/error'),
  'private_key' => env('TRIPAY_PRIVATE_KEY', 'your_private_key'),
];
// Ensure you have the necessary environment variables set in your .env file