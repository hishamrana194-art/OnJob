<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Course extends Model
{
    use HasTranslations;

    protected $fillable = [
        'vendor_id',
        'category_id',
        'title',
        'description',
        'thumbnail_url',
        'level',
        'duration',
        'status',
        'order',
    ];

    public array $translatable = ['title', 'description'];

    protected $casts = [
        'duration' => 'integer',
        'order' => 'integer',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    // Calculate total duration from modules
    public function getTotalDurationAttribute()
    {
        return $this->modules->sum(function ($module) {
            return $module->items->sum('duration');
        });
    }

    // Get modules count
    public function getModulesCountAttribute()
    {
        return $this->modules->count();
    }
}

