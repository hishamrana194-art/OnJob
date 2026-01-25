<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModuleProgress extends Model
{
    protected $table = 'user_module_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'module_id',
        'is_completed',
        'progress_percentage',
        'time_spent_seconds',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class)->withDefault();
    }

    public function course()
    {
        return $this->belongsTo(Course::class)->withDefault();
    }
}
