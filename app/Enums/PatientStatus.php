<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PatientStatus: string implements HasLabel, HasColor
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'نشط',
            self::COMPLETED => 'تم إنهاء العلاج',
            self::ARCHIVED => 'مؤرشف',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::COMPLETED => 'info',
            self::ARCHIVED => 'gray',
        };
    }
}
