<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            $user = User::first();

            // No user exists (fresh database) — run seeders to create admin + reference data.
            if (! $user) {
                Artisan::call('db:seed', ['--class' => 'AdminSeeder', '--force' => true]);
                $user = User::first();
            }

            if ($user) {
                Auth::login($user);
            }
        }

        return $next($request);
    }
}
