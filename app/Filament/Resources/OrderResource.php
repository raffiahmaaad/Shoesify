<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $recordTitleAttribute = 'order_number';

    protected static null|string|\BackedEnum $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static null|string|\UnitEnum $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('Status Pesanan')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'processing' => 'Processing',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required(),
                    Forms\Components\Select::make('payment_status')
                        ->options([
                            'unpaid' => 'Unpaid',
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                            'refunded' => 'Refunded',
                        ])
                        ->required(),
                    Forms\Components\DateTimePicker::make('paid_at')
                        ->label('Dibayar pada')
                        ->seconds(false),
                    Forms\Components\TextInput::make('tracking_number')
                        ->label('Nomor pelacakan')
                        ->maxLength(120),
                ]),
            Section::make('Pengiriman')
                ->schema([
                    Forms\Components\TextInput::make('shipping_service')
                        ->label('Layanan pengiriman')
                        ->maxLength(120),
                    Forms\Components\Placeholder::make('shipping_address')
                        ->label('Alamat pengiriman')
                        ->content(function (?Order $record): string {
                            if (! $record) {
                                return '';
                            }

                            $shipping = $record->shipping_address ?? [];
                            return collect([
                                $shipping['recipient_name'] ?? null,
                                $shipping['phone'] ?? null,
                                $shipping['address_line1'] ?? null,
                                $shipping['address_line2'] ?? null,
                                collect([$shipping['city'] ?? null, $shipping['province'] ?? null, $shipping['postal_code'] ?? null])
                                    ->filter()
                                    ->implode(', '),
                                $shipping['country'] ?? null,
                            ])
                                ->filter()
                                ->implode(PHP_EOL);
                        })
                        ->columnSpanFull(),
                ])
                ->columnSpan(1),
            Section::make('Catatan')
                ->schema([
                    Forms\Components\Textarea::make('notes')
                        ->rows(4)
                        ->label('Catatan internal'),
                ])
                ->columnSpan(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Nomor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary',
                        'warning' => 'processing',
                        'info' => 'shipped',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state) => Str::headline($state)),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->colors([
                        'secondary',
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => ['failed', 'refunded'],
                    ])
                    ->formatStateUsing(fn (string $state) => Str::headline($state)),
                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Total (Rp)')
                    ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Dibuat')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
                Tables\Filters\Filter::make('created_today')
                    ->label('Hari ini')
                    ->query(fn ($query) => $query->whereDate('created_at', today())),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
