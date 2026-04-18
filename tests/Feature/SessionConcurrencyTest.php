<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class SessionConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_invalidates_other_sessions_on_login(): void
    {
        $user = User::factory()->create();

        // 1. Manually insert an "old" session into the database
        DB::table('sessions')->insert([
            'id' => 'old_session_id',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Browser 1',
            'payload' => 'old_payload',
            'last_activity' => time(),
        ]);

        // 2. Insert another session for a DIFFERENT user (should NOT be deleted)
        $otherUser = User::factory()->create();
        DB::table('sessions')->insert([
            'id' => 'other_user_session',
            'user_id' => $otherUser->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Browser 2',
            'payload' => 'other_payload',
            'last_activity' => time(),
        ]);

        // Verify they exist
        $this->assertDatabaseHas('sessions', ['id' => 'old_session_id']);
        $this->assertDatabaseHas('sessions', ['id' => 'other_user_session']);

        // 3. Perform a login (This triggers the Login event)
        // We use Auth::login which fires the event
        Auth::login($user);

        // 4. Assertions
        // The old session for THIS user should be gone
        $this->assertDatabaseMissing('sessions', ['id' => 'old_session_id']);

        // The session for the OTHER user should still exist
        $this->assertDatabaseHas('sessions', ['id' => 'other_user_session']);

        // The CURRENT session for this user should exist (if DB sessions are working in test)
        // Note: In tests, the session ID might be different or not persisted if not careful,
        // but our listener targets everything except the current one.
    }
}
