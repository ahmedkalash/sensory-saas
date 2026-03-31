# Laravel Octane (RoadRunner) on Windows: Implementation Blueprint

This document archives the successful (though eventually reverted) implementation of Laravel Octane with RoadRunner for a desktop-wrapped Laravel 12 application on Windows.

## 1. Core Architecture

The implementation uses a **Dual Launcher** approach:
- **`launcher.cs` / `start_app.bat`**: Standard `artisan serve` flow for local development and base compatibility.
- **`octane_launcher.cs` / `start_octane.bat`**: High-performance flow using RoadRunner.

### Native Launcher (`octane_launcher.cs`)
A C# wrapper that starts `start_octane.bat` in a hidden window and cleans up child processes (`rr.exe` and `php.exe`) on exit.

## 2. Windows-Specific Patches (CRITICAL)

Laravel Octane assumes a POSIX-compliant environment. On Windows, the following patches were necessary:

### A. `PosixExtension.php` Patch
**File:** `vendor/laravel/octane/src/PosixExtension.php`
The `kill` method must be modified to prevent calling `posix_kill`, which triggers a fatal error on Windows.
```php
public function kill(int $processId, int $signal)
{
    if (PHP_OS_FAMILY === 'Windows') {
        return false;
    }
    return posix_kill($processId, $signal);
}
```

### B. Signal Constant Globalizing
RoadRunner workers run in a context where POSIX constants (`SIGINT`, etc.) are undefined.
**Fix:** Define them in `bootstrap/app.php` or `app/Providers/AppServiceProvider.php`.
```php
if (PHP_OS_FAMILY === 'Windows') {
    if (!defined('SIGINT')) define('SIGINT', 2);
    if (!defined('SIGTERM')) define('SIGTERM', 15);
    // ... other signals
}
```

### C. Interface Suppression
Octane start commands implement `SignalableCommandInterface`. On Windows, this interface triggers errors because the underlying Symfony components expect signal handling support.
**Workaround:** Manually remove `implements SignalableCommandInterface` from:
- `vendor/laravel/octane/src/Commands/StartRoadRunnerCommand.php`
- `vendor/laravel/octane/src/Commands/StartSwooleCommand.php`
- `vendor/laravel/octane/src/Commands/StartFrankenPhpCommand.php`

## 3. Configuration Setup

### RoadRunner (`.rr.yaml`)
```yaml
rpc:
  listen: tcp://127.0.0.1:6001

server:
  command: "php artisan octane:roadrunner-worker"

http:
  address: 127.0.0.1:8000
  pool:
    num_workers: 4
```

### Environment (`.env`)
Blade compilation can fail with "Access Denied" if the default temp directory has permission restrictions. Redirecting it to a local storage path fixes this:
```env
VIEW_COMPILED_PATH="d:/Herd/www/storage/framework/views"
```
*Note: Use forward slashes in PHP paths on Windows to avoid escape sequence errors.*

## 4. Dependencies

- **PHP 8.3/8.4**: Ensure the `sockets` extension is enabled in `php.ini`.
- **RoadRunner Binary**: Downloaded via `spiral/roadrunner-cli` and placed in the project root.

## 5. Reversion Checklist
To return to standard mode:
1. Revert `bootstrap/app.php` and `artisan`.
2. Clear `VIEW_COMPILED_PATH` from `.env`.
3. Restore `SignalableCommandInterface` in vendor files.
4. Stop all `rr.exe` and `php.exe` processes manually via Task Manager or `taskkill`.
