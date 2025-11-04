<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static null|string|\BackedEnum $navigationIcon = 'heroicon-o-ticket';

    protected static null|string|\UnitEnum $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Kupon')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->required()
                        ->maxLength(32)
                        ->formatStateUsing(fn (?string $state) => Str::upper((string) $state))
                        ->dehydrateStateUsing(fn (?string $state) => Str::upper((string) $state))
                        ->unique(ignoreRecord: true),
                    Forms\Components\Select::make('type')
                        ->required()
                        ->options([
                            'percent' => 'Diskon Persentase',
                            'fixed' => 'Diskon Nominal',
                            'free_shipping' => 'Gratis Ongkir',
                        ]),
                    Forms\Components\TextInput::make('value')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->suffix(fn (callable $get) => $get('type') === 'percent' ? '%' : 'Rp'),
                    Forms\Components\TextInput::make('max_discount')
                        ->numeric()
                        ->minValue(0)
                        ->label('Maksimal Diskon (Rp)')
                        ->helperText('Opsional. Berlaku untuk kupon persen / gratis ongkir'),
                    Forms\Components\TextInput::make('min_subtotal')
                        ->numeric()
                        ->minValue(0)
                        ->label('Minimal Belanja (Rp)')
                        ->default(0),
                    Forms\Components\TextInput::make('usage_limit')
                        ->numeric()
                        ->minValue(1)
                        ->label('Batas Penggunaan')
                        ->helperText('Kosongkan jika tanpa batas'),
                    Forms\Components\TextInput::make('usage_count')
                        ->numeric()
                        ->label('Telah Digunakan')
                        ->disabled()
                        ->dehydrated(false),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ]),
            Section::make('Periode Kupon')
                ->columns(2)
                ->schema([
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->label('Aktif Mulai'),
                    Forms\Components\DateTimePicker::make('ends_at')
                        ->label('Berakhir')
                        ->after('starts_at'),
                ]),
            Forms\Components\Textarea::make('metadata')
                ->label('Catatan internal')
                ->rows(3)
                ->helperText('Opsional. Gunakan untuk catatan internal tim marketing.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->formatStateUsing(fn (string $state) => Str::upper($state))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state) => Str::headline($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai')
                    ->formatStateUsing(function ($record) {
                        return $record->type === 'percent'
                            ? $record->value . '%'
                            : 'Rp ' . number_format((int) $record->value, 0, ',', '.');
                    }),
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Pemakaian')
                    ->formatStateUsing(function ($record) {
                        if ($record->usage_limit) {
                            return "{$record->usage_count} / {$record->usage_limit}";
                        }

                        return (string) $record->usage_count;
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Aktif'),
                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Status aktif'),
                Tables\Filters\Filter::make('active_now')
                    ->label('Sedang berlaku')
                    ->query(fn ($query) => $query->where(function ($q) {
                        $now = now();
                        $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                    })->where(function ($q) {
                        $now = now();
                        $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                    })),
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
