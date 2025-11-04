<?php

declare(strict_types=1);

namespace App\Services\Shipping;

class ShippingEstimator
{
    /**
     * Estimasi ongkir berbasis berat.
     *
     * @param  array{
     *     destination: string,
     *     postal_code: string,
     *     courier?: string|null,
     *     weight: int
     * }  $payload
     *
     * @return array<int, array{provider: string, service: string, etd: string, cost: int, description: string}>
     */
    public function estimate(array $payload): array
    {
        $courier = strtoupper((string) ($payload['courier'] ?? ''));
        $weight = max(1, (int) ceil(($payload['weight'] ?? 0) / 1000)); // kg, minimal 1kg

        $providers = collect([
            [
                'provider' => 'JNE',
                'service' => 'REG',
                'etd' => '2-3 hari',
                'base' => 18000,
                'increment' => 4000,
                'description' => 'Layanan reguler dengan estimasi 2-3 hari kerja.',
            ],
            [
                'provider' => 'TIKI',
                'service' => 'ECO',
                'etd' => '3-5 hari',
                'base' => 15000,
                'increment' => 3000,
                'description' => 'Ekonomis, cocok untuk paket non-urgent.',
            ],
            [
                'provider' => 'POS',
                'service' => 'Kilat Khusus',
                'etd' => '2-4 hari',
                'base' => 16000,
                'increment' => 3500,
                'description' => 'Jangkauan nasional dengan prioritas khusus.',
            ],
            [
                'provider' => 'JNE',
                'service' => 'YES',
                'etd' => '1 hari',
                'base' => 28000,
                'increment' => 6000,
                'description' => 'Yakin Esok Sampai untuk kebutuhan mendesak.',
            ],
        ]);

        if ($courier !== '') {
            $providers = $providers->filter(fn (array $provider): bool => $provider['provider'] === $courier);
        }

        return $providers
            ->map(function (array $provider) use ($weight): array {
                $cost = $provider['base'] + max(0, $weight - 1) * $provider['increment'];

                return [
                    'provider' => $provider['provider'],
                    'service' => $provider['service'],
                    'etd' => $provider['etd'],
                    'cost' => $cost,
                    'description' => $provider['description'],
                ];
            })
            ->values()
            ->all();
    }
}
