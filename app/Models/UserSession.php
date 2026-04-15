<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'last_activity',
        'expires_at',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if session is still active
     */
    public function isActive(): bool
    {
        return $this->expires_at && $this->expires_at->isFuture();
    }

    /**
     * Get time remaining before expiration (in minutes)
     */
    public function minutesRemaining(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }
        return now()->diffInMinutes($this->expires_at, false);
    }

    /**
     * Get device name from user agent
     */
    public function getDeviceName(): string
    {
        $agent = $this->user_agent;
        if (str_contains($agent, 'Chrome')) return 'Chrome';
        if (str_contains($agent, 'Firefox')) return 'Firefox';
        if (str_contains($agent, 'Safari')) return 'Safari';
        if (str_contains($agent, 'Edge')) return 'Edge';
        if (str_contains($agent, 'Mobile')) return 'Mobile/App';
        return 'Unknown Device';
    }
}
