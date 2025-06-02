<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TripayPaymentService;

class TripayCallbackController extends Controller
{

    public function handle(Request $request)
    {
        Log::info('Tripay Callback: Masuk ke endpoint callback', ['ip' => $request->ip()]);
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE');
        $json = $request->getContent();
        $signature = hash_hmac('sha256', $json, config('tripay.private_key'));

        Log::info('Tripay Callback: Signature check', [
            'expected_signature' => $signature,
            'received_signature' => $callbackSignature
        ]);

        if ($signature !== (string) $callbackSignature) {
            Log::warning('Tripay Callback: Invalid signature', [
                'expected' => $signature,
                'received' => $callbackSignature
            ]);
            return Response::json([
                'success' => false,
                'message' => 'Invalid signature',
            ]);
        }

        if ('payment_status' !== (string) $request->server('HTTP_X_CALLBACK_EVENT')) {
            Log::warning('Tripay Callback: Unrecognized callback event', [
                'event' => $request->server('HTTP_X_CALLBACK_EVENT')
            ]);
            return Response::json([
                'success' => false,
                'message' => 'Unrecognized callback event, no action was taken',
            ]);
        }

        $data = json_decode($json);
        Log::info('Tripay Callback: Payload decoded', ['payload' => $data]);

        if (JSON_ERROR_NONE !== json_last_error()) {
            Log::error('Tripay Callback: Invalid JSON', ['error' => json_last_error_msg()]);
            return Response::json([
                'success' => false,
                'message' => 'Invalid data sent by tripay',
            ]);
        }

        $service = new TripayPaymentService();
        $result = $service->handleCallback($data);
        if ($result) {
            return Response::json(['success' => true]);
        } else {
            return Response::json([
                'success' => false,
                'message' => 'Failed to process callback',
            ]);
        }
    }

    public function webhook(Request $request)
    {
        Log::info('Tripay Webhook: Masuk ke endpoint webhook', ['ip' => $request->ip()]);
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE');
        $json = $request->getContent();
        $signature = hash_hmac('sha256', $json, config('tripay.private_key'));

        Log::info('Tripay Webhook: Signature check', [
            'expected_signature' => $signature,
            'received_signature' => $callbackSignature
        ]);

        if ($signature !== (string) $callbackSignature) {
            Log::warning('Tripay Webhook: Invalid signature', [
                'expected' => $signature,
                'received' => $callbackSignature
            ]);
            return Response::json([
                'success' => false,
                'message' => 'Invalid signature',
            ]);
        }

        Log::info('Tripay Webhook: Payload received', ['payload' => json_decode($json, true)]);
        return Response::json(['success' => true]);
    }
}
