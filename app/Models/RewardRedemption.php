<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardRedemption extends Model
{
    protected $fillable = [
        'reward_id',
        'user_id',
        'status'
    ];

}
