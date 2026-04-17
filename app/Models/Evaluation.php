<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_id',
        'specialist_name',
        'title',
        'evaluation_date',
        'child_age',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);

        static::creating(function (Evaluation $evaluation) {
            if (Auth::hasUser() && ! $evaluation->user_id) {
                $evaluation->user_id = Auth::id();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'evaluation_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(EvaluationAnswer::class);
    }
}
