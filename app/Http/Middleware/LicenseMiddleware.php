<?php

namespace App\Http\Middleware;

use App\Services\LicensingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LicenseMiddleware
{
    /**
     * Set to true to bypass license check (useful for testing).
     */
    public static bool $bypass = false;

    public function __construct(private LicensingService $licensingService) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to the activation routes
        if ($request->routeIs('license.*')) {
            return $next($request);
        }

        if (! $this->licensingService->isActivated()) {
            return redirect()->route('license.show');
        }

        return $next($request);
    }
}
