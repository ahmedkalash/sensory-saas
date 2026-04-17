<?php

namespace Tests\Feature;

use App\Enums\Score;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_successfully_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/');

        if ($response->isRedirect()) {
            $response = $this->followRedirects($response);
        }

        $response->assertStatus(200);
    }

    public function test_dashboard_displays_patient_count_widget(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Patient::factory()->count(5)->create();

        $response = $this->get('/');

        if ($response->isRedirect()) {
            $response = $this->followRedirects($response);
        }

        $response->assertStatus(200);
        $response->assertSeeText('5');
    }

    public function test_report_download_route_returns_pdf(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'measurement_name' => 'Test',
            'dimension_name' => 'Test Dim',
            'question_text' => 'Test Q',
            'recommendations' => ['Rec'],
            'goals' => ['Goal'],
            'activities' => ['Act'],
            'score' => Score::Always,
        ]);

        $response = $this->get(route('evaluations.report', $evaluation));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_report_html_route_returns_html(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $patient = Patient::factory()->create();
        $evaluation = Evaluation::factory()->create(['patient_id' => $patient->id]);

        EvaluationAnswer::factory()->create([
            'evaluation_id' => $evaluation->id,
            'measurement_name' => 'Test',
            'dimension_name' => 'Test Dim',
            'question_text' => 'Test Q',
            'recommendations' => ['Rec'],
            'goals' => ['Goal'],
            'activities' => ['Act'],
            'score' => Score::Never,
        ]);

        $response = $this->get(route('evaluations.report.html', $evaluation));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=utf-8');
        $response->assertSee('<!DOCTYPE html>', false);
    }
}
