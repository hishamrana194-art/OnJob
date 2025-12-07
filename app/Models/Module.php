<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Module extends Model
{
    use HasTranslations;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'is_active',
    ];

    public array $translatable = ['title', 'description'];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function items()
    {
        return $this->hasMany(ModuleItem::class)->orderBy('order');
    }

    // Get total duration of all items in this module
    public function getTotalDurationAttribute()
    {
        return $this->items->sum('duration');
    }

    // Get items count
    public function getItemsCountAttribute()
    {
        return $this->items->count();
    }
}

