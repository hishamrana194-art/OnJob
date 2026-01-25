<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModuleItemProgress extends Model
{
    protected $table = 'user_module_item_progress';

    protected $fillable = [
        'user_id',
        'module_id',
        'module_item_id',
        'is_completed',
        'watched_duration_seconds',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class)->withDefault();
    }

    public function moduleItem()
    {
        return $this->belongsTo(ModuleItem::class)->withDefault();
    }
}
