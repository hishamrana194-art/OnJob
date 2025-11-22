<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{

    protected $fillable = [
        'employer_id',
        'name',
        'description',
        'points_cost'
    ];
}
