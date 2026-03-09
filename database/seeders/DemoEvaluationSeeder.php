<?php

namespace Database\Seeders;

use App\Enums\Score;
use App\Models\Dimension;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Patient;
use App\Models\Question;
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

        // 3. Load all questions with dimension + measurement for snapshots
        $questions = Question::with('dimension.measurement')->get();
        $scores = [Score::Never->value, Score::Sometimes->value, Score::Often->value, Score::Always->value];

        $answers1 = [];
        $answers2 = [];

        foreach ($questions as $question) {
            $snapshot = $this->buildSnapshot($question);

            $answers1[] = array_merge($snapshot, [
                'evaluation_id' => $evaluation1->id,
                'score' => $scores[array_rand($scores)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $answers2[] = array_merge($snapshot, [
                'evaluation_id' => $evaluation2->id,
                'score' => $scores[array_rand($scores)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Generate structured answers for evaluation3 to hit all severities
        $dimensions = Dimension::with('questions.dimension.measurement')->get();
        $severityPattern = [0, 1, 2, 3]; // 0=OK, 1=Mild, 2=Moderate, 3=Severe
        $dimIndex = 0;
        $answers3 = [];

        foreach ($dimensions as $dimension) {
            $baseScore = $severityPattern[$dimIndex % 4];

            foreach ($dimension->questions as $question) {
                $snapshot = $this->buildSnapshot($question);
                $answers3[] = array_merge($snapshot, [
                    'evaluation_id' => $evaluation3->id,
                    'score' => $baseScore,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $dimIndex++;
        }

        // 5. Bulk insert answers
        foreach (array_chunk($answers1, 100) as $chunk) {
            EvaluationAnswer::insert($chunk);
        }
        foreach (array_chunk($answers2, 100) as $chunk) {
            EvaluationAnswer::insert($chunk);
        }
        foreach (array_chunk($answers3, 100) as $chunk) {
            EvaluationAnswer::insert($chunk);
        }

        // 6. Create 3 historical evaluations per patient to demo Progress Tracking comparison
        $allPatients = [$patient1, $patient2, $patient3];
        $historicalAnswers = [];

        foreach ($allPatients as $patient) {
            for ($i = 1; $i <= 3; $i++) {
                $historicalEval = Evaluation::create([
                    'patient_id' => $patient->id,
                    'specialist_name' => 'أخصائي تجريبي',
                    'title' => 'متابعة دورية '.$i,
                    'evaluation_date' => now()->subMonths(5 - $i),
                    'child_age' => '6 سنوات',
                ]);

                foreach ($questions as $question) {
                    $snapshot = $this->buildSnapshot($question);
                    $historicalAnswers[] = array_merge($snapshot, [
                        'evaluation_id' => $historicalEval->id,
                        'score' => $scores[array_rand($scores)],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        foreach (array_chunk($historicalAnswers, 100) as $chunk) {
            EvaluationAnswer::insert($chunk);
        }
    }

    /**
     * Build the snapshot array from a Question model.
     *
     * @return array<string, mixed>
     */
    private function buildSnapshot(Question $question): array
    {
        return [
            'question_text' => $question->q_text,
            'dimension_name' => $question->dimension->name,
            'measurement_name' => $question->dimension->measurement->name,
            'recommendations' => json_encode($question->recommendations ?? []),
            'activities' => json_encode($question->activities ?? []),
            'goals' => json_encode($question->goals ?? []),
        ];
    }
}
