<?php

namespace App\Models;

use App\Enums\Score;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'question_text',
        'dimension_name',
        'measurement_name',
        'recommendations',
        'activities',
        'goals',
        'score',
        'notes',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => Score::class,
            'recommendations' => 'array',
            'activities' => 'array',
            'goals' => 'array',
        ];
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }
}
