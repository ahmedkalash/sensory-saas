<?php

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LogoutOtherSessions
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        if (! $event->user) {
            return;
        }

        // Get the current session ID to preserve it
        $currentSessionId = Session::getId();

        // Delete all OTHER sessions for this user from the database
        DB::table('sessions')
            ->where('user_id', $event->user->getAuthIdentifier())
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }
}
