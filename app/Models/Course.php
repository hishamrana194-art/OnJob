<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Course extends Model
{
    use HasTranslations;

    protected $fillable = [
        'vendor_id',      // nullable when is_global = true
        'category_id',
        'title',
        'description',
        'thumbnail_url',
        'level',
        'duration',
        'status',
        'is_mandatory',
        'is_global',
        'order',
    ];

    public array $translatable = ['title', 'description'];

    protected $casts = [
        'duration'     => 'integer',
        'order'        => 'integer',
        'is_mandatory' => 'boolean',
        'is_global'    => 'boolean',
    ];

    public function vendor()
    {
        // withDefault() prevents null-access errors on global courses (vendor_id = null)
        return $this->belongsTo(Vendor::class)->withDefault();
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** Courses visible to employer users: everything marked global. */
    public function scopeVisibleToEmployer($query)
    {
        return $query->where('is_global', true);
    }

    /** Courses visible to a specific vendor's users. */
    public function scopeVisibleToVendor($query, int $vendorId)
    {
        return $query->where('vendor_id', $vendorId)->where('is_global', false);
    }

    /**
     * Unified visibility scope for any authenticated entity.
     *
     * Pass auth()->user() — the scope resolves the correct filter automatically:
     *
     *   employer_user  →  global courses only
     *   vendor_user    →  their own vendor's non-global courses
     *   Admin model /
     *   training_provider /
     *   null (unauthenticated)  →  no filter (all courses)
     *
     * The two narrow scopes (visibleToEmployer / visibleToVendor) are kept for
     * any code that needs to call them directly (e.g. tests, other services).
     */
    public function scopeForUser($query, $user = null)
    {
        if (!($user instanceof User)) {
            // Admin model, guest, training_provider with no user_type — no filter.
            return $query;
        }

        return match ($user->user_type) {
            'employer_user' => $query->visibleToEmployer(),
            'vendor_user'   => $query->visibleToVendor((int) $user->vendor_id),
            default         => $query, // training_provider, super_admin, etc.
        };
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    // Calculate total duration from modules
    public function getTotalDurationAttribute()
    {
        return $this->modules->sum(function ($module) {
            return $module->items->sum('duration');
        });
    }

    // Get modules count
    public function getModulesCountAttribute()
    {
        return $this->modules->count();
    }
    public function users()
{
    return $this->belongsToMany(User::class)
        ->withTimestamps();
}

}

