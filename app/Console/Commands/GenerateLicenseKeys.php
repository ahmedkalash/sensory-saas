<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateLicenseKeys extends Command
{
    protected $signature = 'license:generate-keys';

    protected $description = 'Generate RSA key pair for license signing (run once, keep private.pem secret)';

    public function handle(): int
    {
        $dir = storage_path('license');

        if (! is_dir($dir)) {
            mkdir($dir, 0700, true);
        }

        $privatePath = $dir.DIRECTORY_SEPARATOR.'private.pem';
        $publicPath = $dir.DIRECTORY_SEPARATOR.'public.pem';

        if (file_exists($privatePath) || file_exists($publicPath)) {
            if (! $this->confirm('Key files already exist. Overwrite?')) {
                $this->info('Aborted.');

                return self::SUCCESS;
            }
        }

        // On Windows (especially with Herd), openssl.cnf may not exist.
        // Create a minimal config if needed.
        $opensslCnf = $this->ensureOpensslConfig($dir);

        $config = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'config' => $opensslCnf,
        ];

        $res = openssl_pkey_new($config);

        if (! $res) {
            $this->error('Failed to generate RSA key pair: '.openssl_error_string());

            return self::FAILURE;
        }

        openssl_pkey_export($res, $privateKey, null, $config);
        $publicKey = openssl_pkey_get_details($res)['key'];

        file_put_contents($privatePath, $privateKey);
        file_put_contents($publicPath, $publicKey);

        $this->info('RSA key pair generated successfully:');
        $this->line("  Private key: {$privatePath}");
        $this->line("  Public key:  {$publicPath}");
        $this->newLine();
        $this->warn('⚠ IMPORTANT: Keep private.pem SECRET. Do NOT distribute it with the app.');
        $this->warn('  Only public.pem should be included in the application build.');

        return self::SUCCESS;
    }

    /**
     * Ensure an OpenSSL config file exists (Windows often lacks one).
     */
    private function ensureOpensslConfig(string $dir): string
    {
        $cnfPath = $dir.DIRECTORY_SEPARATOR.'openssl.cnf';

        if (! file_exists($cnfPath)) {
            $minimalConfig = <<<'CNF'
            [req]
            default_bits = 2048
            default_md = sha256
            distinguished_name = dn

            [dn]
            CNF;

            file_put_contents($cnfPath, $minimalConfig);
        }

        return $cnfPath;
    }
}
