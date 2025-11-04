<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static null|string|\BackedEnum $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static null|string|\UnitEnum $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Forms\Components\Select::make('product_id')
                ->relationship('product', 'name')
                ->required()
                ->searchable()
                ->preload()
                ->columnSpan(1),
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->label('Reviewer')
                ->required()
                ->searchable()
                ->preload()
                ->columnSpan(1),
            Forms\Components\TextInput::make('rating')
                ->numeric()
                ->minValue(1)
                ->maxValue(5)
                ->step(0.5)
                ->required()
                ->label('Rating (1-5)'),
            Forms\Components\TextInput::make('title')
                ->maxLength(120)
                ->required(),
            Forms\Components\Textarea::make('content')
                ->rows(5)
                ->required()
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_verified_purchase')
                ->label('Verified purchase'),
            Forms\Components\Toggle::make('is_active')
                ->label('Visible on site')
                ->default(true),
            Forms\Components\TextInput::make('helpful_count')
                ->numeric()
                ->minValue(0)
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->limit(30)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Reviewer')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric(decimalPlaces: 1)
                    ->label('Rating')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_verified_purchase')
                    ->label('Verified')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Visible')
                    ->boolean(),
                Tables\Columns\TextColumn::make('helpful_count')
                    ->label('Helpful')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Visible'),
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        '5' => '5 stars',
                        '4' => '4 stars',
                        '3' => '3 stars',
                        '2' => '2 stars',
                        '1' => '1 star',
                    ]),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
