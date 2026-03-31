<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class GenerateLicensePage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'توليد مفتاح ترخيص';

    protected static ?string $title = 'توليد مفتاح ترخيص';

    protected static ?int $navigationSort = 99;

    public static function canAccess(): bool
    {
        return true;
    }

    protected string $view = 'filament.pages.generate-license';

    public string $machineId = '';

    public string $generatedKey = '';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('machineId')
                ->label('معرّف الجهاز (Machine ID)')
                ->placeholder('مثال: A45BE4AE-C1A3-254D-AE61-B4E8F80F8673')
                ->required()
                ->extraAttributes(['dir' => 'ltr', 'style' => 'font-family: Consolas, monospace;']),

            Textarea::make('generatedKey')
                ->label('المفتاح المُولَّد')
                ->readOnly()
                ->visible(fn (): bool => $this->generatedKey !== '')
                ->rows(4)
                ->extraAttributes(['dir' => 'ltr', 'style' => 'font-family: Consolas, monospace;']),
        ]);
    }

    public function generate(): void
    {
        $this->validate();

        $privatePath = storage_path('license/private.pem');

        if (! file_exists($privatePath)) {
            Notification::make()
                ->title('ملف private.pem غير موجود')
                ->danger()
                ->send();

            return;
        }

        $privateKey = openssl_pkey_get_private(file_get_contents($privatePath));

        if (! $privateKey) {
            Notification::make()
                ->title('فشل في قراءة المفتاح الخاص')
                ->danger()
                ->send();

            return;
        }

        $machineId = strtoupper(trim($this->machineId));
        $signature = '';

        if (! openssl_sign($machineId, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            Notification::make()
                ->title('فشل في توقيع المفتاح')
                ->danger()
                ->send();

            return;
        }

        $this->generatedKey = base64_encode($signature);

        Notification::make()
            ->title('تم توليد المفتاح بنجاح ✓')
            ->success()
            ->send();
    }
}
