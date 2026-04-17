<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->sortable(),

                TextColumn::make('subscription.plan.name')
                    ->label('الاشتراك الحالي')
                    ->badge()
                    ->state(fn (User $record) => $record->hasActiveSubscription() ? $record->subscription?->plan?->name : 'لا يوجد')
                    ->color(fn (User $record) => $record->hasActiveSubscription() ? 'success' : 'danger'),

                TextColumn::make('created_at')
                    ->label('تاريخ الانضمام')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalHeading('حذف المستخدمين المحددين')
                        ->modalDescription('سيتم حذف المستخدمين المحددين وجميع اشتراكاتهم بشكل نهائي ولا يمكن التراجع عن هذا الإجراء.')
                        ->modalSubmitActionLabel('نعم، احذف'),
                ]),
            ]);
    }
}
