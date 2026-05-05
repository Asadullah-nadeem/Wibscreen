<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'plan',
        'plan_expiry_at',
        'plan_status',
        'payment_id',
        'monthly_usage_minutes',
        'usage_reset_at',
        'account_status',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function tabs()
    {
        return $this->hasMany(WorkspaceTab::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'plan_expiry_at' => 'datetime',
            'usage_reset_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user can create another workspace (Collection)
     */
    public function canCreateWorkspace(): bool
    {
        // Business plan must be active to have unlimited
        if ($this->plan === 'business' && $this->plan_status === 'active') {
            return true;
        }

        $limit = $this->plan === 'pro' ? 10 : 1;
        return $this->collections()->count() < $limit;
    }

    /**
     * Check if user has access to premium features (Pro/Business)
     */
    public function hasPremium(): bool
    {
        if ($this->plan_status !== 'active') return false;
        return in_array($this->plan, ['pro', 'business']);
    }

    /**
     * Get limit for Email Accounts
     */
    public function emailLimit(): int
    {
        if ($this->hasPremium()) return 999999; // Unlimited
        return 10;
    }

    /**
     * Get limit for Bot Queries
     */
    public function botQueryLimit(): int
    {
        if ($this->hasPremium()) return 999999; // Unlimited
        return 10;
    }

    /**
     * Return the count of all WorkspaceTab records for the user
     */
    public function totalTabsCount(): int
    {
        return $this->tabs()->count();
    }

    /**
     * Check if user is on Business plan but pending
     */
    public function isPendingBusiness(): bool
    {
        return $this->plan === 'business' && $this->plan_status === 'pending';
    }

    /**
     * Check if the current premium plan has expired
     */
    public function isPlanExpired(): bool
    {
        if ($this->plan === 'free') return false;
        
        return $this->plan_expiry_at && $this->plan_expiry_at->isPast();
    }
}
