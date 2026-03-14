<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Enums\Score;
use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Measurement;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class EditEvaluation extends EditRecord
{
    protected static string $resource = EvaluationResource::class;

    protected static ?string $title = 'تعديل التقييم';

    /**
     * Build the report-type select schema for a given evaluation record.
     *
     * @return array<\Filament\Forms\Components\Select>
     */
    private function reportTypeSchema(): array
    {
        $record = $this->record;

        return [
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
        ];
    }

    /**
     * Build route params from the modal form data.
     *
     * @return array<string, mixed>
     */
    private function reportParams(array $data): array
    {
        $params = ['evaluation' => $this->record];
        if ($data['report_type'] !== 'all') {
            $params['measurement_id'] = $data['report_type'];
        }

        return $params;
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('parent_report')
                ->label('تحميل تقرير ولي الأمر')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->modalSubmitActionLabel('تحميل')
                ->schema(fn () => $this->reportTypeSchema())
                ->action(fn (array $data) => redirect()->route('evaluations.parent_report', $this->reportParams($data)))
                ->openUrlInNewTab(),
            Action::make('htmlReport')
                ->label('عرض التقرير')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->modalSubmitActionLabel('عرض')
                ->schema(fn () => $this->reportTypeSchema())
                ->action(function (array $data) {
                    $url = route('evaluations.report.html', $this->reportParams($data));
                    $this->js("window.open('" . $url . "', '_blank')");
                }),
            Action::make('downloadReport')
                ->label('تحميل التقرير')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->modalSubmitActionLabel('تحميل')
                ->schema(fn () => $this->reportTypeSchema())
                ->action(fn (array $data) => redirect()->route('evaluations.report', $this->reportParams($data))),
        ];
    }

    /**
     * Build the wizard for EDITING an existing evaluation.
     * Uses the saved EvaluationAnswer snapshot data so the form shows exactly
     * what was captured — even if the original Questions have since changed.
     */
    public function form(Schema $schema): Schema
    {
        /** @var Collection<EvaluationAnswer> $answers */
        $answers = $this->record->answers()->get();

        /**
         * @var Collection<string,Collection<EvaluationAnswer>> $grouped Group answers by measurement_name → dimension_name
         */
        $grouped = $answers->groupBy('measurement_name');

        // Build measurement options — show ALL scales so the user can navigate to any one
        /** @var Collection<string,int> $measurements */
        $measurements = Measurement::pluck('id', 'name'); // ['name' => id]

        $totalAnswers = $answers->count();

        return $schema->columns(['default' => 1, 'lg' => 3])->components([
            View::make('filament.components.evaluation-wizard-css')->columnSpanFull(),
            View::make('filament.components.no-copy')->columnSpanFull(),
            Section::make('تحديد المقياس وبيانات المريض')
                ->columnSpan(['default' => 3, 'lg' => 1])
                ->schema([
                    TextInput::make('title')
                        ->label('عنوان التقييم')
                        ->placeholder('مثل: تقييم مبدئي، متابعة بعد 3 أشهر')
                        ->required()
                        ->string()
                        ->maxLength(255),
                    Select::make('selected_scale')
                        ->label('تحديد المقياس المنفذ')
                        ->options($measurements->flip()) // [id => name]
                        ->required()
                        ->live()
                        ->dehydrated(false)
                        ->helperText('اختر المقياس الذي تريد مراجعته أو تعديله.'),
                    Select::make('patient_id')
                        ->label('الطفل')
                        ->relationship('patient', 'name')
                        ->preload()
                        ->searchable()
                        ->required()
                        ->disabled(),
                    TextInput::make('specialist_name')
                        ->label('اسم الأخصائي'),
                    DatePicker::make('evaluation_date')
                        ->label('تاريخ التقييم')
                        ->required(),
                    TextInput::make('child_age')
                        ->label('عمر المريض وقت التقييم'),
                ]),

            // LEFT SIDE (Wizard & Loading)
            Group::make([
                View::make('filament.components.loading-overlay'),
                
                Group::make()
                    ->schema(function (Get $get) use ($grouped, $measurements, $totalAnswers) {
                        $selectedScaleId = $get('selected_scale');

                    if (! $selectedScaleId) {
                        return [];
                    }

                    // Find the measurement name mapped to the selected scale ID
                    $measurementName = $measurements->search($selectedScaleId);

                    if (! $measurementName) {
                        return [];
                    }

                    $wizardSteps = [];

                    // If this scale has existing answers, show them (edit mode)
                    if ($grouped->has($measurementName)) {
                        $measurementAnswers = $grouped->get($measurementName)->sortBy('dimension_name');
                        $answerIndex = 1;

                        foreach ($measurementAnswers as $answer) {
                            $answerId = $answer->id;
                            $qText = e($answer->question_text);
                            $contextHtml = "<div class='evaluation-context-badge' style='margin-inline: auto; text-align: center;'>{$measurementName}</div>";
                            $progressHtml = "<div class='evaluation-wizard-progress' style='text-align: center; width: 100%;'>السؤال {$answerIndex} من {$totalAnswers}</div>";
                            $questionHtml = "<h2 style='font-size:1.5rem; font-weight:700; margin:0 0 0.75rem 0; color:#1e293b; text-align: center;'>{$qText}</h2>";

                            $wizardSteps[] = Step::make("a_{$answerId}")
                                ->label("Q{$answerIndex}")
                                ->id("a_{$answerId}")
                                ->icon(fn (Get $get) => $get("draft_answers.{$answerId}") !== null ? Heroicon::HandThumbUp : Heroicon::QuestionMarkCircle)
                                ->completedIcon(Heroicon::HandThumbUp)
                                ->schema([
                                    TextEntry::make("progress_{$answerId}")
                                        ->hiddenLabel()
                                        ->alignCenter()
                                        ->state(new HtmlString($progressHtml)),
                                    TextEntry::make("context_{$answerId}")
                                        ->hiddenLabel()
                                        ->alignCenter()
                                        ->state(new HtmlString($contextHtml)),
                                    TextEntry::make("question_text_{$answerId}")
                                        ->hiddenLabel()
                                        ->alignCenter()
                                        ->state(new HtmlString($questionHtml)),
                                    ToggleButtons::make("draft_answers.{$answerId}")
                                        ->hiddenLabel()
                                        ->inline()
                                        ->options([
                                            Score::Never->value => Score::Never->label(),
                                            Score::Sometimes->value => Score::Sometimes->label(),
                                            Score::Often->value => Score::Often->label(),
                                            Score::Always->value => Score::Always->label(),
                                        ])
                                        ->colors([
                                            Score::Never->value => 'gray',
                                            Score::Sometimes->value => 'success',
                                            Score::Often->value => 'warning',
                                            Score::Always->value => 'danger',
                                        ])
                                        ->icons([
                                            Score::Never->value => 'heroicon-o-x-circle',
                                            Score::Sometimes->value => 'heroicon-o-minus-circle',
                                            Score::Often->value => 'heroicon-o-exclamation-circle',
                                            Score::Always->value => 'heroicon-o-check-circle',
                                        ])
                                        ->grouped()
                                        ->dehydrated(false)
                                        ->required()
                                        ->validationAttribute('الإجابة')
                                        ->live(),
                                    Textarea::make("draft_notes.{$answerId}")
                                        ->label('ملاحظات (اختياري)')
                                        ->dehydrated(false)
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ]);
                            $answerIndex++;
                        }
                    } else {
                        // No existing answers — load questions from DB (create mode)
                        /** @var Measurement $measurement */
                        $measurement = Measurement::with('dimensions.questions')->find($selectedScaleId);

                        if (! $measurement) {
                            return [];
                        }

                        $totalQuestionsForScale = $measurement->dimensions->sum(fn ($d) => $d->questions->count());
                        $questionIndex = 1;

                        foreach ($measurement->dimensions as $dimension) {
                            foreach ($dimension->questions as $question) {
                                $qText = e($question->q_text);
                                $contextHtml = "<div class='evaluation-context-badge'>{$measurement->name} &rsaquo; {$dimension->name}</div>";
                                $progressHtml = "<div class='evaluation-wizard-progress'>السؤال {$questionIndex} من {$totalQuestionsForScale}</div>";
                                $questionHtml = "<h2 style='font-size:1.5rem; font-weight:700; margin:0 0 0.75rem 0; color:#1e293b;'>{$qText}</h2>";

                                $wizardSteps[] = Step::make("q_{$question->id}")
                                    ->label("Q{$questionIndex}")
                                    ->id("q_{$question->id}")
                                    ->icon(fn (Get $get) => $get("draft_answers.{$question->id}") !== null ? Heroicon::HandThumbUp : Heroicon::QuestionMarkCircle)
                                    ->completedIcon(Heroicon::HandThumbUp)
                                    ->schema([
                                        TextEntry::make("progress_{$question->id}")
                                            ->hiddenLabel()
                                            ->state(new HtmlString($progressHtml)),
                                        TextEntry::make("context_{$question->id}")
                                            ->hiddenLabel()
                                            ->state(new HtmlString($contextHtml)),
                                        TextEntry::make("question_text_{$question->id}")
                                            ->hiddenLabel()
                                            ->state(new HtmlString($questionHtml)),
                                        ToggleButtons::make("draft_answers.{$question->id}")
                                            ->hiddenLabel()
                                            ->inline()
                                            ->options([
                                                Score::Never->value => Score::Never->label(),
                                                Score::Sometimes->value => Score::Sometimes->label(),
                                                Score::Often->value => Score::Often->label(),
                                                Score::Always->value => Score::Always->label(),
                                            ])
                                            ->colors([
                                                Score::Never->value => 'gray',
                                                Score::Sometimes->value => 'success',
                                                Score::Often->value => 'warning',
                                                Score::Always->value => 'danger',
                                            ])
                                            ->icons([
                                                Score::Never->value => 'heroicon-o-x-circle',
                                                Score::Sometimes->value => 'heroicon-o-minus-circle',
                                                Score::Often->value => 'heroicon-o-exclamation-circle',
                                                Score::Always->value => 'heroicon-o-check-circle',
                                            ])
                                            ->grouped()
                                            ->dehydrated(false)
                                            ->required()
                                            ->validationAttribute('الإجابة')
                                            ->live(),
                                        Textarea::make("draft_notes.{$question->id}")
                                            ->label('ملاحظات (اختياري)')
                                            ->dehydrated(false)
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ]);
                                $questionIndex++;
                            }
                        }
                    }

                    return [
                        Wizard::make($wizardSteps)
                            ->view('filament.components.custom-wizard')
                            ->startOnStep(1)
                            ->skippable()
                            ->persistStepInQueryString()
                            ->columnSpanFull(),
                    ];
                }),
            ])->columnSpan(['default' => 3, 'lg' => 2]),
        ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $answers = $this->record->answers;

        $draftAnswers = [];
        $draftNotes = [];

        foreach ($answers as $answer) {
            // Key by the answer's own ID (not question_id), since we no longer store question_id
            $draftAnswers[$answer->id] = $answer->score->value;
            $draftNotes[$answer->id] = $answer->notes;
        }

        $data['draft_answers'] = $draftAnswers;
        $data['draft_notes'] = $draftNotes;

        return $data;
    }

    protected function afterSave(): void
    {
        $evaluation = $this->record;
        $draftAnswers = $this->data['draft_answers'] ?? [];
        $draftNotes = $this->data['draft_notes'] ?? [];

        // Separate existing answer IDs from new question IDs
        $existingAnswerIds = $evaluation->answers()->pluck('id')->toArray();
        $newAnswerRecords = [];

        foreach ($draftAnswers as $id => $score) {
            if ($score === null) {
                continue;
            }

            if (in_array($id, $existingAnswerIds)) {
                // Update existing answer
                EvaluationAnswer::where('id', $id)
                    ->where('evaluation_id', $evaluation->id)
                    ->update([
                        'score' => (int) $score,
                        'notes' => $draftNotes[$id] ?? null,
                        'updated_at' => now(),
                    ]);
            } else {
                // New question answered — create a new EvaluationAnswer
                $question = Question::with('dimension.measurement')->find($id);

                if ($question) {
                    $newAnswerRecords[] = [
                        'evaluation_id' => $evaluation->id,
                        'question_text' => $question->q_text,
                        'dimension_name' => $question->dimension->name,
                        'measurement_name' => $question->dimension->measurement->name,
                        'recommendations' => json_encode($question->recommendations ?? []),
                        'activities' => json_encode($question->activities ?? []),
                        'goals' => json_encode($question->goals ?? []),
                        'score' => (int) $score,
                        'notes' => $draftNotes[$id] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (! empty($newAnswerRecords)) {
            EvaluationAnswer::insert($newAnswerRecords);
        }
    }
}
