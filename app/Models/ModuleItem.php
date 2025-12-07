<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ModuleItem extends Model
{
    use HasTranslations;

    protected $fillable = [
        'module_id',
        'type',
        'title',
        'content',
        'file_url',
        'duration',
        'order',
        'is_active',
    ];

    public array $translatable = ['title', 'content'];

    protected $casts = [
        'is_active' => 'boolean',
        'duration' => 'integer',
        'order' => 'integer',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    // Check if this item is a quiz type
    public function isQuiz()
    {
        return $this->type === 'quiz';
    }
}

