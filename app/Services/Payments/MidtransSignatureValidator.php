<?php

declare(strict_types=1);

namespace App\Services\Payments;

class MidtransSignatureValidator
{
    private ?string $serverKey;

    public function __construct(?string $serverKey = null)
    {
        $this->serverKey = $serverKey ?? config('services.midtrans.server_key');
    }

    public function isValid(array $payload, ?string $signature): bool
    {
        if (! $this->serverKey) {
            // Jika server key belum dikonfigurasi (mis. lingkungan pengembangan), abaikan validasi.
            return true;
        }

        if (! $signature) {
            return false;
        }

        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = isset($payload['gross_amount']) ? (string) $payload['gross_amount'] : '';

        $hashed = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        return hash_equals($hashed, (string) $signature);
    }
}
