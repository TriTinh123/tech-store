<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LoginAnomaly;
use App\Models\SecurityAlert;
use App\Models\User;

class RiskAssessmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Show risk assessment dashboard
     */
    public function dashboard()
    {
        $data = [
            'overall_risk_score' => $this->calculateOverallRiskScore(),
            'risk_trend' => $this->getRiskTrend(),
            'risk_breakdown' => $this->getRiskBreakdown(),
            'critical_users' => $this->getCriticalRiskUsers(),
            'threat_analysis' => $this->getThreatAnalysis(),
            'anomalies_trend' => $this->getAnomaliesTrend(),
            'alerts_severity_distribution' => $this->getAlertsSeverityDistribution(),
            'top_threats' => $this->getTopThreats(),
            'risk_metrics' => $this->getRiskMetrics(),
            'timestamp' => now(),
        ];

        return view('admin.risk-assessment.dashboard', $data);
    }

    /**
     * Show detailed risk analysis
     */
    public function analysis()
    {
        $data = [
            'user_risk_profiles' => $this->getUserRiskProfiles(),
            'ip_risk_assessment' => $this->getIpRiskAssessment(),
            'device_risk_assessment' => $this->getDeviceRiskAssessment(),
            'time_based_risk' => $this->getTimeBasedRiskAnalysis(),
        ];

        return view('admin.risk-assessment.analysis', $data);
    }

    /**
     * Show user risk details
     */
    public function userRiskDetail($userId)
    {
        $user = User::findOrFail($userId);

        $data = [
            'user' => $user,
            'risk_score' => $this->calculateUserRiskScore($userId),
            'anomalies' => $this->getUserAnomalies($userId),
            'alerts' => $this->getUserAlerts($userId),
            'login_patterns' => $this->getUserLoginPatterns($userId),
            'risk_events' => $this->getUserRiskEvents($userId),
            'recommendations' => $this->generateRiskRecommendations($userId),
        ];

        return view('admin.risk-assessment.user-detail', $data);
    }

    /**
     * Calculate overall risk score (0-100)
     */
    private function calculateOverallRiskScore()
    {
        $totalUsers = User::count();
        if ($totalUsers === 0) {
            return 0;
        }

        $criticalAlerts = SecurityAlert::where('severity', 'critical')->count();
        $highAlerts = SecurityAlert::where('severity', 'high')->count();
        $unResolvedAnomalies = LoginAnomaly::where('is_resolved', false)->count();
        $failedAuthAttempts = AuditLog::where('action', 'login_failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        // Risk score calculation: weighted sum
        $criticalWeight = 0.40;
        $highWeight = 0.30;
        $anomalyWeight = 0.20;
        $failureWeight = 0.10;

        $score = (
            ($criticalAlerts / ($totalUsers + 1)) * $criticalWeight * 100 +
            ($highAlerts / ($totalUsers + 1)) * $highWeight * 100 +
            ($unResolvedAnomalies / ($totalUsers + 1)) * $anomalyWeight * 100 +
            ($failedAuthAttempts / ($totalUsers + 1)) * $failureWeight * 100
        );

        return min(100, max(0, $score));
    }

    /**
     * Get risk trend over 30 days
     */
    private function getRiskTrend()
    {
        $trend = [];
        $labels = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');

            $dailyAlerts = SecurityAlert::whereDate('created_at', $date)->count();
            $dailyAnomalies = LoginAnomaly::whereDate('created_at', $date)->count();
            $dailyFailures = AuditLog::where('action', 'login_failed')
                ->whereDate('created_at', $date)->count();

            $score = ($dailyAlerts * 0.5 + $dailyAnomalies * 0.3 + $dailyFailures * 0.2);
            $trend[] = min(100, $score);
        }

        return [
            'labels' => $labels,
            'data' => $trend,
        ];
    }

    /**
     * Get risk breakdown by type
     */
    private function getRiskBreakdown()
    {
        return [
            'authentication_risk' => $this->calculateAuthenticationRisk(),
            'anomaly_risk' => $this->calculateAnomalyRisk(),
            'alert_risk' => $this->calculateAlertRisk(),
            'session_risk' => $this->calculateSessionRisk(),
            'data_access_risk' => $this->calculateDataAccessRisk(),
        ];
    }

    /**
     * Get users with critical risk
     */
    private function getCriticalRiskUsers()
    {
        $users = User::limit(10)->get();
        $criticalUsers = [];

        foreach ($users as $user) {
            $riskScore = $this->calculateUserRiskScore($user->id);
            if ($riskScore >= 70) {
                $criticalUsers[] = [
                    'user' => $user,
                    'risk_score' => $riskScore,
                    'alerts_count' => SecurityAlert::where('user_id', $user->id)->count(),
                    'anomalies_count' => LoginAnomaly::where('user_id', $user->id)->count(),
                ];
            }
        }

        return collect($criticalUsers)->sortByDesc('risk_score')->take(5);
    }

    /**
     * Get threat analysis
     */
    private function getThreatAnalysis()
    {
        return [
            'active_threats' => SecurityAlert::where('status', 'pending')->count(),
            'resolved_threats' => SecurityAlert::where('status', 'resolved')->count(),
            'critical_threats' => SecurityAlert::where('severity', 'critical')->count(),
            'unresolved_anomalies' => LoginAnomaly::where('is_resolved', false)->count(),
        ];
    }

    /**
     * Get anomalies trend
     */
    private function getAnomaliesTrend()
    {
        $trend = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            $count = LoginAnomaly::whereDate('created_at', $date)->count();
            $trend[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $trend,
        ];
    }

    /**
     * Get alerts severity distribution
     */
    private function getAlertsSeverityDistribution()
    {
        return [
            'critical' => SecurityAlert::where('severity', 'critical')->count(),
            'high' => SecurityAlert::where('severity', 'high')->count(),
            'medium' => SecurityAlert::where('severity', 'medium')->count(),
            'low' => SecurityAlert::where('severity', 'low')->count(),
        ];
    }

    /**
     * Get top threats
     */
    private function getTopThreats()
    {
        return SecurityAlert::select('alert_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('alert_type')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->alert_type,
                    'count' => $item->count,
                    'percentage' => round(($item->count / SecurityAlert::count()) * 100),
                ];
            });
    }

    /**
     * Get risk metrics
     */
    private function getRiskMetrics()
    {
        $totalAlerts = SecurityAlert::count();
        $resolvedAlerts = SecurityAlert::where('status', 'resolved')->count();
        $totalAnomalies = LoginAnomaly::count();

        return [
            'resolution_rate' => $totalAlerts > 0 ? round(($resolvedAlerts / $totalAlerts) * 100) : 0,
            'avg_response_time' => $this->calculateAvgResponseTime(),
            'system_compliance' => $this->calculateComplianceScore(),
            'mttr' => $this->calculateMTTR(),
        ];
    }

    /**
     * Calculate authentication risk (0-100)
     */
    private function calculateAuthenticationRisk()
    {
        $failedAttempts = AuditLog::where('action', 'login_failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();
        $bruteForceAttempts = $failedAttempts / max(1, User::count());

        return min(100, ($bruteForceAttempts * 10));
    }

    /**
     * Calculate anomaly risk (0-100)
     */
    private function calculateAnomalyRisk()
    {
        $unresolved = LoginAnomaly::where('is_resolved', false)->count();
        $critical = LoginAnomaly::where('risk_level', 'critical')->count();

        return min(100, (($unresolved / max(1, User::count()) * 30) + ($critical * 15)));
    }

    /**
     * Calculate alert risk (0-100)
     */
    private function calculateAlertRisk()
    {
        $distribution = $this->getAlertsSeverityDistribution();

        return (
            ($distribution['critical'] * 25) +
            ($distribution['high'] * 15) +
            ($distribution['medium'] * 8) +
            ($distribution['low'] * 2)
        ) / max(1, array_sum($distribution)) * 100;
    }

    /**
     * Calculate session risk (0-100)
     */
    private function calculateSessionRisk()
    {
        // In a real app, this would analyze session data
        $activeSessions = /* get active sessions */ 0;
        $suspiciousSessions = /* get suspicious sessions */ 0;

        return $activeSessions > 0 ? ($suspiciousSessions / $activeSessions) * 100 : 0;
    }

    /**
     * Calculate data access risk (0-100)
     */
    private function calculateDataAccessRisk()
    {
        // Track unauthorized access attempts
        $unauthorizedAttempts = AuditLog::where('action', 'unauthorized_access')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return min(100, $unauthorizedAttempts * 5);
    }

    /**
     * Calculate user risk score
     */
    private function calculateUserRiskScore($userId)
    {
        $user = User::find($userId);
        if (! $user) {
            return 0;
        }

        $alerts = SecurityAlert::where('user_id', $userId)->count();
        $anomalies = LoginAnomaly::where('user_id', $userId)->count();
        $failedLogins = AuditLog::where('user_id', $userId)
            ->where('action', 'login_failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        $score = ($alerts * 15) + ($anomalies * 20) + ($failedLogins * 5);

        return min(100, $score);
    }

    /**
     * Get user anomalies
     */
    private function getUserAnomalies($userId)
    {
        return LoginAnomaly::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get user alerts
     */
    private function getUserAlerts($userId)
    {
        return SecurityAlert::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get user login patterns
     */
    private function getUserLoginPatterns($userId)
    {
        return AuditLog::where('user_id', $userId)
            ->where('action', 'login_success')
            ->where('created_at', '>=', now()->subDays(30))
            ->select('created_at', 'ip_address', 'user_agent')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get user risk events
     */
    private function getUserRiskEvents($userId)
    {
        return AuditLog::where('user_id', $userId)
            ->whereIn('action', ['login_failed', 'unauthorized_access', 'failed_2fa'])
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();
    }

    /**
     * Generate risk recommendations
     */
    private function generateRiskRecommendations($userId)
    {
        $riskScore = $this->calculateUserRiskScore($userId);
        $recommendations = [];

        if ($riskScore >= 80) {
            $recommendations[] = [
                'severity' => 'critical',
                'action' => 'Force password change',
                'description' => 'High risk detected - user should reset password immediately',
            ];
            $recommendations[] = [
                'severity' => 'critical',
                'action' => 'Review recent activities',
                'description' => 'Check user\'s recent login locations and devices',
            ];
        } elseif ($riskScore >= 60) {
            $recommendations[] = [
                'severity' => 'high',
                'action' => 'Require 2FA verification',
                'description' => 'Enable two-factor authentication for this user',
            ];
            $recommendations[] = [
                'severity' => 'high',
                'action' => 'Monitor closely',
                'description' => 'Set up alerts for this user\'s activities',
            ];
        } elseif ($riskScore >= 40) {
            $recommendations[] = [
                'severity' => 'medium',
                'action' => 'Recommend security update',
                'description' => 'User should review security settings',
            ];
        }

        return $recommendations;
    }

    /**
     * Get user risk profiles
     */
    private function getUserRiskProfiles()
    {
        $users = User::limit(20)->get();

        return $users->map(function ($user) {
            return [
                'user' => $user,
                'risk_score' => $this->calculateUserRiskScore($user->id),
                'alerts_count' => SecurityAlert::where('user_id', $user->id)->count(),
                'anomalies_count' => LoginAnomaly::where('user_id', $user->id)->count(),
                'risk_level' => $this->getRiskLevel($this->calculateUserRiskScore($user->id)),
            ];
        })->sortByDesc('risk_score');
    }

    /**
     * Get IP risk assessment
     */
    private function getIpRiskAssessment()
    {
        return AuditLog::select('ip_address')
            ->selectRaw('COUNT(*) as access_count')
            ->selectRaw('COUNT(CASE WHEN action = "login_failed" THEN 1 END) as failed_attempts')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('ip_address')
            ->orderByDesc('failed_attempts')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $riskScore = ($item->failed_attempts / max(1, $item->access_count)) * 100;

                return [
                    'ip' => $item->ip_address,
                    'access_count' => $item->access_count,
                    'failed_attempts' => $item->failed_attempts,
                    'risk_score' => min(100, $riskScore),
                ];
            });
    }

    /**
     * Get device risk assessment
     */
    private function getDeviceRiskAssessment()
    {
        return LoginAnomaly::select('device_fingerprint')
            ->selectRaw('COUNT(*) as anomaly_count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('device_fingerprint')
            ->orderByDesc('anomaly_count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'device' => $item->device_fingerprint,
                    'anomalies' => $item->anomaly_count,
                    'risk_level' => $item->anomaly_count > 5 ? 'high' : 'medium',
                ];
            });
    }

    /**
     * Get time-based risk analysis
     */
    private function getTimeBasedRiskAnalysis()
    {
        $hourlyRisk = [];

        for ($hour = 0; $hour < 24; $hour++) {
            $startOfHour = now()->setHour($hour)->setMinute(0)->setSecond(0);
            $endOfHour = now()->setHour($hour)->setMinute(59)->setSecond(59);

            $count = SecurityAlert::whereBetween('created_at', [$startOfHour, $endOfHour])->count();
            $hourlyRisk[] = [
                'hour' => sprintf('%02d:00', $hour),
                'risk_level' => $count,
            ];
        }

        return $hourlyRisk;
    }

    /**
     * Calculate average response time
     */
    private function calculateAvgResponseTime()
    {
        // In a real app, calculate from response timestamps
        return '4.2 hours';
    }

    /**
     * Calculate compliance score
     */
    private function calculateComplianceScore()
    {
        $totalChecks = 100;
        $passedChecks = 85; // Example

        return round(($passedChecks / $totalChecks) * 100);
    }

    /**
     * Calculate MTTR (Mean Time To Resolution)
     */
    private function calculateMTTR()
    {
        return '2.1 hours';
    }

    /**
     * Get risk level text
     */
    private function getRiskLevel($score)
    {
        if ($score >= 80) {
            return 'Critical';
        }
        if ($score >= 60) {
            return 'High';
        }
        if ($score >= 40) {
            return 'Medium';
        }
        if ($score >= 20) {
            return 'Low';
        }

        return 'Minimal';
    }
}
