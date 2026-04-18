<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PlanStatusWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && ! $user->isAdmin();
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        if (! ($user instanceof User)) {
            return [];
        }

        /** @var \App\Models\Subscription|null $subscription */
        $subscription = $user->activeSubscription()->with('plan')->first();

        if (! $subscription || ! $subscription->isActive()) {
            return [
                Stat::make('حالة الاشتراك', 'غير نشط')
                    ->description('ليس لديك اشتراك فعال')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle'),
            ];
        }

        $plan = $subscription->plan;

        if ($plan->isYearly()) {
            $daysRemaining = $subscription->ends_at ? now()->diffInDays($subscription->ends_at, false) : 0;

            return [
                Stat::make('الخطة الحالية', $plan->name)
                    ->description('اشتراك سنوي')
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),

                Stat::make('أيام متبقية', max(0, (int) $daysRemaining).' يوم')
                    ->description('تاريخ الانتهاء: '.($subscription->ends_at?->format('Y-m-d') ?? '-'))
                    ->color($daysRemaining < 30 ? 'warning' : 'primary')
                    ->icon('heroicon-o-calendar'),
            ];
        }

        if ($plan->isQuota()) {
            return [
                Stat::make('الخطة الحالية', $plan->name)
                    ->description('باقة تقييمات')
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),

                Stat::make('التقييمات المتبقية', $subscription->quota_remaining ?? 0)
                    ->description('من الرصيد الكلي')
                    ->color($subscription->quota_remaining < 10 ? 'warning' : 'primary')
                    ->icon('heroicon-o-calculator'),
            ];
        }

        return [];
    }
}
