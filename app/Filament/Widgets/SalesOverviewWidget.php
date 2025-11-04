<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SalesOverviewWidget extends BaseWidget
{
    protected ?string $pollingInterval = '60s';

    protected function getCards(): array
    {
        $totalRevenue = Order::query()
            ->where('payment_status', 'paid')
            ->sum('grand_total');

        $ordersThisWeek = Order::query()
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        $pendingOrders = Order::query()
            ->where('status', 'pending')
            ->count();

        $averageOrderValue = $ordersThisWeek > 0
            ? Order::query()
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->avg('grand_total')
            : 0;

        return [
            Stat::make('Total Revenue', 'Rp ' . number_format((int) $totalRevenue, 0, ',', '.'))
                ->description('Paid orders (all time)')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Orders This Week', number_format($ordersThisWeek))
                ->description('New orders in current week')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Pending Orders', number_format($pendingOrders))
                ->description('Require follow-up')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'success'),

            Stat::make('Average Order Value', 'Rp ' . number_format((int) $averageOrderValue, 0, ',', '.'))
                ->description('Orders created this week')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }
}
