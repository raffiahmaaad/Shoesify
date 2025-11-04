<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payments\MidtransSignatureValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MidtransWebhookController extends Controller
{
    public function __invoke(Request $request, MidtransSignatureValidator $validator): JsonResponse
    {
        $payload = $request->all();
        $signature = $request->header('X-Midtrans-Signature') ?? Arr::get($payload, 'signature_key', '');

        if (! $validator->isValid($payload, (string) $signature)) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $orderNumber = Arr::get($payload, 'order_id');

        if (! $orderNumber) {
            return response()->json(['message' => 'Missing order_id'], 422);
        }

        $order = Order::query()->where('order_number', $orderNumber)->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $transactionStatus = Arr::get($payload, 'transaction_status');
        $fraudStatus = Arr::get($payload, 'fraud_status');

        $updates = [
            'payment_payload' => array_merge($order->payment_payload ?? [], $payload),
        ];

        if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
            $updates['payment_status'] = 'paid';
            $updates['status'] = $transactionStatus === 'settlement' ? 'processing' : $order->status;
            $updates['paid_at'] = now();
            if ($fraudStatus === 'challenge') {
                $updates['payment_status'] = 'pending';
            }
        } elseif ($transactionStatus === 'pending') {
            $updates['payment_status'] = 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'], true)) {
            $updates['payment_status'] = 'failed';
            if ($transactionStatus === 'cancel') {
                $updates['status'] = 'cancelled';
            }
        }

        $order->forceFill($updates)->save();

        return response()->json([
            'message' => 'Webhook processed',
        ]);
    }
}
