<?php

namespace App\Filament\Resources\Plans\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم الخطة')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('السعر')
                    ->suffix(' ج.م')
                    ->numeric(locale: 'en')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->sortable(),

                TextColumn::make('duration_days')
                    ->label('المدة (أيام)')
                    ->numeric(locale: 'en')
                    ->sortable(),

                TextColumn::make('quota_count')
                    ->label('التقييمات')
                    ->numeric(locale: 'en')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('مفعل')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('تاريخ الاضافة')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([

            ]);
    }
}
