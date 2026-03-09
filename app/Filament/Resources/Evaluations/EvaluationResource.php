<?php

namespace App\Filament\Resources\Evaluations;

use App\Models\Evaluation;
use App\Models\Measurement;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'التقييمات';

    protected static ?string $modelLabel = 'تقييم';

    protected static ?string $pluralModelLabel = 'التقييمات';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('عنوان التقييم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('patient.name')
                    ->label('اسم الطفل')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('evaluation_date')
                    ->label('تاريخ التقييم')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('specialist_name')
                    ->label('الأخصائي')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('child_age')
                    ->label('عمر المريض وقت التقييم')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->label('تعديل'),
                    Action::make('parent_report')
                        ->label('تقرير ولي الأمر')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->modalSubmitActionLabel('عرض')
                        ->schema(fn (Evaluation $record) => [
                            Select::make('report_type')
                                ->label('نوع التقرير')
                                ->options(function () use ($record) {
                                    $options = ['all' => 'التقرير الشامل'];
                                    $measurementNames = $record->answers()->pluck('measurement_name')->unique();
                                    $measurements = Measurement::whereIn('name', $measurementNames)->pluck('name', 'id');
                                    foreach ($measurements as $id => $name) {
                                        $options[$id] = "تقرير {$name}";
                                    }

                                    return $options;
                                })
                                ->default('all')
                                ->required(),
                        ])
                        ->action(function (array $data, Evaluation $record) {
                            $params = [];
                            if ($data['report_type'] !== 'all') {
                                $params['measurement_id'] = $data['report_type'];
                            }

                            return redirect()->route('evaluations.parent_report', array_merge(['evaluation' => $record], $params));
                        })
                        ->openUrlInNewTab(),
                    Action::make('htmlReport')
                        ->label('عرض التقرير')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->modalSubmitActionLabel('عرض')
                        ->schema(fn (Evaluation $record) => [
                            Select::make('report_type')
                                ->label('نوع التقرير')
                                ->options(function () use ($record) {
                                    $options = ['all' => 'التقرير الشامل'];
                                    $measurementNames = $record->answers()->pluck('measurement_name')->unique();
                                    $measurements = Measurement::whereIn('name', $measurementNames)->pluck('name', 'id');
                                    foreach ($measurements as $id => $name) {
                                        $options[$id] = "تقرير {$name}";
                                    }

                                    return $options;
                                })
                                ->default('all')
                                ->required(),
                        ])
                        ->action(function (array $data, Evaluation $record) {
                            $params = [];
                            if ($data['report_type'] !== 'all') {
                                $params['measurement_id'] = $data['report_type'];
                            }

                            return redirect()->route('evaluations.report.html', array_merge(['evaluation' => $record], $params));
                        })
                        ->openUrlInNewTab(),
                    Action::make('downloadReport')
                        ->label('تحميل التقرير')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->modalSubmitActionLabel('تحميل')
                        ->schema(fn (Evaluation $record) => [
                            Select::make('report_type')
                                ->label('نوع التقرير')
                                ->options(function () use ($record) {
                                    $options = ['all' => 'التقرير الشامل'];
                                    $measurementNames = $record->answers()->pluck('measurement_name')->unique();
                                    $measurements = Measurement::whereIn('name', $measurementNames)->pluck('name', 'id');
                                    foreach ($measurements as $id => $name) {
                                        $options[$id] = "تقرير {$name}";
                                    }

                                    return $options;
                                })
                                ->default('all')
                                ->required(),
                        ])
                        ->action(function (array $data, Evaluation $record) {
                            $params = [];
                            if ($data['report_type'] !== 'all') {
                                $params['measurement_id'] = $data['report_type'];
                            }

                            return redirect()->route('evaluations.report', array_merge(['evaluation' => $record], $params));
                        }),
                    DeleteAction::make()->label('حذف'),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('الإجراءات'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluation::route('/create'),
            'edit' => Pages\EditEvaluation::route('/{record}/edit'),
        ];
    }
}
