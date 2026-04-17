<?php

namespace App\Models;

use App\Enums\PlanType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    /** @use HasFactory<\Database\Factories\PlanFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'duration_days',
        'quota_count',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => PlanType::class,
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function isYearly(): bool
    {
        return $this->type === PlanType::Yearly;
    }

    public function isQuota(): bool
    {
        return $this->type === PlanType::Quota;
    }
}
