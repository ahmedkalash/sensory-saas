<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class UserScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::hasUser()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Admins can see everything
            if ($user->isAdmin()) {
                return;
            }

            $builder->where('user_id', $user->id);
        }
    }
}
