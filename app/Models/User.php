<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * User type constants
     */
    public const TYPE_EMPLOYER_USER = 'employer_user';
    public const TYPE_VENDOR_USER = 'vendor_user';
    public const TYPE_TRAINING_PROVIDER = 'training_provider';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'user_type',
        'employer_id',
        'vendor_id',
        'role',
        'points',
        'locale',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'points' => 'integer',
            'password' => 'hashed',
        ];
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            // Ensure data integrity based on user_type
            if ($user->user_type === self::TYPE_EMPLOYER_USER) {
                $user->vendor_id = null;
            } elseif ($user->user_type === self::TYPE_VENDOR_USER) {
                $user->employer_id = null;
            } elseif ($user->user_type === self::TYPE_TRAINING_PROVIDER) {
                $user->employer_id = null;
                $user->vendor_id = null;
            }
        });
    }

    // ==================
    // Relationships
    // ==================

    /**
     * Get the employer that the user belongs to.
     */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class, 'employer_id');
    }

    /**
     * Get the vendor that the user belongs to.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    // ==================
    // Accessors
    // ==================

    /**
     * Get the owner (employer or vendor) based on user_type
     */
    public function getOwnerAttribute()
    {
        if ($this->user_type === self::TYPE_EMPLOYER_USER) {
            return $this->employer;
        } elseif ($this->user_type === self::TYPE_VENDOR_USER) {
            return $this->vendor;
        }
        return null;
    }

    /**
     * Get the owner name
     */
    public function getOwnerNameAttribute(): string
    {
        $owner = $this->owner;
        if ($owner) {
            return is_array($owner->name) ? ($owner->name['en'] ?? $owner->name['ar'] ?? 'N/A') : ($owner->name ?? 'N/A');
        }
        return 'N/A';
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    // ==================
    // Query Scopes
    // ==================

    /**
     * Scope to filter employer users only
     */
    public function scopeEmployerUsers(Builder $query): Builder
    {
        return $query->where('user_type', self::TYPE_EMPLOYER_USER);
    }

    /**
     * Scope to filter vendor users only
     */
    public function scopeVendorUsers(Builder $query): Builder
    {
        return $query->where('user_type', self::TYPE_VENDOR_USER);
    }

    /**
     * Scope to filter training providers only
     */
    public function scopeTrainingProviders(Builder $query): Builder
    {
        return $query->where('user_type', self::TYPE_TRAINING_PROVIDER);
    }

    /**
     * Scope to filter by employer
     */
    public function scopeByEmployer(Builder $query, int $employerId): Builder
    {
        return $query->where('employer_id', $employerId);
    }

    /**
     * Scope to filter by vendor
     */
    public function scopeByVendor(Builder $query, int $vendorId): Builder
    {
        return $query->where('vendor_id', $vendorId);
    }

    // ==================
    // Helper Methods
    // ==================

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if user is an employer user
     */
    public function isEmployerUser(): bool
    {
        return $this->user_type === self::TYPE_EMPLOYER_USER;
    }

    /**
     * Check if user is a vendor user
     */
    public function isVendorUser(): bool
    {
        return $this->user_type === self::TYPE_VENDOR_USER;
    }

    /**
     * Check if user is a training provider
     */
    public function isTrainingProvider(): bool
    {
        return $this->user_type === self::TYPE_TRAINING_PROVIDER;
    }

    /**
     * Add points to user
     */
    public function addPoints(int $points): void
    {
        $this->increment('points', $points);
    }
}
