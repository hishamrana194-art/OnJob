<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
protected $fillable = [
    'subscribable_id',
    'subscribable_type',
    'package_id',
    'status',
    'seats_limit',
    'billing_period',
    'price_per_period',
    'started_at',
    'ends_at',
];
    public function subscribable()
{
    return $this->morphTo();
}
}
