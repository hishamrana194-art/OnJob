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
        'seats_used',
        'billing_period',
        'price_per_period',
        'started_at',
        'ends_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'price_per_period' => 'decimal:2',
        'seats_limit' => 'integer',
        'seats_used' => 'integer',
    ];

    public function subscribable()
    {
        return $this->morphTo();
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Check if there are available seats
     */
    public function hasAvailableSeats(): bool
    {
        return $this->seats_used < $this->seats_limit;
    }

    /**
     * Get remaining seats count
     */
    public function getRemainingSeatsAttribute(): int
    {
        return max(0, $this->seats_limit - $this->seats_used);
    }

    /**
     * Increment seats used counter
     */
    public function incrementSeats(): void
    {
        $this->increment('seats_used');
    }

    /**
     * Decrement seats used counter
     */
    public function decrementSeats(): void
    {
        if ($this->seats_used > 0) {
            $this->decrement('seats_used');
        }
    }

    /**
     * Check if seat limit is reached
     */
    public function isSeatLimitReached(): bool
    {
        return $this->seats_used >= $this->seats_limit;
    }

    /**
     * Get seat usage percentage
     */
    public function getSeatUsagePercentageAttribute(): float
    {
        if ($this->seats_limit == 0) {
            return 0;
        }
        return ($this->seats_used / $this->seats_limit) * 100;
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }
}
