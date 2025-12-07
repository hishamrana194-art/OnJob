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
        'status',
        
    ];

    public array $translatable = ['name'];

    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'subscribable');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
