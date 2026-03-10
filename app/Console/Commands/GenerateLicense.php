<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateLicense extends Command
{
    protected $signature = 'license:generate {machineId : The BIOS UUID of the target machine}';

    protected $description = 'Generate a license key for a specific machine (developer only)';

    public function handle(): int
    {
        $machineId = strtoupper(trim($this->argument('machineId')));

        if (empty($machineId)) {
            $this->error('Machine ID cannot be empty.');

            return self::FAILURE;
        }

        $privatePath = storage_path('license/private.pem');

        if (! file_exists($privatePath)) {
            $this->error('Private key not found. Run `php artisan license:generate-keys` first.');

            return self::FAILURE;
        }

        $privateKey = openssl_pkey_get_private(file_get_contents($privatePath));

        if (! $privateKey) {
            $this->error('Failed to read private key.');

            return self::FAILURE;
        }

        $signature = '';
        $signed = openssl_sign($machineId, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        if (! $signed) {
            $this->error('Failed to sign machine ID.');

            return self::FAILURE;
        }

        $licenseKey = base64_encode($signature);

        $this->newLine();
        $this->info('
         generated successfully!');
        $this->newLine();
        $this->line('Machine ID:  '.$machineId);
        $this->line('License Key:');
        $this->newLine();
        $this->line($licenseKey);
        $this->newLine();
        $this->info('Send this license key to the customer.');

        return self::SUCCESS;
    }
}
