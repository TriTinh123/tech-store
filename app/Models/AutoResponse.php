<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'suspicious_login_id',
        'triggered_by_admin_id',
        'trigger_type',
        'severity',
        'trigger_description',
        'response_action',
        'action_description',
        'status',
        'execution_result',
        'error_message',
        'triggered_at',
        'executed_at',
        'expires_at',
        'security_config_snapshot',
        'user_context',
        'anomaly_details',
        'requires_user_confirmation',
        'user_confirmed',
        'user_confirmation_at',
        'user_confirmation_details',
        'lockout_until',
        'lockout_reason',
        'lockout_auto_unlock',
        'blocked_ip_address',
        'is_permanent_block',
        'user_notified',
        'notification_sent_at',
        'notification_method',
        'admin_notes',
        'action_history',
        'reviewed_at',
        'reviewed_by_admin_id',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'executed_at' => 'datetime',
        'expires_at' => 'datetime',
        'user_confirmation_at' => 'datetime',
        'lockout_until' => 'datetime',
        'notification_sent_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'requires_user_confirmation' => 'boolean',
        'user_confirmed' => 'boolean',
        'lockout_auto_unlock' => 'boolean',
        'is_permanent_block' => 'boolean',
        'user_notified' => 'boolean',
        'security_config_snapshot' => 'json',
        'user_context' => 'json',
        'anomaly_details' => 'json',
        'action_history' => 'json',
    ];

    /**
     * Get the user this response relates to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the suspicious login that triggered this response
     */
    public function suspiciousLogin()
    {
        return $this->belongsTo(SuspiciousLogin::class);
    }

    /**
     * Get the admin who triggered this manually
     */
    public function triggeredByAdmin()
    {
        return $this->belongsTo(User::class, 'triggered_by_admin_id');
    }

    /**
     * Get the admin who reviewed this response
     */
    public function reviewedByAdmin()
    {
        return $this->belongsTo(User::class, 'reviewed_by_admin_id');
    }

    /**
     * Scope: Get pending responses
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get executed responses
     */
    public function scopeExecuted($query)
    {
        return $query->where('status', 'executed');
    }

    /**
     * Scope: Get failed responses
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: Get by trigger type
     */
    public function scopeByTriggerType($query, $type)
    {
        return $query->where('trigger_type', $type);
    }

    /**
     * Scope: Get critical responses
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope: Get responses requiring confirmation
     */
    public function scopeRequiresConfirmation($query)
    {
        return $query->where('requires_user_confirmation', true);
    }

    /**
     * Scope: Get unconfirmed responses
     */
    public function scopeUnconfirmed($query)
    {
        return $query->where('requires_user_confirmation', true)
            ->where('user_confirmed', false);
    }

    /**
     * Scope: Get lockouts
     */
    public function scopeLockouts($query)
    {
        return $query->where('response_action', 'temporary_lockout')
            ->orWhere('response_action', 'lock_account');
    }

    /**
     * Scope: Get recent (last 24 hours)
     */
    public function scopeRecent($query)
    {
        return $query->where('triggered_at', '>=', now()->subDay());
    }

    /**
     * Mark as executed
     */
    public function markExecuted($result = null)
    {
        $this->status = 'executed';
        $this->executed_at = now();
        if ($result) {
            $this->execution_result = $result;
        }
        $this->addToHistory('executed', ['executed_at' => now()]);

        return $this->save();
    }

    /**
     * Mark as failed
     */
    public function markFailed($error = null)
    {
        $this->status = 'failed';
        if ($error) {
            $this->error_message = $error;
        }
        $this->addToHistory('failed', ['error' => $error, 'failed_at' => now()]);

        return $this->save();
    }

    /**
     * Mark as cancelled
     */
    public function markCancelled($reason = null)
    {
        $this->status = 'cancelled';
        $this->addToHistory('cancelled', ['reason' => $reason, 'cancelled_at' => now()]);

        return $this->save();
    }

    /**
     * Confirm user confirmation
     */
    public function confirmByUser($confirmationDetails = null)
    {
        $this->user_confirmed = true;
        $this->user_confirmation_at = now();
        if ($confirmationDetails) {
            $this->user_confirmation_details = $confirmationDetails;
        }
        $this->addToHistory('user_confirmed', ['confirmed_at' => now()]);

        return $this->save();
    }

    /**
     * Notify user
     */
    public function notifyUser($method = 'email')
    {
        $this->user_notified = true;
        $this->notification_sent_at = now();
        $this->notification_method = $method;
        $this->addToHistory('user_notified', ['method' => $method, 'notified_at' => now()]);

        return $this->save();
    }

    /**
     * Add record to action history
     */
    public function addToHistory($action, $details = [])
    {
        $history = $this->action_history ?? [];
        $history[] = [
            'action' => $action,
            'timestamp' => now()->toIso8601String(),
            'details' => $details,
        ];
        $this->action_history = $history;

        return $this;
    }

    /**
     * Review this response
     */
    public function reviewBy($adminId, $notes = null)
    {
        $this->reviewed_at = now();
        $this->reviewed_by_admin_id = $adminId;
        if ($notes) {
            $this->admin_notes = $notes;
        }
        $this->addToHistory('reviewed', ['reviewed_at' => now(), 'notes' => $notes]);

        return $this->save();
    }

    /**
     * Get trigger type label
     */
    public function getTriggerTypeLabel()
    {
        return match ($this->trigger_type) {
            'anomaly_detection' => 'Phát hiện bất thường (Anomaly Detection)',
            'manual_trigger' => 'Kích hoạt thủ công (Manual Trigger)',
            'scheduled' => 'Lên lịch (Scheduled)',
            'threshold_breach' => 'Vượt ngưỡng (Threshold Breach)',
            default => $this->trigger_type,
        };
    }

    /**
     * Get response action label
     */
    public function getResponseActionLabel()
    {
        return match ($this->response_action) {
            'send_alert' => 'Gửi cảnh báo (Send Alert)',
            'request_confirmation' => 'Yêu cầu xác nhận (Request Confirmation)',
            'lock_account' => 'Khóa tài khoản (Lock Account)',
            'block_ip' => 'Chặn IP (Block IP)',
            'logout_all_sessions' => 'Đăng xuất tất cả (Logout All)',
            'force_2fa_reauth' => 'Buộc xác thực 2FA (Force 2FA)',
            'temporary_lockout' => 'Khóa tạm thời (Temporary Lockout)',
            default => $this->response_action,
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        return match ($this->status) {
            'pending' => 'Chờ xử lý (Pending)',
            'in_progress' => 'Đang xử lý (In Progress)',
            'executed' => 'Đã thực thi (Executed)',
            'failed' => 'Thất bại (Failed)',
            'cancelled' => 'Đã hủy (Cancelled)',
            'expired' => 'Hết hạn (Expired)',
            default => $this->status,
        };
    }

    /**
     * Get severity label
     */
    public function getSeverityLabel()
    {
        return match ($this->severity) {
            'low' => 'Thấp (Low)',
            'medium' => 'Trung bình (Medium)',
            'high' => 'Cao (High)',
            'critical' => 'Nguy cấp (Critical)',
            default => $this->severity,
        };
    }
}
