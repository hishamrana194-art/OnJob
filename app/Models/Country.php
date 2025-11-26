<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];
    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }
    
}
