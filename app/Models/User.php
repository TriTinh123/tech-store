<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\User
 *
 * @property-read int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $phone
 * @property string|null $address
 * @property bool $is_blocked
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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
        'role',
        'phone',
        'address',
        'is_blocked',
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
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get wishlist for this user
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get comparisons for this user
     */
    public function comparisons()
    {
        return $this->hasMany(Comparison::class);
    }

    /**
     * Get orders for this user
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get chatbot conversations for this user
     */
    public function chatbotConversations()
    {
        return $this->hasMany(ChatbotConversation::class);
    }

    /**
     * Get user preferences
     */
    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Get suspicious login attempts
     */
    public function suspiciousLogins()
    {
        return $this->hasMany(SuspiciousLogin::class);
    }

    /**
     * Get login logs for this user
     */
    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class);
    }

    /**
     * Three-Factor Authentication
     */
    public function threeFactorAuth()
    {
        return $this->hasMany(ThreeFactorAuthentication::class);
    }

    /**
     * Security answers for this user
     */
    public function securityAnswers()
    {
        return $this->hasMany(UserSecurityAnswer::class);
    }

    /**
     * Login attempts for this user
     */
    public function loginAttempts()
    {
        return $this->hasMany(LoginAttempt::class);
    }

    /**
     * Security alerts for this user
     */
    public function securityAlerts()
    {
        return $this->hasMany(SecurityAlert::class);
    }

    /**
     * User sessions for this user
     */
    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Auto responses triggered by anomalies for this user
     */
    public function autoResponses()
    {
        return $this->hasMany(AutoResponse::class);
    }

    /**
     * IPs blocked by this admin
     */
    public function blockedIps()
    {
        return $this->hasMany(BlockedIp::class, 'blocked_by_admin_id');
    }

    /**
     * Auto responses manually triggered by this admin
     */
    public function triggeredAutoResponses()
    {
        return $this->hasMany(AutoResponse::class, 'triggered_by_admin_id');
    }

    /**
     * Auto responses reviewed by this admin
     */
    public function reviewedAutoResponses()
    {
        return $this->hasMany(AutoResponse::class, 'reviewed_by_admin_id');
    }

    /**
     * User's notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * User's notification preferences
     */
    public function notificationPreferences()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    /**
     * User's notification logs
     */
    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    /**
     * Check if user has 3FA enabled
     */
    public function has3FAEnabled()
    {
        return $this->securityAnswers()->exists();
    }

    /**
     * Get current pending 3FA verification
     */
    public function pendingThreeFactorAuth()
    {
        return $this->threeFactorAuth()->where('is_verified', false)->latest()->first();
    }
}
