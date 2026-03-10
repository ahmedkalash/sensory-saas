<?php

namespace App\Http\Controllers;

use App\Services\LicensingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LicenseController extends Controller
{
    public function __construct(private LicensingService $licensingService) {}

    /**
     * Show the activation page with the Machine ID.
     */
    public function show(): View|RedirectResponse
    {
        if ($this->licensingService->isActivated()) {
            return redirect('/');
        }

        return view('license.activation', [
            'machineId' => $this->licensingService->getMachineId(),
        ]);
    }

    /**
     * Attempt to activate the application with the provided license key.
     */
    public function activate(Request $request): RedirectResponse
    {
        $request->validate([
            'license_key' => ['required', 'string'],
        ]);

        $licenseKey = trim($request->input('license_key'));

        if ($this->licensingService->activate($licenseKey)) {
            return redirect('/')->with('success', 'تم تفعيل التطبيق بنجاح! 🎉');
        }

        return back()->withErrors([
            'license_key' => 'مفتاح الترخيص غير صالح أو لا يتطابق مع هذا الجهاز.',
        ])->withInput();
    }
}
