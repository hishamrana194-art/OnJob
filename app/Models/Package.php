<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Package extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'type',
        'description',
        'price_monthly',
        'price_yearly',
        'max_employees',
        'max_content',
        'features',
        'is_active',
    ];

    public array $translatable = ['name', 'description', 'features'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
