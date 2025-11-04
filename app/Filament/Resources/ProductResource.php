<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static null|string|\BackedEnum $navigationIcon = 'heroicon-o-shopping-bag';

    protected static null|string|\UnitEnum $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            ComponentsSection::make('Informasi Produk')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->alphaDash()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            Forms\Components\TextInput::make('sku')
                                ->required()
                                ->maxLength(64)
                                ->unique(ignoreRecord: true),
                        ]),
                    Forms\Components\Select::make('category_id')
                        ->relationship('category', 'name')
                        ->label('Category')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('brand_id')
                        ->relationship('brand', 'name')
                        ->label('Brand')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('price')
                                ->numeric()
                                ->minValue(0)
                                ->required(),
                            Forms\Components\TextInput::make('compare_price')
                                ->label('Compare at Price')
                                ->numeric()
                                ->minValue(0),
                            Forms\Components\TextInput::make('discount')
                                ->numeric()
                                ->suffix('%')
                                ->minValue(0)
                                ->maxValue(90)
                                ->default(0),
                        ]),
                    Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('rating')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(5)
                                ->step(0.1)
                                ->default(0),
                            Forms\Components\TextInput::make('reviews')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            Forms\Components\DatePicker::make('release_date')
                                ->label('Release Date'),
                        ]),
                    Forms\Components\RichEditor::make('description')
                        ->columnSpanFull()
                        ->required()
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link', 'blockquote'
                        ]),
                    Forms\Components\Textarea::make('short_description')
                        ->maxLength(255),
                    Forms\Components\TagsInput::make('images')
                        ->placeholder('Tambah URL gambar dan tekan Enter')
                        ->suggestions(fn () => []),
                ])
                ->collapsible()
                ->columns(2),
            ComponentsSection::make('Pengaturan Tambahan')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->default(true)
                        ->label('Active'),
                    Forms\Components\Toggle::make('is_featured')
                        ->label('Featured'),
                    Forms\Components\TextInput::make('meta_title')
                        ->label('Meta Title')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('meta_description')
                        ->label('Meta Description')
                        ->rows(3)
                        ->maxLength(500),
                ])
                ->columns(2),
            ComponentsSection::make('Variants')
                ->schema([
                    Forms\Components\Repeater::make('variants')
                        ->relationship()
                        ->schema([
                            Forms\Components\TextInput::make('sku')
                                ->required()
                                ->maxLength(64),
                            Forms\Components\TextInput::make('size')
                                ->maxLength(50),
                            Forms\Components\TextInput::make('color_name')
                                ->label('Color Name')
                                ->maxLength(50),
                            Forms\Components\ColorPicker::make('color_hex')
                                ->label('Color Hex'),
                            Forms\Components\TextInput::make('stock_quantity')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            Forms\Components\TextInput::make('price_adjustment')
                                ->numeric()
                                ->minValue(-500)
                                ->default(0),
                            Forms\Components\TagsInput::make('images')
                                ->placeholder('URL gambar (opsional)'),
                        ])
                        ->defaultItems(0)
                        ->collapsible()
                        ->grid(2),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('Preview')
                    ->getStateUsing(fn ($record) => $record->images[0] ?? null)
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(36),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brand')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->since()
                    ->label('Updated'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
                Tables\Filters\SelectFilter::make('brand')->relationship('brand', 'name'),
                Tables\Filters\SelectFilter::make('category')->relationship('category', 'name'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                Actions\ActionGroup::make([
                    Actions\EditAction::make(),
                    Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['brand', 'category']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
