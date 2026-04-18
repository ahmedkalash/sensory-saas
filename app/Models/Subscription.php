<?php

namespace App\Models;

use App\Enums\PlanType;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class Subscription extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Subscription $subscription) {
            // If the subscription is being saved as active
            if ($subscription->isActive()) {
                $exists = Subscription::query()
                    ->where('user_id', $subscription->user_id)
                    ->where('id', '!=', $subscription->id)
                    ->active()
                    ->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'plan_id' => 'لا يمكن تفعيل أكثر من اشتراك في نفس الوقت لهذا المستخدم.',
                    ]);
                }
            }
        });
    }

    protected $fillable = [
        'user_id',
        'plan_id',
        'ends_at',
        'quota_remaining',
        'is_suspended',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'ends_at' => 'date',
            'is_suspended' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function getPlanNameAttribute(): string
    {
        return $this->plan?->name ?? 'اشتراك';
    }

    public function isYearly(): bool
    {
        return $this->plan?->type === PlanType::Yearly;
    }

    public function isQuota(): bool
    {
        return $this->plan?->type === PlanType::Quota;
    }

    /**
     * Returns true if the subscription grants active access right now.
     */
    public function isActive(): bool
    {
        if ($this->is_suspended) {
            return false;
        }

        return ! $this->isExpired();
    }

    public function isExpired(): bool
    {
        if ($this->isYearly()) {
            return $this->ends_at && Carbon::today()->gt($this->ends_at);
        }

        if ($this->isQuota()) {
            return $this->quota_remaining <= 0;
        }

        return false;
    }

    public function getStatusAttribute(): SubscriptionStatus
    {
        if ($this->isExpired()) {
            return SubscriptionStatus::Expired;
        }

        if ($this->is_suspended) {
            return SubscriptionStatus::Suspended;
        }

        return SubscriptionStatus::Active;
    }

    public function scopeActive($query)
    {
        return $query->where('is_suspended', false)->notExpired();
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where(function ($sq) {
                $sq->whereHas('plan', fn ($pq) => $pq->where('type', PlanType::Yearly))
                    ->where('ends_at', '<', now()->startOfDay());
            })->orWhere(function ($sq) {
                $sq->whereHas('plan', fn ($pq) => $pq->where('type', PlanType::Quota))
                    ->where('quota_remaining', '<=', 0);
            });
        });
    }

    public function scopeSuspended($query)
    {
        return $query->where('is_suspended', true)->notExpired();
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->where(function ($sq) {
                $sq->whereHas('plan', fn ($pq) => $pq->where('type', PlanType::Yearly))
                    ->where('ends_at', '>=', now()->startOfDay());
            })->orWhere(function ($sq) {
                $sq->whereHas('plan', fn ($pq) => $pq->where('type', PlanType::Quota))
                    ->where('quota_remaining', '>', 0);
            });
        });
    }
}
