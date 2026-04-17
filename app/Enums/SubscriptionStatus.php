<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SubscriptionStatus: string implements HasColor, HasLabel
{
    case Active = 'active';
    case Expired = 'expired';
    case Suspended = 'suspended';

    public function getLabel(): ?string
    {
        return match ($this) {
            SubscriptionStatus::Active => 'نشط',
            SubscriptionStatus::Expired => 'منتهي',
            SubscriptionStatus::Suspended => 'موقوف',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            SubscriptionStatus::Active => 'success',
            SubscriptionStatus::Expired => 'danger',
            SubscriptionStatus::Suspended => 'warning',
        };
    }
}
