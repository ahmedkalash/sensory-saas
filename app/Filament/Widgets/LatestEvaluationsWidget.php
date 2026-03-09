<?php

namespace App\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestEvaluationsWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'آخر التقييمات المُنفذة';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Evaluation::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('عنوان التقييم')
                    ->searchable(),
                TextColumn::make('patient.name')
                    ->label('اسم الطفل'),
                TextColumn::make('evaluation_date')
                    ->label('التاريخ')
                    ->date('Y-m-d'),
                TextColumn::make('specialist_name')
                    ->label('الأخصائي'),
            ])
            ->actions([
                Action::make('view')
                    ->label('عرض')
                    ->icon('heroicon-m-eye')
                    ->url(fn (\App\Models\Evaluation $record): string => \App\Filament\Resources\Evaluations\EvaluationResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
