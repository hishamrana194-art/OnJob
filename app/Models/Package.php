<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
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
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
