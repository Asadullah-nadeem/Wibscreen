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
    ];

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
            'password' => 'hashed',
        ];
    }

    /**
     * Get the collections for the user.
     */
    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    /**
     * Get the workspace tabs for the user.
     */
    public function tabs()
    {
        return $this->hasMany(WorkspaceTab::class);
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
     * Check if user is on Business plan but pending
     */
    public function isPendingBusiness(): bool
    {
        return $this->plan === 'business' && $this->plan_status === 'pending';
    }
}
