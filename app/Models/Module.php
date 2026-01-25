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
        return $this->belongsTo(Course::class)->withDefault();
    }

    public function items()
    {
        return $this->hasMany(ModuleItem::class)->orderBy('order');
    }

    public function userProgress()
    {
        return $this->hasMany(UserModuleProgress::class, 'module_id', 'id');
    }

    public function getTotalDurationAttribute()
    {
        return $this->items->sum('duration');
    }

    public function getItemsCountAttribute()
    {
        return $this->items->count();
    }
    public function module()
{
    return $this->belongsTo(Module::class)->withDefault(); // لو الموديول محذوف، يرجع قيمة افتراضية بدل الوقوع
}

}
