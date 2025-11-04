<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static null|string|\BackedEnum $navigationIcon = 'heroicon-o-user-group';

    protected static null|string|\UnitEnum $navigationGroup = 'Accounts';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Forms\Components\Section::make('Profile')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(30),
                    Forms\Components\Select::make('gender')
                        ->options([
                            'male' => 'Male',
                            'female' => 'Female',
                            'non-binary' => 'Non-binary',
                            'prefer_not_to_say' => 'Prefer not to say',
                        ])
                        ->searchable(),
                    Forms\Components\DatePicker::make('birth_date'),
                    Forms\Components\FileUpload::make('avatar_path')
                        ->label('Avatar')
                        ->disk('public')
                        ->directory('avatars')
                        ->image()
                        ->imageEditor()
                        ->maxSize(2048),
                ])
                ->columnSpan(1),
            Forms\Components\Section::make('Bio')
                ->schema([
                    Forms\Components\Textarea::make('bio')
                        ->rows(6)
                        ->maxLength(500),
                    Forms\Components\DateTimePicker::make('email_verified_at')
                        ->label('Email verified at')
                        ->seconds(false)
                        ->helperText('Kosongkan untuk menandai sebagai belum terverifikasi.'),
                ])
                ->columnSpan(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_path')
                    ->label('Avatar')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=Shoesify&color=FFFFFF&background=016b61'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Orders')
                    ->sortable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email verified'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('orders');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
