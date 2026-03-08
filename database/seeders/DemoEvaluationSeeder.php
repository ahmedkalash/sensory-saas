<?php

namespace Database\Seeders;

use App\Enums\Score;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class DemoEvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create two demo patients
        $patient1 = Patient::create([
            'name' => 'أحمد محمد',
            'dob' => now()->subYears(6)->subMonths(3),
            'gender' => 'ذكر',
            'school' => 'مدرسة الأمل',
            'grade' => 'الروضة',
        ]);

        $patient2 = Patient::create([
            'name' => 'سارة علي',
            'dob' => now()->subYears(8)->subMonths(1),
            'gender' => 'أنثى',
            'school' => 'مدرسة النور',
            'grade' => 'الصف الثاني',
        ]);

        // 2. Create the evaluations
        $evaluation1 = Evaluation::create([
            'patient_id' => $patient1->id,
            'specialist_name' => 'د. خالد عبدالله',
            'title' => 'التقييم المبدئي الشامل',
            'evaluation_date' => now()->subDays(2),
            'child_age' => '6 سنوات و 3 أشهر',
        ]);

        $evaluation2 = Evaluation::create([
            'patient_id' => $patient2->id,
            'specialist_name' => 'أ. نورة صالح',
            'title' => 'متابعة ما بعد 3 أشهر',
            'evaluation_date' => now()->subDays(5),
            'child_age' => '8 سنوات و شهر',
        ]);

        $patient3 = Patient::create([
            'name' => 'عمر المحمدي (حالة مختلطة)',
            'dob' => now()->subYears(7)->subMonths(5),
            'gender' => 'ذكر',
            'school' => 'مدرسة التميز',
            'grade' => 'الصف الأول',
        ]);

        $evaluation3 = Evaluation::create([
            'patient_id' => $patient3->id,
            'specialist_name' => 'د. سمر محمد',
            'title' => 'التقييم الدوري السنوي',
            'evaluation_date' => now(),
            'child_age' => '7 سنوات و 5 أشهر',
        ]);

        // 3. Generate random answers for all 310 questions
        $questions = \App\Models\Question::pluck('id');
        $scores = [Score::Never->value, Score::Sometimes->value, Score::Often->value, Score::Always->value];

        $answers1 = [];
        $answers2 = [];
        $answers3 = [];
        $draftAnswers1 = [];
        $draftAnswers2 = [];
        $draftAnswers3 = [];

        foreach ($questions as $questionId) {
            $randomScore1 = $scores[array_rand($scores)];
            $randomScore2 = $scores[array_rand($scores)];

            $draftAnswers1[$questionId] = $randomScore1;
            $draftAnswers2[$questionId] = $randomScore2;

            $answers1[] = [
                'evaluation_id' => $evaluation1->id,
                'question_id' => $questionId,
                'score' => $randomScore1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $answers2[] = [
                'evaluation_id' => $evaluation2->id,
                'question_id' => $questionId,
                'score' => $randomScore2,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Generate structured answers for evaluation 3 to hit all severities (OK, Mild, Moderate, Severe)
        $dimensions = \App\Models\Dimension::with('questions')->get();
        $severityPattern = [0, 1, 2, 3]; // 0=OK, 1=Mild, 2=Moderate, 3=Severe
        $dimIndex = 0;

        foreach ($dimensions as $dimension) {
            $baseScore = $severityPattern[$dimIndex % 4];

            foreach ($dimension->questions as $question) {
                $draftAnswers3[$question->id] = $baseScore;
                $answers3[] = [
                    'evaluation_id' => $evaluation3->id,
                    'question_id' => $question->id,
                    'score' => $baseScore,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $dimIndex++;
        }

        // Bulk insert answers
        foreach (array_chunk($answers1, 100) as $chunk) {
            EvaluationAnswer::insert($chunk);
        }
        foreach (array_chunk($answers2, 100) as $chunk) {
            EvaluationAnswer::insert($chunk);
        }
        foreach (array_chunk($answers3, 100) as $chunk) {
            EvaluationAnswer::insert($chunk);
        }

        // Update draft_answers on the evaluations so the wizard can load them if edited
        $evaluation1->update(['draft_answers' => $draftAnswers1]);
        $evaluation2->update(['draft_answers' => $draftAnswers2]);
        $evaluation3->update(['draft_answers' => $draftAnswers3]);
    }
}
