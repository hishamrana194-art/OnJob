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
}
