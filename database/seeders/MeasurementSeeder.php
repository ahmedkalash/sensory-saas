<?php

namespace Database\Seeders;

use App\Models\Dimension;
use App\Models\Measurement;
use App\Models\Question;
use Illuminate\Database\Seeder;

class MeasurementSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate in FK-safe order
        Question::query()->delete();
        Dimension::query()->delete();
        Measurement::query()->delete();
    }

    // ── FORMAT ──────────────────────────────────────────────────────────────
    // Each question entry:
    // [
    //     'q_text'          => 'النص العربي للعبارة',
    //     'recommendations' => ['توصية 1', 'توصية 2', 'توصية 3'],
    //     'goals'           => ['أن يوجه الطفل...', 'زيادة مدة...'],
    //     'activities'      => ['لعبة تتبع الضوء: ...', 'نشاط الحواف: ...'],
    // ],
    // ────────────────────────────────────────────────────────────────────────

    // ════════════════════════════════════════════════════════════════════════
    // 2. السمعية  (12 + 10 + 10 + 10 = 42 questions)
    // ════════════════════════════════════════════════════════════════════════
    private function seedAuditory(): void
    {
        $m = Measurement::create(['name' => 'مقياس اضطراب المعالجة السمعية']);

        // ── البعد الأول : ضعف الاستجابة للمثيرات السمعية (12 questions) ──
        $m->dimensions()->create(['name' => 'البعد الأول : ضعف الاستجابة للمثيرات السمعية'])
            ->questions()->createMany([
                // paste 12 questions here
            ]);

        // ── البعد الثاني : فرط الاستجابة للمثيرات السمعية (10 questions) ──
        $m->dimensions()->create(['name' => 'البعد الثاني : فرط الاستجابة للمثيرات السمعية'])
            ->questions()->createMany([
                // paste 10 questions here
            ]);

        // ── البعد الثالث : المتجنب الحسي للمثيرات السمعية (10 questions) ──
        $m->dimensions()->create(['name' => 'البعد الثالث : المتجنب الحسي للمثيرات السمعية'])
            ->questions()->createMany([
                // paste 10 questions here
            ]);

        // ── البعد الرابع : الساعي وراء المثير السمعي (10 questions) ──
        $m->dimensions()->create(['name' => 'البعد الرابع : الساعي وراء المثير السمعي'])
            ->questions()->createMany([
                // paste 10 questions here
            ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // 3. اللمسية  (11 + 12 + 9 + 12 = 44 questions)
    // ════════════════════════════════════════════════════════════════════════
    private function seedTactile(): void
    {
        $m = Measurement::create(['name' => 'مقياس اضطراب المعالجة اللمسية']);

        // ── البعد الأول : ضعف الاستجابة للمثيرات اللمسية (11 questions) ──
        $m->dimensions()->create(['name' => 'البعد الأول : ضعف الاستجابة للمثيرات اللمسية'])
            ->questions()->createMany([
                // paste 11 questions here
            ]);

        // ── البعد الثاني : فرط الاستجابة للمثيرات اللمسية (12 questions) ──
        $m->dimensions()->create(['name' => 'البعد الثاني : فرط الاستجابة للمثيرات اللمسية'])
            ->questions()->createMany([
                // paste 12 questions here
            ]);

        // ── البعد الثالث : المتجنب الحسي للمثيرات اللمسية (9 questions) ──
        $m->dimensions()->create(['name' => 'البعد الثالث : المتجنب الحسي للمثيرات اللمسية'])
            ->questions()->createMany([
                // paste 9 questions here
            ]);

        // ── البعد الرابع : الساعي وراء المثير اللمسي (12 questions) ──
        $m->dimensions()->create(['name' => 'البعد الرابع : الساعي وراء المثير اللمسي'])
            ->questions()->createMany([
                // paste 12 questions here
            ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // 4. الدهليزي  (12 + 12 + 10 + 11 = 45 questions)
    // ════════════════════════════════════════════════════════════════════════
    private function seedVestibular(): void
    {
        $m = Measurement::create(['name' => 'مقياس اضطراب الحس الدهليزي']);

        // ── البعد الأول : ضعف استجابة الحس الدهليزي (12 questions) ──
        $m->dimensions()->create(['name' => 'البعد الأول : ضعف استجابة الحس الدهليزي'])
            ->questions()->createMany([
                // paste 12 questions here
            ]);

        // ── البعد الثاني : فرط استجابة الحس الدهليزي (12 questions) ──
        $m->dimensions()->create(['name' => 'البعد الثاني : فرط استجابة الحس الدهليزي'])
            ->questions()->createMany([
                // paste 12 questions here
            ]);

        // ── البعد الثالث : المتجنب الحسي للمثيرات الدهليزية (10 questions) ──
        $m->dimensions()->create(['name' => 'البعد الثالث : المتجنب الحسي للمثيرات الدهليزية'])
            ->questions()->createMany([
                // paste 10 questions here
            ]);

        // ── البعد الرابع : الساعي وراء المثير الدهليزي (11 questions) ──
        $m->dimensions()->create(['name' => 'البعد الرابع : الساعي وراء المثير الدهليزي'])
            ->questions()->createMany([
                // paste 11 questions here
            ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // 5. العضلي  (12 + 12 + 10 + 11 = 45 questions)
    // ════════════════════════════════════════════════════════════════════════
    private function seedProprioceptive(): void
    {
        $m = Measurement::create(['name' => 'مقياس اضطراب الحس العضلي']);

        // ── البعد الأول : ضعف استجابة الحس العضلي (12 questions) ──
        $m->dimensions()->create(['name' => 'البعد الأول : ضعف استجابة الحس العضلي'])
            ->questions()->createMany([
                // paste 12 questions here
            ]);

        // ── البعد الثاني : فرط استجابة للمثيرات العضلية (12 questions) ──
        $m->dimensions()->create(['name' => 'البعد الثاني : فرط استجابة للمثيرات العضلية'])
            ->questions()->createMany([
                // paste 12 questions here
            ]);

        // ── البعد الثالث : المتجنب الحسي للمثيرات العضلية (10 questions) ──
        $m->dimensions()->create(['name' => 'البعد الثالث : المتجنب الحسي للمثيرات العضلية'])
            ->questions()->createMany([
                // paste 10 questions here
            ]);

        // ── البعد الرابع : الساعي وراء المثير العضلي (11 questions) ──
        $m->dimensions()->create(['name' => 'البعد الرابع : الساعي وراء المثير العضلي'])
            ->questions()->createMany([
                // paste 11 questions here
            ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // 6. الشمية  (11 + 12 + 10 + 11 = 44 questions)
    // ════════════════════════════════════════════════════════════════════════
    private function seedOlfactory(): void
    {
        $m = Measurement::create(['name' => 'مقياس اضطراب المعالجة الشمية']);

        // ── البعد الأول : ضعف الاستجابة للمثيرات الشمية (11 questions) ──
        $m->dimensions()->create(['name' => 'البعد الأول : ضعف الاستجابة للمثيرات الشمية'])
            ->questions()->createMany([
                // paste 11 questions here
            ]);

        // ── البعد الثاني : فرط الاستجابة للمثيرات الشمية (12 questions) ──
        $m->dimensions()->create(['name' => 'البعد الثاني : فرط الاستجابة للمثيرات الشمية'])
            ->questions()->createMany([
                // paste 12 questions here
            ]);

        // ── البعد الثالث : المتجنب الحسي للمثيرات الشمية (10 questions) ──
        $m->dimensions()->create(['name' => 'البعد الثالث : المتجنب الحسي للمثيرات الشمية'])
            ->questions()->createMany([
                // paste 10 questions here
            ]);

        // ── البعد الرابع : الساعي وراء المثير الشمي (11 questions) ──
        $m->dimensions()->create(['name' => 'البعد الرابع : الساعي وراء المثير الشمي'])
            ->questions()->createMany([
                // paste 11 questions here
            ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // 7. التذوقية  (12 + 12 + 10 + 10 = 44 questions)
    // ════════════════════════════════════════════════════════════════════════
    private function seedGustatory(): void
    {
        $m = Measurement::create(['name' => 'مقياس المعالجة التذوقية']);

        // ── البعد الأول : ضعف الاستجابة للمثيرات التذوقية (12 questions) ──
        $m->dimensions()->create(['name' => 'البعد الأول : ضعف الاستجابة للمثيرات التذوقية'])
            ->questions()->createMany([
                // paste 12 questions here
            ]);

        // ── البعد الثاني : فرط الاستجابة للمثيرات التذوقية (12 questions) ──
        $m->dimensions()->create(['name' => 'البعد الثاني : فرط الاستجابة للمثيرات التذوقية'])
            ->questions()->createMany([
                // paste 12 questions here
            ]);

        // ── البعد الثالث : المتجنب الحسي للمثيرات التذوقية (10 questions) ──
        $m->dimensions()->create(['name' => 'البعد الثالث : المتجنب الحسي للمثيرات التذوقية'])
            ->questions()->createMany([
                // paste 10 questions here
            ]);

        // ── البعد الرابع : الساعي وراء المثير التذوقي (10 questions) ──
        $m->dimensions()->create(['name' => 'البعد الرابع : الساعي وراء المثير التذوقي'])
            ->questions()->createMany([
                // paste 10 questions here
            ]);
    }
}
