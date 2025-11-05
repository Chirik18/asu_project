<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Користувачі';
    protected static ?string $pluralLabel = 'Користувачі';
    protected static ?string $modelLabel = 'Користувач';

    // --- Форма створення/редагування користувача ---
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Ім’я')
                    ->required(),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),

                TextInput::make('password')
                    ->label('Пароль')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),

                Toggle::make('is_active')
                    ->label('Активний користувач')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(false),

                // --- Вибір ролі користувача ---
                Select::make('role')
                    ->label('Роль')
                    ->options([
                        'admin' => 'Адмін',
                        'user' => 'Користувач',
                        'moderator' => 'Модератор',
                    ])
                    ->default('user')
                    ->required(),
            ]);
    }

    // --- Таблиця користувачів в адмінці ---
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Ім’я')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                BooleanColumn::make('is_active')
                    ->label('Активний'),

                // --- Колонка ролі з кольоровим бейджем ---
                TextColumn::make('role')
                    ->label('Роль')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'moderator' => 'warning',
                        'user' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Редагувати'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Видалити'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    // --- Сторінки ресурсу ---
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
