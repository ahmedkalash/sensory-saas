<?php

namespace App\Services;

class LicensingService
{
    private const PUBLIC_KEY = <<<'PEM'
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4dS2yJ5TL3iFC5I2X8va
PY+n6YDnnEg/Xf0iZW9VLO7kpG/cvOQNRazpbE+nEeDRi3GYfPUYix8ns5eU1bBK
5Op4RHDKtg1vxt8LkA9olVCcaHfp0S2TTmhfcknmlqW2lub/Fy6OF2uU7hYfhMHH
ikdYAQoGYDD4tyo9fJUbi9aOutq2ZhQycc5qvS8a9FVPC6M9IzO4WdnGuH0+xT/L
UWOQ1qOntkX5+jKAaL+30kVyW+IVY60+WZAUnIgzJCxmhdjFEtTejQDCKdzaVnMF
ZARVHHgkUHjatTz728oNhNpFnksHnyBIg3AWLI8p75NjPCzAnjyLPCPmLbf0wz3h
RwIDAQAB
-----END PUBLIC KEY-----
PEM;

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

        $publicKey = openssl_pkey_get_public(self::PUBLIC_KEY);

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

    public function isActivated(): bool
    {
        $licenseKey = $this->getStoredLicenseKey();

        if (empty($licenseKey)) {
            return false;
        }

        return $this->verifyLicense($licenseKey);
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

        return true;
    }

    public function deactivate(): void
    {
        $path = $this->getLicenseFilePath();

        if (file_exists($path)) {
            unlink($path);
        }
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
