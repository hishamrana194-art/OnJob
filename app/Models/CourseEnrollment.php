<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function course()
    {
        return $this->belongsTo(Course::class)->withDefault();
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
