<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AutoLoginMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure no redirect based on LicenseMiddleware during this test
        \App\Http\Middleware\LicenseMiddleware::$bypass = true;
    }

    public function test_auto_login_middleware_logs_in_first_user(): void
    {
        // Arrange
        $user1 = User::factory()->create(['email' => 'admin@example.com']);
        $user2 = User::factory()->create(['email' => 'other@example.com']);

        // Assert not logged in initially
        $this->assertFalse(Auth::check());

        // Act
        // Making a request to the admin panel triggers the middleware
        $response = $this->get('/');

        // Assert
        $this->assertTrue(Auth::check());
        $this->assertEquals($user1->id, Auth::id());
    }

    public function test_auto_login_middleware_does_not_override_existing_session(): void
    {
        // Arrange
        $user1 = User::factory()->create(['email' => 'admin@example.com']);
        $user2 = User::factory()->create(['email' => 'other@example.com']);

        // Manually log in user 2
        $this->actingAs($user2);

        // Act
        $response = $this->get('/');

        // Assert
        $this->assertTrue(Auth::check());
        $this->assertEquals($user2->id, Auth::id(), 'AutoLogin should not override an existing active session');
    }

    public function test_auto_login_middleware_handles_empty_users_table_gracefully(): void
    {
        // Arrange
        // Ensure database has no users
        User::truncate();

        // Assert not logged in
        $this->assertFalse(Auth::check());

        // Act & Assert
        // In the absence of users and with AuthenticateSession disabled,
        // Filament might try to redirect to login, which might throw a RouteNotFoundException
        // if auth components are not fully enabled. That's outside the middleware's scope.
        // What we care about is that Auth::check() is false after it runs.
        try {
            $this->withoutExceptionHandling();
            $this->get('/');
        } catch (\Exception $e) {
            // It could be RouteNotFoundException but we don't care.
            // The key is that the auto-login did not happen.
            $this->assertTrue(true);
        }

        // Assert
        $this->assertFalse(Auth::check());
    }
}
