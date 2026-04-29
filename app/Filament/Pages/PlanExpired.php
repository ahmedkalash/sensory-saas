<?php

namespace App\Filament\Pages;

use App\Models\Plan;
use App\Models\Subscription;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry as Text;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;

class PlanExpired extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.pages.plan-expired';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'نظام SRPA - تنبيه الاشتراك';

    protected ?string $heading = 'تنبيه: حالة الحساب غير نشطة';

    protected ?string $subheading = 'يرجى مراجعة تفاصيل اشتراكك أدناه لاتخاذ الإجراء اللازم.';

    public function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('تحديث الحالة')
                ->color('gray')
                ->icon('heroicon-m-arrow-path')
                ->action(fn () => $this->redirect(route('filament.admin.pages.plan-expired'))),
        ];
    }

    public ?Subscription $subscription = null;

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->hasActiveSubscription()) {
            $this->redirect('/');

            return;
        }

        $this->subscription = $user->subscription()->with('plan')->first();
    }

    public function planSchema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Text::make('no_active_sub_alert')
                            ->hiddenLabel()
                            ->state('⚠️ لا يوجد اشتراك فعال حالياً على هذا الحساب')
                            ->color('danger')
                            ->weight('bold')
                            ->alignCenter(),
                    ])
                    ->compact(),

                Section::make('معلومات الحساب')
                    ->description('استخدم هذه البيانات عند التواصل مع الإدارة.')
                    ->schema([
                        Grid::make(2)->schema([
                            Text::make('user_name')
                                ->label('الاسم')
                                ->state(fn () => auth()->user()->name)
                                ->icon('heroicon-m-user'),
                            Text::make('user_email')
                                ->label('البريد الإلكتروني')
                                ->state(fn () => auth()->user()->email)
                                ->icon('heroicon-m-envelope')
                                ->copyable(),
                        ]),
                    ])
                    ->compact(),

                Section::make('تفاصيل الاشتراك الأخير')
                    ->description('حالة آخر اشتراك تم تسجيله على حسابك.')
                    ->schema([
                        Grid::make(3)->schema([
                            Text::make('plan_name')
                                ->label('الباقة')
                                ->state(fn () => $this->subscription?->plan?->name ?? 'غير معروف')
                                ->weight('bold'),

                            Text::make('ends_at')
                                ->label('تاريخ الانتهاء')
                                ->state(fn () => $this->subscription?->ends_at?->format('Y-m-d') ?? '--')
                                ->weight('bold'),

                            Text::make('quota_remaining')
                                ->label('الرصيد المتبقي')
                                ->state(fn () => $this->subscription?->quota_remaining ?? '0')
                                ->weight('bold'),
                        ]),

                        Text::make('status')
                            ->label('الحالة')
                            ->state(fn () => $this->subscription ? ($this->subscription->is_suspended ? 'معلق' : 'منتهى') : 'لا يوجد اشتراك')
                            ->badge()
                            ->color(fn () => $this->subscription?->is_suspended ? 'warning' : 'danger'),
                    ]),

                Section::make('سجل الاشتراكات')
                    ->description('قائمة بجميع اشتراكاتك ووضعها الحالي.')
                    ->schema([
                        RepeatableEntry::make('subscriptions')
                            ->label(false)
                            ->state(fn () => auth()->user()->subscriptions()->latest()->get())
                            ->schema([
                                Grid::make(4)->schema([
                                    Text::make('plan.name')
                                        ->label('الباقة'),

                                    Text::make('status')
                                        ->label('الحالة')
                                        ->badge(),

                                    Text::make('ends_at')
                                        ->label('تاريخ الانتهاء')
                                        ->date()
                                        ->placeholder('--'),

                                    Text::make('quota_remaining')
                                        ->label('الرصيد')
                                        ->placeholder('--'),
                                ]),
                            ])
                            ->visible(fn () => auth()->user()->subscriptions()->count() > 0),
                    ])
                    ->collapsible(),

                Section::make('الباقات المتاحة')
                    ->description('قائمة بالباقات المتوفرة حالياً في النظام لتجديد اشتراكك.')
                    ->schema([
                        Grid::make(2)->schema(
                            Plan::query()
                                ->where('is_active', true)
                                ->get()
                                ->map(fn (Plan $plan) => Section::make($plan->name)
                                    ->compact()
                                    ->schema([
                                        Text::make('price')
                                            ->label('السعر')
                                            ->state(fn () => number_format($plan->price, 2).' ج.م')
                                            ->color('success')
                                            ->weight('bold'),
                                        Text::make('details')
                                            ->label('التفاصيل')
                                            ->state(fn () => $plan->isYearly() ? "صلاحية لمدة {$plan->duration_days} يوم" : "رصيد {$plan->quota_count} تقييمات"),
                                        Text::make('description')
                                            ->label('الوصف')
                                            ->state(fn () => $plan->description)
                                            ->size('sm')
                                            ->color(Color::Gray),
                                    ])
                                )
                                ->toArray()
                        ),
                    ]),

                Section::make('خيارات الدعم والاشتراك')
                    ->description('يرجى مراسلة الإدارة لتجديد الباقة أو الاستفسار عن سبب التعليق. بعد التجديد، ستتم إعادة تفعيل كافة ميزات النظام تلقائياً.')
                    ->footerActions([
                        Action::make('logout_action')
                            ->label('تسجيل الخروج')
                            ->color('gray')
                            ->icon('heroicon-m-arrow-left-on-rectangle')
                            ->action(fn () => $this->handleLogout()),

                        Action::make('contact_action')
                            ->label('مراسلة الدعم (واتساب)')
                            ->color('success')
                            ->icon('heroicon-m-chat-bubble-left-right')
                            ->url(fn () => 'https://wa.me/'.config('app.admin_whatsapp').'?text='.urlencode('أرغب في تجديد اشتراكي. البريد الإلكتروني: '.auth()->user()->email), true),
                    ]),

            ]);
    }

    public function handleLogout(): void
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('filament.admin.auth.login'));
    }
}
