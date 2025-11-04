<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockProducts extends BaseWidget
{
    protected static ?string $heading = 'Low Stock Products';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): ?Builder
    {
        return Product::query()
            ->with(['brand'])
            ->withSum('variants as stock_sum', 'stock_quantity')
            ->havingRaw('COALESCE(stock_sum, 0) < ?', [config('inventory.low_stock_threshold', 15)])
            ->orderBy('stock_sum');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Product')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brand')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('stock_sum')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (?int $state): string => $state === null || $state <= 0 ? 'danger' : ($state < 10 ? 'warning' : 'success')),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->toggleable(),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (Product $record) => route('filament.admin.resources.products.edit', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
