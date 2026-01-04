<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailVerificationToken extends Model
{
    protected $fillable = [
        'token',
        'email',
        'user_id',
        'verifiable_id',
        'verifiable_type',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Generate a new verification token
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Create a new verification token for a user
     */
    public static function createForUser(User $user, $verifiable = null): self
    {
        // Invalidate any existing tokens for this email
        static::where('email', $user->email)
            ->whereNull('verified_at')
            ->delete();

        return static::create([
            'token' => static::generateToken(),
            'email' => $user->email,
            'user_id' => $user->id,
            'verifiable_id' => $verifiable?->id,
            'verifiable_type' => $verifiable ? get_class($verifiable) : null,
            'expires_at' => now()->addHours(24),
        ]);
    }

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if token is already used
     */
    public function isUsed(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Check if token is valid
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isUsed();
    }

    /**
     * Mark token as verified
     */
    public function markAsVerified(): void
    {
        $this->update(['verified_at' => now()]);
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the verifiable entity (employer or vendor)
     */
    public function verifiable()
    {
        return $this->morphTo();
    }
}

