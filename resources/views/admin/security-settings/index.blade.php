@extends('layouts.app')

@section('title', 'Admin Security Settings')

@section('content')
<div class="security-settings py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-shield-alt"></i> Security Settings</h1>
                <small class="text-muted">Configure advanced security policies and configurations</small>
            </div>
            <div>
                <a href="{{ route('admin.security-settings.export') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-download"></i> Export Settings
                </a>
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#restoreModal">
                    <i class="fas fa-upload"></i> Restore Settings
                </button>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#general" data-bs-toggle="tab" role="tab">
                    <i class="fas fa-cog"></i> General Security
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#threefa" data-bs-toggle="tab" role="tab">
                    <i class="fas fa-key"></i> 3FA Configuration
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#ip" data-bs-toggle="tab" role="tab">
                    <i class="fas fa-network-wired"></i> IP Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#alerts" data-bs-toggle="tab" role="tab">
                    <i class="fas fa-bell"></i> Alert Thresholds
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#sessions" data-bs-toggle="tab" role="tab">
                    <i class="fas fa-lock"></i> Sessions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#encryption" data-bs-toggle="tab" role="tab">
                    <i class="fas fa-lock-open"></i> Encryption
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#audit" data-bs-toggle="tab" role="tab">
                    <i class="fas fa-history"></i> Audit Log
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- General Security Tab -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-cog"></i> General Security Settings</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.security-settings.update-general') }}">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Session Timeout (minutes)</label>
                                    <input type="number" name="session_timeout" class="form-control" 
                                           value="{{ $settings['general']['session_timeout'] ?? 60 }}"
                                           min="5" max="1440" required>
                                    <small class="text-muted">Auto-logout duration</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password Expiry (days)</label>
                                    <input type="number" name="password_expiry_days" class="form-control"
                                           value="{{ $settings['general']['password_expiry_days'] ?? 90 }}"
                                           min="0" max="365" required>
                                    <small class="text-muted">0 = never expires</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Failed Login Attempts</label>
                                    <input type="number" name="failed_login_attempts" class="form-control"
                                           value="{{ $settings['general']['failed_login_attempts'] ?? 5 }}"
                                           min="3" max="20" required>
                                    <small class="text-muted">Before account lockout</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lockout Duration (minutes)</label>
                                    <input type="number" name="lockout_duration" class="form-control"
                                           value="{{ $settings['general']['lockout_duration'] ?? 30 }}"
                                           min="5" max="1440" required>
                                    <small class="text-muted">Account lockout duration</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="require_password_change" 
                                               id="requirePwdChange" value="1"
                                               {{ ($settings['general']['require_password_change'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requirePwdChange">
                                            Require Password Change on Next Login
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="enable_audit_logging"
                                               id="enableAudit" value="1"
                                               {{ ($settings['general']['enable_audit_logging'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enableAudit">
                                            Enable Audit Logging
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Audit Log Retention (days)</label>
                                <input type="number" name="audit_retention_days" class="form-control"
                                       value="{{ $settings['general']['audit_retention_days'] ?? 90 }}"
                                       min="30" max="730" required>
                                <small class="text-muted">Logs older than this will be archived</small>
                            </div>

                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Current Configuration:</strong> These settings apply system-wide to all users
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 3FA Tab -->
            <div class="tab-pane fade" id="threefa" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-key"></i> Three Factor Authentication</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.security-settings.update-3fa') }}">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="enabled" 
                                               id="3faEnabled" value="1"
                                               {{ ($settings['threeFa']['enabled'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="3faEnabled">
                                            <strong>Enable 3FA System-wide</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="require_for_admin"
                                               id="3faAdmin" value="1"
                                               {{ ($settings['threeFa']['require_for_admin'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="3faAdmin">
                                            <strong>Required for Admin Users</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="require_for_users"
                                               id="3faUsers" value="1"
                                               {{ ($settings['threeFa']['require_for_users'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="3faUsers">
                                            <strong>Required for Regular Users</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Grace Period (days)</label>
                                    <input type="number" name="grace_period_days" class="form-control"
                                           value="{{ $settings['threeFa']['grace_period_days'] ?? 7 }}"
                                           min="0" max="30">
                                    <small class="text-muted">Days before 3FA enforcement</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Backup Codes Count</label>
                                <input type="number" name="backup_codes_count" class="form-control"
                                       value="{{ $settings['threeFa']['backup_codes_count'] ?? 10 }}"
                                       min="5" max="20" required>
                                <small class="text-muted">Number of backup codes per user</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Enabled 3FA Methods</label>
                                <div class="row">
                                    @foreach(['email' => 'Email', 'sms' => 'SMS', 'authenticator' => 'Authenticator App'] as $value => $label)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="methods[]"
                                                       value="{{ $value }}" id="method-{{ $value }}"
                                                       {{ in_array($value, $settings['threeFa']['methods'] ?? ['email', 'authenticator']) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="method-{{ $value }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Important:</strong> Changing 3FA requirements will affect user access
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save 3FA Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- IP Management Tab -->
            <div class="tab-pane fade" id="ip" role="tabpanel">
                <div class="row">
                    <!-- Whitelist -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0"><i class="fas fa-check-circle text-success"></i> IP Whitelist</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.security-settings.whitelist-add') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Add IP Address</label>
                                        <input type="text" name="ip_address" class="form-control"
                                               placeholder="e.g., 192.168.1.1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <input type="text" name="description" class="form-control"
                                               placeholder="e.g., Office Network">
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-plus"></i> Add to Whitelist
                                    </button>
                                </form>

                                <hr>

                                <div class="whitelisted-ips">
                                    @forelse($settings['ipManagement']['whitelist'] as $entry)
                                        <div class="card border-0 bg-light mb-2">
                                            <div class="card-body py-2 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong class="text-monospace">{{ $entry['ip'] }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $entry['description'] ?? 'No description' }}</small>
                                                </div>
                                                <form method="POST" action="{{ route('admin.security-settings.whitelist-remove') }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="ip_address" value="{{ $entry['ip'] }}">
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Remove this IP?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="alert alert-info mb-0">
                                            <small><i class="fas fa-info-circle"></i> No whitelisted IPs</small>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Blacklist -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0"><i class="fas fa-times-circle text-danger"></i> IP Blacklist</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.security-settings.blacklist-add') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Add IP Address</label>
                                        <input type="text" name="ip_address" class="form-control"
                                               placeholder="e.g., 203.0.113.42" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Reason</label>
                                        <input type="text" name="reason" class="form-control"
                                               placeholder="e.g., Suspicious activity">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Duration (hours)</label>
                                        <input type="number" name="duration_hours" class="form-control"
                                               value="24" min="1" max="8760" required>
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        <i class="fas fa-ban"></i> Add to Blacklist
                                    </button>
                                </form>

                                <hr>

                                <div class="blacklisted-ips">
                                    @forelse($settings['ipManagement']['blacklist'] as $entry)
                                        <div class="card border-0 bg-light mb-2">
                                            <div class="card-body py-2 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong class="text-monospace">{{ $entry['ip'] }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $entry['reason'] ?? 'Suspicious activity' }}</small>
                                                    <br>
                                                    <small class="text-warning">Expires: {{ $entry['blocks_until']->format('M d, H:i') ?? 'N/A' }}</small>
                                                </div>
                                                <form method="POST" action="{{ route('admin.security-settings.blacklist-remove') }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="ip_address" value="{{ $entry['ip'] }}">
                                                    <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Remove blacklist?')">
                                                        <i class="fas fa-unlock"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="alert alert-info mb-0">
                                            <small><i class="fas fa-info-circle"></i> No blacklisted IPs</small>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Thresholds Tab -->
            <div class="tab-pane fade" id="alerts" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-bell"></i> Alert Thresholds</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.security-settings.update-thresholds') }}">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Failed Login Attempts Threshold</label>
                                    <input type="number" name="failed_login_attempts" class="form-control"
                                           value="{{ $settings['alertThresholds']['failed_login_attempts'] ?? 5 }}"
                                           min="3" max="20" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Rapid Location Changes</label>
                                    <input type="number" name="rapid_locations" class="form-control"
                                           value="{{ $settings['alertThresholds']['rapid_locations'] ?? 3 }}"
                                           min="2" max="10" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">New Devices Per Day</label>
                                    <input type="number" name="new_devices_per_day" class="form-control"
                                           value="{{ $settings['alertThresholds']['new_devices_per_day'] ?? 5 }}"
                                           min="3" max="20" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Suspicious Activities Per Hour</label>
                                    <input type="number" name="suspicious_activities_per_hour" class="form-control"
                                           value="{{ $settings['alertThresholds']['suspicious_activities_per_hour'] ?? 10 }}"
                                           min="5" max="50" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email Alert Threshold</label>
                                    <select name="alert_email_threshold" class="form-select" required>
                                        <option value="low" {{ ($settings['alertThresholds']['alert_email_threshold'] ?? 'high') === 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ ($settings['alertThresholds']['alert_email_threshold'] ?? 'high') === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ ($settings['alertThresholds']['alert_email_threshold'] ?? 'high') === 'high' ? 'selected' : '' }}>High</option>
                                        <option value="critical" {{ ($settings['alertThresholds']['alert_email_threshold'] ?? 'high') === 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Escalation Threshold</label>
                                    <select name="escalation_threshold" class="form-select" required>
                                        <option value="medium" {{ ($settings['alertThresholds']['escalation_threshold'] ?? 'critical') === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ ($settings['alertThresholds']['escalation_threshold'] ?? 'critical') === 'high' ? 'selected' : '' }}>High</option>
                                        <option value="critical" {{ ($settings['alertThresholds']['escalation_threshold'] ?? 'critical') === 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Thresholds
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sessions Tab -->
            <div class="tab-pane fade" id="sessions" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-lock"></i> Session Management</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.security-settings.update-sessions') }}">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Max Concurrent Sessions</label>
                                    <input type="number" name="max_concurrent_sessions" class="form-control"
                                           value="{{ $settings['sessionManagement']['max_concurrent_sessions'] ?? 3 }}"
                                           min="1" max="10" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Session Timeout (minutes)</label>
                                    <input type="number" name="session_timeout" class="form-control"
                                           value="{{ $settings['sessionManagement']['session_timeout'] ?? 60 }}"
                                           min="15" max="1440" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="require_reauth_sensitive_ops"
                                               id="reauthSensitive" value="1"
                                               {{ ($settings['sessionManagement']['require_reauth_sensitive_ops'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="reauthSensitive">
                                            Require Re-authentication for Sensitive Operations
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Re-auth Timeout (minutes)</label>
                                    <input type="number" name="reauth_timeout" class="form-control"
                                           value="{{ $settings['sessionManagement']['reauth_timeout'] ?? 5 }}"
                                           min="1" max="15">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="track_session_locations"
                                               id="trackLocations" value="1"
                                               {{ ($settings['sessionManagement']['track_session_locations'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="trackLocations">
                                            Track Session Locations
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notify_new_sessions"
                                               id="notifyNew" value="1"
                                               {{ ($settings['sessionManagement']['notify_new_sessions'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="notifyNew">
                                            Notify on New Sessions
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Session Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Encryption Tab -->
            <div class="tab-pane fade" id="encryption" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-lock-open"></i> Encryption Settings</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.security-settings.update-encryption') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="enable_field_encryption"
                                           id="enableEncryption" value="1"
                                           {{ ($settings['encryption']['enable_field_encryption'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enableEncryption">
                                        <strong>Enable Field-Level Encryption</strong>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Encryption Algorithm</label>
                                <select name="encryption_algorithm" class="form-select" required>
                                    <option value="AES-256" {{ ($settings['encryption']['encryption_algorithm'] ?? 'AES-256') === 'AES-256' ? 'selected' : '' }}>AES-256 (Recommended)</option>
                                    <option value="AES-128" {{ ($settings['encryption']['encryption_algorithm'] ?? 'AES-256') === 'AES-128' ? 'selected' : '' }}>AES-128</option>
                                    <option value="ChaCha20" {{ ($settings['encryption']['encryption_algorithm'] ?? 'AES-256') === 'ChaCha20' ? 'selected' : '' }}>ChaCha20</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sensitive Fields to Encrypt</label>
                                <div class="row">
                                    @foreach(['email' => 'Email', 'phone' => 'Phone', 'ssn' => 'SSN/ID', 'address' => 'Address'] as $value => $label)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="sensitive_fields[]"
                                                       value="{{ $value }}" id="field-{{ $value }}"
                                                       {{ in_array($value, $settings['encryption']['sensitive_fields'] ?? ['email', 'phone']) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="field-{{ $value }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="auto_decrypt_on_retrieve"
                                               id="autoDecrypt" value="1"
                                               {{ ($settings['encryption']['auto_decrypt_on_retrieve'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="autoDecrypt">
                                            Auto-decrypt on Retrieve
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="log_encryption_key_access"
                                               id="logKeyAccess" value="1"
                                               {{ ($settings['encryption']['log_encryption_key_access'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="logKeyAccess">
                                            Log Encryption Key Access
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Warning:</strong> Changing encryption settings requires data re-encryption
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Encryption Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Audit Log Tab -->
            <div class="tab-pane fade" id="audit" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Settings Audit Log</h5>
                        <a href="{{ route('admin.audit.index') }}" class="btn btn-sm btn-outline-primary">
                            View Full Audit Log
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Admin</th>
                                    <th>Action</th>
                                    <th>Changes</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mar 3, 2026 14:32</td>
                                    <td>Admin User</td>
                                    <td><span class="badge bg-primary">Configuration Updated</span></td>
                                    <td>Session Timeout: 60 → 45 minutes</td>
                                    <td><a href="#" class="btn btn-sm btn-outline-secondary">View</a></td>
                                </tr>
                                <tr>
                                    <td>Mar 2, 2026 10:15</td>
                                    <td>Admin User</td>
                                    <td><span class="badge bg-warning">IP Added</span></td>
                                    <td>Whitelisted: 192.168.1.100</td>
                                    <td><a href="#" class="btn btn-sm btn-outline-secondary">View</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Restore Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.security-settings.restore') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Restore Settings Backup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Backup File</label>
                        <input type="file" name="backup_file" class="form-control" accept=".json" required>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This will overwrite all current settings
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                        Restore Backup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .security-settings {
        background-color: #f5f7fa;
    }

    .nav-tabs .nav-link {
        color: #495057;
        border: none;
        border-bottom: 2px solid transparent;
    }

    .nav-tabs .nav-link:hover {
        border-bottom-color: #0d6efd;
    }

    .nav-tabs .nav-link.active {
        border-bottom-color: #0d6efd;
        color: #0d6efd;
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }

    .text-monospace {
        font-family: 'Courier New', monospace;
        font-size: 13px;
    }

    .whitelisted-ips, .blacklisted-ips {
        max-height: 400px;
        overflow-y: auto;
    }
</style>
@endsection
