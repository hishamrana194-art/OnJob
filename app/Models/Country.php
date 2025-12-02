<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'code',
    ];

    public array $translatable = ['name'];

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }
}
