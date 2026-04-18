<?php

namespace App\Observers;

use App\Models\Evaluation;

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

        /** @var \App\Models\Subscription|null $subscription */
        $subscription = $user->subscription()->first();
        if ($subscription && $subscription->isQuota() && $subscription->quota_remaining > 0) {
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
