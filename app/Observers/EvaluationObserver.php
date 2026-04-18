<?php

namespace App\Observers;

use App\Enums\PlanType;
use App\Models\Evaluation;
use App\Models\Subscription;

class EvaluationObserver
{
    /**
     * Handle the Evaluation "created" event.
     */
    public function created(Evaluation $evaluation): void
    {
        $user = $evaluation->user;

        if (! $user) {
            return;
        }

        /** @var Subscription|null $subscription */
        $subscription = Subscription::where('user_id', $user->id)
            ->whereHas('plan', fn ($q) => $q->where('type', PlanType::Quota))
            ->where('quota_remaining', '>', 0)
            ->where('is_suspended', false)
            ->first();

        if ($subscription) {
            $subscription->decrement('quota_remaining');
        }
    }

    /**
     * Handle the Evaluation "updated" event.
     */
    public function updated(Evaluation $evaluation): void
    {
        //
    }

    /**
     * Handle the Evaluation "deleted" event.
     */
    public function deleted(Evaluation $evaluation): void
    {
        //
    }

    /**
     * Handle the Evaluation "restored" event.
     */
    public function restored(Evaluation $evaluation): void
    {
        //
    }

    /**
     * Handle the Evaluation "force deleted" event.
     */
    public function forceDeleted(Evaluation $evaluation): void
    {
        //
    }
}
