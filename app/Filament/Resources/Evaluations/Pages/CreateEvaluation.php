<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Enums\Score;
use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\EvaluationAnswer;
use App\Models\Measurement;
use App\Models\Question;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class CreateEvaluation extends CreateRecord
{
    protected static string $resource = EvaluationResource::class;

    protected static ?string $title = 'تقييم جديد';

    /**
     * Build the wizard for CREATING a new evaluation.
     * Uses live Question/Dimension/Measurement models.
     * Fields are keyed by question_id; CreateEvaluation::afterCreate() will
     * convert them to snapshot rows.
     */
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            View::make('filament.components.evaluation-wizard-css'),
            View::make('filament.components.no-copy'),
            Section::make('تحديد المقياس وبيانات المريض')
                ->schema([
                    TextInput::make('title')
                        ->label('عنوان التقييم')
                        ->placeholder('مثل: تقييم مبدئي، متابعة بعد 3 أشهر')
                        ->required()
                        ->string()
                        ->maxLength(255),
                    Select::make('selected_scale')
                        ->label('تحديد المقياس المنفذ')
                        ->options(Measurement::pluck('name', 'id'))
                        ->required()
                        ->live()
                        ->dehydrated(false)
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
                ]),

            Group::make()
                ->schema(function (Get $get) {
                    $selectedScaleId = $get('selected_scale');

                    if (! $selectedScaleId) {
                        return [];
                    }

                    /** @var Measurement $measurement */
                    $measurement = Measurement::with('dimensions.questions')->find($selectedScaleId);

                    if (! $measurement) {
                        return [];
                    }

                    $wizardSteps = [];
                    $totalQuestionsForScale = $measurement->dimensions->sum(fn($d) => $d->questions->count());
                    $questionIndex = 1;

                    foreach ($measurement->dimensions as $dimension) {
                        foreach ($dimension->questions as $question) {
                            $wizardSteps[] = Step::make("q_{$question->id}")
                                ->label("Q{$questionIndex}")
                                ->id("q_{$question->id}")
                                ->icon(fn(Get $get) => $get("draft_answers.{$question->id}") !== null ? Heroicon::HandThumbUp : Heroicon::QuestionMarkCircle)
                                ->completedIcon(Heroicon::HandThumbUp)
                                ->schema([
                                    TextEntry::make("progress_{$question->id}")
                                        ->hiddenLabel()
                                        ->state(new HtmlString("<div class='evaluation-wizard-progress'>السؤال {$questionIndex} من {$totalQuestionsForScale}</div>")),
                                    TextEntry::make("context_{$question->id}")
                                        ->hiddenLabel()
                                        ->state(new HtmlString("<div class='evaluation-context-badge'>{$measurement->name} &rsaquo; {$dimension->name}</div>")),
                                    TextEntry::make("question_text_{$question->id}")
                                        ->hiddenLabel()
                                        ->state(new HtmlString("<h2 style='font-size:1.5rem; font-weight:700; margin:0 0 0.75rem 0; color:#1e293b;'>{$question->q_text}</h2>")),
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

                    return [
                        Wizard::make($wizardSteps)
                            ->view('filament.components.custom-wizard')
                            ->startOnStep(1)
                            ->skippable()
                            ->persistStepInQueryString()
                            ->columnSpanFull(),
                    ];
                }),
        ]);
    }

    protected function afterCreate(): void
    {
        $evaluation = $this->record;
        $draftAnswers = $this->data['draft_answers'] ?? [];
        $draftNotes = $this->data['draft_notes'] ?? [];

        // Load all relevant questions with their full hierarchy in one query
        $questionIds = array_keys(array_filter($draftAnswers, fn($s) => $s !== null));
        $questions = Question::with('dimension.measurement')
            ->whereIn('id', $questionIds)
            ->get()
            ->keyBy('id');

        $answerRecords = [];
        foreach ($draftAnswers as $questionId => $score) {
            if ($score === null) {
                continue;
            }

            $question = $questions->get($questionId);

            if ($question === null) {
                continue;
            }

            $answerRecords[] = [
                'evaluation_id' => $evaluation->id,
                'question_text' => $question->q_text,
                'dimension_name' => $question->dimension->name,
                'measurement_name' => $question->dimension->measurement->name,
                'recommendations' => json_encode($question->recommendations ?? []),
                'activities' => json_encode($question->activities ?? []),
                'goals' => json_encode($question->goals ?? []),
                'score' => (int) $score,
                'notes' => $draftNotes[$questionId] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (! empty($answerRecords)) {
            EvaluationAnswer::insert($answerRecords);
        }
    }
}
