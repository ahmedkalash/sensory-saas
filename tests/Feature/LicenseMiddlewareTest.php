<?php

namespace Tests\Feature;

use App\Services\LicensingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LicenseMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private LicensingService $licensingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->licensingService = app(LicensingService::class);

        // Ensure the cache is clean so tests run without residual activation data
        Cache::forget('app_license_key');

        // Make sure license checking is enforced for testing
        \App\Http\Middleware\LicenseMiddleware::$bypass = false;

        // Let's explicitly put the middleware back in the web group for these tests
        // in case our routes are tested before Filament's normal registration.
    }

    public function test_unactivated_application_redirects_to_license_page_on_home(): void
    {
        $this->partialMock(LicensingService::class, function ($mock) {
            $mock->shouldReceive('isActivated')->andReturn(false);
        });

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect(route('license.show'));
    }

    public function test_unactivated_application_redirects_to_license_page_on_other_routes(): void
    {
        $this->partialMock(LicensingService::class, function ($mock) {
            $mock->shouldReceive('isActivated')->andReturn(false);
        });

        // Act
        $response = $this->get('/patients');

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect(route('license.show'));
    }

    public function test_unactivated_application_allows_access_to_license_routes(): void
    {
        $this->partialMock(LicensingService::class, function ($mock) {
            $mock->shouldReceive('isActivated')->andReturn(false);
        });

        $this->withoutExceptionHandling();
        $response = $this->get(route('license.show'));

        // Assert
        $response->assertStatus(200);
        $this->assertStringContainsString('تفعيل', $response->getContent());
    }

    public function test_activated_application_allows_access_to_routes(): void
    {
        // Arrange
        $this->partialMock(LicensingService::class, function ($mock) {
            $mock->shouldReceive('isActivated')->andReturn(true);
        });

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // Act
        $this->withoutExceptionHandling();
        $response = $this->get('/');

        if ($response->isRedirect()) {
            $response = $this->followRedirects($response);
        }

        // Assert
        $response->assertStatus(200);
    }
}
