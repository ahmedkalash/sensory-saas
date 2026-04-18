<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PlanStatusWidget;
use App\Models\Subscription;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;

class PlanExpired extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.pages.plan-expired';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'نظام SRPS - تنبيه الاشتراك';

    protected ?string $heading = 'تنبيه: حالة الحساب غير نشطة';

    protected ?string $subheading = 'يرجى مراجعة تفاصيل اشتراكك أدناه لاتخاذ الإجراء اللازم.';

    public ?Subscription $subscription = null;

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $this->subscription = $user->subscription()->with('plan')->first();

        if ($this->subscription?->isActive()) {
            $this->redirect('/');
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PlanStatusWidget::class,
        ];
    }

    public function planSchema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات التجديد')
                    ->description('نظام التقييمات يتطلب اشتراكاً سارياً للمتابعة.')
                    ->schema([
                        Grid::make(3)->schema([
                            Text::make(fn () => 'الباقة: '.($this->subscription?->plan?->name ?? 'غير معروف'))
                                ->weight('bold'),

                            Text::make(fn () => 'تاريخ الانتهاء: '.($this->subscription?->ends_at?->format('Y-m-d') ?? '--'))
                                ->weight('bold'),

                            Text::make(fn () => 'الرصيد المتبقي: '.($this->subscription?->quota_remaining ?? '0'))
                                ->weight('bold'),
                        ]),

                        Text::make(fn () => 'الحالة: '.
                            ($this->subscription ? ($this->subscription->is_suspended ? 'معلق' : 'منتهى') : 'لا يوجد اشتراك')
                        )
                            ->badge()
                            ->color('danger'),

                        Text::make('يرجى مراسلة الإدارة لتجديد الباقة أو الاستفسار عن سبب التعليق. بعد التجديد، ستتم إعادة تفعيل كافة ميزات النظام تلقائياً.')
                            ->columnSpanFull()
                            ->color(Color::Gray),
                    ])
                    ->footer([
                        Action::make('logout_action')
                            ->label('تسجيل الخروج')
                            ->color('gray')
                            ->icon('heroicon-m-arrow-left-on-rectangle')
                            ->action(fn () => $this->handleLogout()),

                        Action::make('contact_action')
                            ->label('مراسلة الدعم (واتساب)')
                            ->color('success')
                            ->icon('heroicon-m-chat-bubble-left-right')
                            ->url('https://wa.me/'.config('app.admin_whatsapp'), true),
                    ]),

                Section::make('الباقات المتاحة')
                    ->description('قائمة بالباقات المتوفرة حالياً في النظام لتجديد اشتراكك.')
                    ->schema([
                        Grid::make(2)->schema(
                            \App\Models\Plan::query()
                                ->where('is_active', true)
                                ->get()
                                ->map(fn (\App\Models\Plan $plan) => Section::make($plan->name)
                                    ->compact()
                                    ->schema([
                                        Text::make('السعر: '.number_format($plan->price, 2).' ج.م')
                                            ->color('success')
                                            ->weight('bold'),
                                        Text::make('التفاصيل: '.($plan->isYearly() ? "صلاحية لمدة {$plan->duration_days} يوم" : "رصيد {$plan->quota_count} تقييمات")),
                                        Text::make($plan->description)
                                            ->size('sm')
                                            ->color(Color::Gray),
                                    ])
                                )
                                ->toArray()
                        ),
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
