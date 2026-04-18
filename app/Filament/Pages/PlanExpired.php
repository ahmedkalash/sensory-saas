<?php

namespace App\Filament\Pages;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Filament\Pages\SimplePage;

class PlanExpired extends SimplePage
{
    protected string $view = 'filament.pages.plan-expired';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'انتهى الاشتراك';

    public ?Subscription $subscription = null;

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $this->subscription = $user->subscription()->with('plan')->first();

        // If somehow the user now has an active subscription, send them home
        if ($this->subscription?->isActive()) {
            $this->redirect('/');
        }
    }

    public function getReasonText(): string
    {
        if ($this->subscription === null) {
            return 'لم يتم تفعيل اشتراك لحسابك بعد. يرجى التواصل مع المسؤول لتفعيل الحساب.';
        }

        return match ($this->subscription->status) {
            SubscriptionStatus::Expired => 'لقد انتهت صلاحية اشتراكك. يرجى التواصل مع المسؤول لتجديد الاشتراك.',
            SubscriptionStatus::Suspended => 'تم تعليق اشتراكك من قِبل المسؤول. يرجى التواصل معه للاستفسار.',
            default => 'اشتراكك غير نشط حالياً. يرجى التواصل مع المسؤول.',
        };
    }
}
