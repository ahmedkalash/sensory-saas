<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Enums\Score;
use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Measurement;
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

        // Build measurement options from the snapshot names (only scales that exist in this evaluation)
        $snapshotMeasurementNames = $grouped->keys(); // Collection of measurement names
        /** @var Collection<string,int> $measurements */
        $measurements = Measurement::whereIn('name', $snapshotMeasurementNames)->pluck('id', 'name'); // ['name' => id]

        $totalAnswers = $answers->count();

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
                        ->required(),
                    TextInput::make('specialist_name')
                        ->label('اسم الأخصائي'),
                    DatePicker::make('evaluation_date')
                        ->label('تاريخ التقييم')
                        ->required(),
                    TextInput::make('child_age')
                        ->label('عمر المريض وقت التقييم'),
                ]),

            Group::make()
                ->schema(function (Get $get) use ($grouped, $measurements, $totalAnswers) {
                    $selectedScaleId = $get('selected_scale');

                    if (! $selectedScaleId) {
                        return [];
                    }

                    // Find the measurement name mapped to the selected scale ID
                    $measurementName = $measurements->search($selectedScaleId);

                    if (! $measurementName || ! $grouped->has($measurementName)) {
                        return [];
                    }

                    $wizardSteps = [];
                    $measurementAnswers = $grouped->get($measurementName)->sortBy('dimension_name');
                    $answerIndex = 1;

                    foreach ($measurementAnswers as $answer) {
                        $answerId = $answer->id;
                        $qText = e($answer->question_text);
                        $contextHtml = "<div class='evaluation-context-badge'>{$measurementName} &rsaquo; {$answer->dimension_name}</div>";
                        $progressHtml = "<div class='evaluation-wizard-progress'>السؤال {$answerIndex} من {$totalAnswers}</div>";
                        $questionHtml = "<h2 style='font-size:1.5rem; font-weight:700; margin:0 0 0.75rem 0; color:#1e293b;'>{$qText}</h2>";

                        $wizardSteps[] = Step::make("a_{$answerId}")
                            ->label("Q{$answerIndex}")
                            ->id("a_{$answerId}")
                            ->icon(fn(Get $get) => $get("draft_answers.{$answerId}") !== null ? Heroicon::HandThumbUp : Heroicon::QuestionMarkCircle)
                            ->completedIcon(Heroicon::HandThumbUp)
                            ->schema([
                                TextEntry::make("progress_{$answerId}")
                                    ->hiddenLabel()
                                    ->state(new HtmlString($progressHtml)),
                                TextEntry::make("context_{$answerId}")
                                    ->hiddenLabel()
                                    ->state(new HtmlString($contextHtml)),
                                TextEntry::make("question_text_{$answerId}")
                                    ->hiddenLabel()
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
                                    ->live(),
                                Textarea::make("draft_notes.{$answerId}")
                                    ->label('ملاحظات (اختياري)')
                                    ->dehydrated(false)
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ]);
                        $answerIndex++;
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

        // Update each existing answer by its own ID — DO NOT delete and re-insert
        // as that would lose the snapped question text, dimension, measurement, etc.
        foreach ($draftAnswers as $answerId => $score) {
            if ($score === null) {
                continue;
            }

            EvaluationAnswer::where('id', $answerId)
                ->where('evaluation_id', $evaluation->id)
                ->update([
                    'score' => (int) $score,
                    'notes' => $draftNotes[$answerId] ?? null,
                    'updated_at' => now(),
                ]);
        }
    }
}
