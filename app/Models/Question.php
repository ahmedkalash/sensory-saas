<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

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
}
