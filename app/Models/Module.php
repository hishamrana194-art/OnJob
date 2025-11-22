<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
      protected $fillable = [
        'vendor_id',
        'title',
        'type',
        'file_url',
        'description'
    ];
    
}
