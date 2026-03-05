<?php

namespace App\Filament\Resources\Evaluations;

use App\Enums\Score;
use App\Models\Evaluation;
use App\Models\Measurement;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'التقييمات';

    protected static ?string $modelLabel = 'تقييم';

    protected static ?string $pluralModelLabel = 'التقييمات';

    public static function form(Schema $schema): Schema
    {
        $measurements = Measurement::with('dimensions.questions')->get();

        $wizardSteps = [];
        $totalQuestions = 0;

        foreach ($measurements as $m) {
            foreach ($m->dimensions as $d) {
                $totalQuestions += $d->questions->count();
            }
        }

        $currentQuestionNo = 1;

        $wizardSteps[] = Step::make('Patient Details')
            ->label('بيانات المريض')
            ->schema([
                Select::make('patient_id')
                    ->label('المريض')
                    ->relationship('patient', 'name')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('اسم المريض')
                            ->required(),
                        DatePicker::make('dob')
                            ->label('تاريخ الميلاد')
                            ->required(),
                        Select::make('gender')
                            ->label('الجنس')
                            ->options(['ذكر' => 'ذكر', 'أنثى' => 'أنثى'])
                            ->required(),
                        TextInput::make('school')
                            ->label('المدرسة/المركز'),
                        TextInput::make('grade')
                            ->label('الصف الدراسي'),
                    ])
                    ->preload()
                    ->searchable()
                    ->required(),
                TextInput::make('specialist_name')
                    ->label('اسم الأخصائي'),
                DatePicker::make('evaluation_date')
                    ->label('تاريخ التقييم')
                    ->default(now())
                    ->required(),
                TextInput::make('child_age')
                    ->label('عمر المريض وقت التقييم'),
            ]);

        foreach ($measurements as $measurement) {
            foreach ($measurement->dimensions as $dimension) {
                foreach ($dimension->questions as $question) {
                    $wizardSteps[] = Step::make("q_{$question->id}")
                        ->label("Q{$question->id}")
                        ->schema([
                            Placeholder::make("context_{$question->id}")
                                ->label('المقياس / البعد')
                                ->content("{$measurement->name} - {$dimension->name}"),

                            Radio::make("draft_answers.{$question->id}")
                                ->label($question->q_text)
                                ->options([
                                    Score::Never->value => 'لايوجد',
                                    Score::Sometimes->value => 'أحيانا',
                                    Score::Often->value => 'غالبا',
                                    Score::Always->value => 'موجود دائما',
                                ])
                                ->required(),
                        ]);
                    $currentQuestionNo++;
                }
            }
        }

        return $schema
            ->components([
                View::make('filament.components.evaluation-wizard-css'),
                View::make('filament.components.no-copy'),
                Wizard::make($wizardSteps)
                    ->skippable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.name')
                    ->label('اسم المريض')
                    ->searchable(),
                TextColumn::make('evaluation_date')
                    ->label('تاريخ التقييم')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('specialist_name')
                    ->label('الأخصائي')
                    ->searchable(),
                TextColumn::make('child_age')
                    ->label('عمر المريض'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('download_report')
                    ->label('تحميل التقرير')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (Evaluation $record): string => route('evaluations.report', $record))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
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
