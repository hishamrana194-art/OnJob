<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
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
    public function countries()
    {
        return $this->hasOne(Country::class);
    }
    public function subscriptions()
{
    return $this->morphMany(Subscription::class, 'subscribable');
}
}
