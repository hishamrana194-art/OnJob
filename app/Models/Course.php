<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'vendor_id',
        'title',
        'description',
        'category_id',
        'thumbnail_url',
        'level',
        'duration',
        'status',
    ];
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
