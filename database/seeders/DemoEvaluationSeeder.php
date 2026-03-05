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
            'evaluation_date' => now()->subDays(2),
            'child_age' => '6 سنوات و 3 أشهر',
        ]);

        $evaluation2 = Evaluation::create([
            'patient_id' => $patient2->id,
            'specialist_name' => 'أ. نورة صالح',
            'evaluation_date' => now()->subDays(5),
            'child_age' => '8 سنوات و شهر',
        ]);

        // 3. Generate random answers for all 310 questions
        $questions = \App\Models\Question::pluck('id');
        $scores = [Score::Never->value, Score::Sometimes->value, Score::Often->value, Score::Always->value];

        $answers1 = [];
        $answers2 = [];
        $draftAnswers1 = [];
        $draftAnswers2 = [];

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

        // Bulk insert answers
        foreach (array_chunk($answers1, 100) as $chunk) {
            EvaluationAnswer::insert($chunk);
        }
        foreach (array_chunk($answers2, 100) as $chunk) {
            EvaluationAnswer::insert($chunk);
        }

        // Update draft_answers on the evaluations so the wizard can load them if edited
        $evaluation1->update(['draft_answers' => $draftAnswers1]);
        $evaluation2->update(['draft_answers' => $draftAnswers2]);
    }
}
