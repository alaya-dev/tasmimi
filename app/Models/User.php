<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_CLIENT = 'client';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'phone',
        'password',
        'role',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get all available roles.
     *
     * @return array
     */
    public static function getRoles()
    {
        return [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
            self::ROLE_CLIENT,
        ];
    }

    /**
     * Check if user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is super admin.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    /**
     * Check if user is admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if user is client.
     *
     * @return bool
     */
    public function isClient()
    {
        return $this->hasRole(self::ROLE_CLIENT);
    }

    /**
     * Check if user has admin privileges (super_admin or admin).
     *
     * @return bool
     */
    public function hasAdminPrivileges()
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the client templates for this user.
     */
    public function clientTemplates()
    {
        return $this->hasMany(ClientTemplate::class);
    }

    /**
     * Get the user's subscriptions.
     */
    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the user's payments.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the user's active subscription.
     */
    public function activeSubscription()
    {
        return $this->userSubscriptions()
                    ->with('subscription')
                    ->where('status', UserSubscription::STATUS_ACTIVE)
                    ->where('ends_at', '>', now())
                    ->first();
    }

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    /**
     * Get the user's template purchases.
     */
    public function templatePurchases()
    {
        return $this->hasMany(TemplatePurchase::class);
    }

    /**
     * Check if user has purchased a specific template.
     */
    public function hasPurchasedTemplate($templateId): bool
    {
        return $this->templatePurchases()
                    ->where('template_id', $templateId)
                    ->where('status', TemplatePurchase::STATUS_PAID)
                    ->exists();
    }
}
