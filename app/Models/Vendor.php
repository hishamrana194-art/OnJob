<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'website',
        'email',
        'phone_number',
        'status',
    ];
public function subscriptions()
{
    return $this->morphMany(Subscription::class, 'subscribable');
}
}
