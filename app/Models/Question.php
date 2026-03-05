<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'dimension_id',
        'q_text',
        'recommendations',
        'goals',
        'activities',
    ];

    protected function casts(): array
    {
        return [
            'recommendations' => 'array',
            'goals' => 'array',
            'activities' => 'array',
        ];
    }

    public function dimension(): BelongsTo
    {
        return $this->belongsTo(Dimension::class);
    }

    public function evaluationAnswers(): HasMany
    {
        return $this->hasMany(EvaluationAnswer::class);
    }
}
