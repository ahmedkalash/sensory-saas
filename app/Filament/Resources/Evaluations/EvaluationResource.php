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
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'التقييمات';

    protected static ?string $modelLabel = 'تقييم';

    protected static ?string $pluralModelLabel = 'التقييمات';

    public static function form(Schema $schema): Schema
    {
        /** @var Collection $measurements */
        $measurements = Measurement::with('dimensions.questions')->get();

        $wizardSteps = [];

        $wizardSteps[] = Step::make('Evaluation Details')
            ->label('تحديد المقياس وبيانات المريض')
            ->schema([
                TextInput::make('title')
                    ->label('عنوان التقييم')
                    ->placeholder('مثل: تقييم مبدئي، متابعة بعد 3 أشهر')
                    ->required()
                    ->string()
                    ->maxLength(255),
                Select::make('selected_scale')
                    ->label('تحديد المقياس المنفذ')
                    ->options($measurements->pluck('name', 'id'))
                    ->required()
                    ->live()
                    ->dehydrated(false) // We don't save this to the DB, it's just for UI state
                    ->helperText('اختر المقياس الذي ترغب في تقييم الطفل عليه الآن.'),
                Select::make('patient_id')
                    ->label('الطفل')
                    ->relationship('patient', 'name')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('اسم الطفل')
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
            $totalQuestionsForScale = $measurement->dimensions->sum(fn($d) => $d->questions->count());
            $questionIndex = 1;

            foreach ($measurement->dimensions as $dimension) {
                foreach ($dimension->questions as $question) {
                    $wizardSteps[] = Step::make("q_{$question->id}")
                        ->label("Q{$questionIndex}")
                        ->id("q_{$question->id}")
                        ->icon(Heroicon::QuestionMarkCircle)
                        ->completedIcon(Heroicon::HandThumbUp)
                        ->schema([
                            TextEntry::make("progress_{$question->id}")
                                ->hiddenLabel()
                                ->state(new HtmlString("<div class='evaluation-wizard-progress'>السؤال {$questionIndex} من {$totalQuestionsForScale}</div>")),
                            TextEntry::make("context_{$question->id}")
                                ->label("{$measurement->name} - {$dimension->name}"),
                            Radio::make("draft_answers.{$question->id}")
                                ->label($question->q_text)
                                ->columns(4)
                                ->options([
                                    Score::Never->value => Score::Never->label(),
                                    Score::Sometimes->value => Score::Sometimes->label(), 
                                    Score::Often->value => Score::Often->label(),
                                    Score::Always->value => Score::Always->label(),
                                ])
                                ->dehydrated(false)
                                ->required(),
                            RichEditor::make("draft_notes.{$question->id}")
                                ->label('ملاحظات (اختياري)')
                                ->dehydrated(false)
                                ->columnSpanFull(),
                        ])
                        ->visible(fn(Get $get) => $get('selected_scale') == $measurement->id);
                    $questionIndex++;
                }
            }
        }

        return $schema
            ->components([
                View::make('filament.components.evaluation-wizard-css'),
                View::make('filament.components.no-copy'),
                Wizard::make($wizardSteps)
                    ->view('filament.components.custom-wizard')
                    ->skippable()
                    ->persistStepInQueryString()
                    ->columnSpanFull(),
            ]);
    }

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
                    ->label('عمر الطفل')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('parent_report')
                    ->label('تقرير ولي الأمر')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->modalSubmitActionLabel('عرض')
                    ->schema(fn(Evaluation $record) => [
                        Select::make('report_type')
                            ->label('نوع التقرير')
                            ->options(function () use ($record) {
                                $options = ['all' => 'التقرير الشامل'];
                                $evaluatedIds = $record->answers()->with('question.dimension.measurement')->get()->pluck('question.dimension.measurement_id')->unique();
                                $measurements = Measurement::whereIn('id', $evaluatedIds)->pluck('name', 'id');
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
                    ->schema(fn(Evaluation $record) => [
                        Select::make('report_type')
                            ->label('نوع التقرير')
                            ->options(function () use ($record) {
                                $options = ['all' => 'التقرير الشامل'];
                                $evaluatedIds = $record->answers()->with('question.dimension.measurement')->get()->pluck('question.dimension.measurement_id')->unique();
                                $measurements = Measurement::whereIn('id', $evaluatedIds)->pluck('name', 'id');
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
                    ->schema(fn(Evaluation $record) => [
                        Select::make('report_type')
                            ->label('نوع التقرير')
                            ->options(function () use ($record) {
                                $options = ['all' => 'التقرير الشامل'];
                                $evaluatedIds = $record->answers()->with('question.dimension.measurement')->get()->pluck('question.dimension.measurement_id')->unique();
                                $measurements = Measurement::whereIn('id', $evaluatedIds)->pluck('name', 'id');
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
                ViewAction::make()->label(''),
                EditAction::make()->label(''),
                DeleteAction::make()->label(''),
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
