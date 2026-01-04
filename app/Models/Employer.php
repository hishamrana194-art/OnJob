<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Employer extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'email',
        'country_id',
        'phone_number',
        'company',
        'address',
        'gender',
        'status',
    ];

    public array $translatable = ['name', 'company', 'address'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'subscribable');
    }

    /**
     * Get the active subscription
     */
    public function activeSubscription()
    {
        return $this->morphOne(Subscription::class, 'subscribable')
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest();
    }

    /**
     * Get users belonging to this employer
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
