<?php

namespace App\Filament\Resources\Questions\Tables;

use App\Models\Measurement;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width('60px'),
                TextColumn::make('q_text')
                    ->label('نص السؤال')
                    ->searchable()
                    ->limit(80)
                    ->wrap(),
                TextColumn::make('dimension.measurement.name')
                    ->label('المقياس')
                    ->sortable()
                    ->badge(),
                TextColumn::make('dimension.name')
                    ->label('البُعد')
                    ->sortable()
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort('dimension.measurement.name')
            ->filters([
                SelectFilter::make('measurement')
                    ->label('المقياس')
                    ->options(Measurement::pluck('name', 'id'))
                    ->query(fn ($query, $data) => $data['value']
                        ? $query->whereHas('dimension.measurement', fn ($q) => $q->where('measurements.id', $data['value']))
                        : $query),
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
