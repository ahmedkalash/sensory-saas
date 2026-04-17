<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('subscription.plan.name') //todo: show 'none' id not hasActiveSubscription
                    ->label('الاشتراك الحالي')
                    ->badge()
                    ->color(fn (\App\Models\User $record) => $record->hasActiveSubscription() ? 'success' : 'danger')
                    ->default('لا يوجد'),

                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الانضمام')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
