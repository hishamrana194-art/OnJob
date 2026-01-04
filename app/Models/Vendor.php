<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Vendor extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'website',
        'email',
        'phone_number',
        'country_id',
        'description',
        'status',
    ];

    public array $translatable = ['name', 'description'];

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

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get users belonging to this vendor
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
