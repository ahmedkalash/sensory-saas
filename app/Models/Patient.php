<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'dob',
        'gender',
        'school',
        'grade',
        'medical_plan',
        'status',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);

        static::creating(function (Patient $patient) {
            if (Auth::hasUser() && ! $patient->user_id) {
                $patient->user_id = Auth::id();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'medical_plan' => 'array',
            'status' => \App\Enums\PatientStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
}
