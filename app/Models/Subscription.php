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

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'price_per_period' => 'decimal:2',
    ];

    public function subscribable()
    {
        return $this->morphTo();
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
