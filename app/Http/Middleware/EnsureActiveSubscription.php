<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use App\Filament\Pages\PlanExpired;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($user == null) {
            return $next($request);
        }

        // Admins always have access — no subscription required
        if ($user->type === UserType::Admin) {
            return $next($request);
        }

        // Allow the plan-expired page itself to prevent redirect loops
        if ($request->routeIs('filament.admin.pages.plan-expired')) {
            return $next($request);
        }

        if (! $user->hasActiveSubscription()) {
            return redirect()->to(PlanExpired::getUrl());
        }

        return $next($request);
    }
}
