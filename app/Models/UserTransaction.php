<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'reason',
        'ref_type',
        'ref_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
