<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuizAttempt extends Model
{
    protected $table = 'user_quiz_attempts';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'module_id',
        'attempt_number',
        'score',
        'answers',
        'is_passed',
        'time_taken_seconds',
    ];

    protected $casts = [
        'answers' => 'array',
        'is_passed' => 'boolean',
        'score' => 'integer',
        'attempt_number' => 'integer',
        'time_taken_seconds' => 'integer',
    ];

    // 🔗 Relationships
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class)->withDefault();
    }

    public function module()
    {
        return $this->belongsTo(Module::class)->withDefault();
    }

    // 📌 Scopes
    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('is_passed', false);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
