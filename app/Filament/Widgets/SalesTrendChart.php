<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesTrendChart extends ChartWidget
{
    protected ?string $heading = 'Sales Trend (30 days)';

    protected ?string $pollingInterval = '120s';

    protected function getData(): array
    {
        $start = Carbon::now()->subDays(29)->startOfDay();

        $data = Order::query()
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, SUM(grand_total) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->all();

        $labels = [];
        $totals = [];

        for ($date = $start->copy(); $date <= Carbon::now(); $date->addDay()) {
            $labels[] = $date->format('d M');
            $totals[] = (int) ($data[$date->toDateString()] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $totals,
                    'borderColor' => '#4de4d4',
                    'backgroundColor' => 'rgba(77, 228, 212, 0.25)',
                    'tension' => 0.4,
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
