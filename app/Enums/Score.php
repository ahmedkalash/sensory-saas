<?php

namespace App\Enums;

enum Score: int
{
    case Never = 0; // لايوجد
    case Sometimes = 1; // أحيانا
    case Often = 2; // غالبا
    case Always = 3; // موجود دائما

    public function label(): string
    {
        return match ($this) {
            Score::Never => 'لايوجد',
            Score::Sometimes => 'أحيانا',
            Score::Often => 'غالبا',
            Score::Always => 'موجود دائما',
        };
    }
}
