<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class LicensingService
{
    private const CACHE_KEY = 'license_activated';

    /**
     * Get the BIOS UUID of the current Windows machine.
     */
    public function getMachineId(): string
    {
        $output = [];
        exec('wmic csproduct get uuid', $output);

        // The first line is the header "UUID", the second line is the value
        $uuid = trim($output[1] ?? '');

        return strtoupper($uuid);
    }

    /**
     * Verify a license key against the current machine's ID.
     */
    public function verifyLicense(string $licenseKey): bool
    {
        $machineId = $this->getMachineId();

        if (empty($machineId)) {
            return false;
        }

        $publicKeyPath = storage_path('license/public.pem');

        if (! file_exists($publicKeyPath)) {
            return false;
        }

        $publicKey = openssl_pkey_get_public(file_get_contents($publicKeyPath));

        if (! $publicKey) {
            return false;
        }

        $signature = base64_decode($licenseKey, true);

        if ($signature === false) {
            return false;
        }

        $result = openssl_verify($machineId, $signature, $publicKey, OPENSSL_ALGO_SHA256);

        return $result === 1;
    }

    /**
     * Check if the application is currently activated.
     */
    public function isActivated(): bool
    {
        // Use cache to avoid repeated disk reads and wmic calls
        return Cache::remember(self::CACHE_KEY, 3600, function (): bool {
            $licenseKey = $this->getStoredLicenseKey();

            if (empty($licenseKey)) {
                return false;
            }

            return $this->verifyLicense($licenseKey);
        });
    }

    /**
     * Activate the application with the given license key.
     */
    public function activate(string $licenseKey): bool
    {
        if (! $this->verifyLicense($licenseKey)) {
            return false;
        }

        $this->storeLicenseKey($licenseKey);

        // Clear the cache so isActivated() picks up the new key
        Cache::forget(self::CACHE_KEY);

        return true;
    }

    /**
     * Deactivate the application (for testing or reset).
     */
    public function deactivate(): void
    {
        $path = $this->getLicenseFilePath();

        if (file_exists($path)) {
            unlink($path);
        }

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get the stored license key from the local file.
     */
    private function getStoredLicenseKey(): ?string
    {
        $path = $this->getLicenseFilePath();

        if (! file_exists($path)) {
            return null;
        }

        $content = trim(file_get_contents($path));

        return $content ?: null;
    }

    /**
     * Store the license key to a local file.
     */
    private function storeLicenseKey(string $licenseKey): void
    {
        $dir = dirname($this->getLicenseFilePath());

        if (! is_dir($dir)) {
            mkdir($dir, 0700, true);
        }

        file_put_contents($this->getLicenseFilePath(), $licenseKey);
    }

    /**
     * Get the path to the license key file.
     */
    private function getLicenseFilePath(): string
    {
        return storage_path('license/activation.key');
    }
}
