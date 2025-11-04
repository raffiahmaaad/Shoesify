<?php

declare(strict_types=1);

namespace App\Services\Payments;

use Illuminate\Support\Str;

class MidtransGateway
{
    /**
     * @param  array{
     *     order_id: string,
     *     amount: int,
     *     customer: array<string, mixed>
     * }  $payload
     *
     * @return array{token: string, redirect_url: string, sandbox_url: string}
     */
    public function createTransaction(array $payload): array
    {
        $token = 'SNAP-' . strtoupper(Str::random(32));

        return [
            'token' => $token,
            'redirect_url' => 'https://app.midtrans.com/snap/v2/vtweb/' . $token,
            'sandbox_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $token,
        ];
    }
}
