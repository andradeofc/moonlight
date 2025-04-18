// resources/views/settings.blade.php

@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<h1 class="mb-4">Settings</h1>

<div class="row">
    <div class="col-md-3">
        <div class="list-group mb-4">
            <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                <i class="fas fa-cog me-2"></i> General
            </a>
            <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">
                <i class="fas fa-shield-alt me-2"></i> Security
            </a>
            <a href="#filters" class="list-group-item list-group-item-action" data-bs-toggle="list">
                <i class="fas fa-filter me-2"></i> Filter Settings
            </a>
            <a href="#account" class="list-group-item list-group-item-action" data-bs-toggle="list">
                <i class="fas fa-user me-2"></i> Account
            </a>
        </div>
        
        <div class="card bg-light">
            <div class="card-body">
                <h5><i class="fas fa-info-circle me-2"></i> About</h5>
                <p class="mb-2">Cloacker v1.0.0</p>
                <p class="mb-0 text-muted">Developed by Your Company</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="tab-content">
            <!-- General Settings -->
            <div class="tab-pane fade show active" id="general">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">General Settings</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="default_language" class="form-label">Default Language</label>
                                <select class="form-select" id="default_language" name="default_language">
                                    <option value="en" selected>English</option>
                                    <option value="es">Spanish</option>
                                    <option value="pt">Portuguese</option>
                                    <option value="fr">French</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="default_traffic_source" class="form-label">Default Traffic Source</label>
                                <select class="form-select" id="default_traffic_source" name="default_traffic_source">
                                    <option value="facebook" selected>Facebook</option>
                                    <option value="google">Google Ads</option>
                                    <option value="tiktok">TikTok</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="cache_enabled" 
                                           name="cache_enabled" checked>
                                    <label class="form-check-label" for="cache_enabled">
                                        Enable Result Caching
                                    </label>
                                    <div class="form-text">
                                        Caching improves performance by storing filter results for repeated visitors
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="cache_duration" class="form-label">Cache Duration (minutes)</label>
                                <input type="number" class="form-control" id="cache_duration" 
                                       name="cache_duration" value="10" min="1" max="1440">
                                <div class="form-text">
                                    How long to keep cached results (1-1440 minutes)
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Security Settings -->
            <div class="tab-pane fade" id="security">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Security Settings</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.security') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable_2fa" 
                                           name="enable_2fa">
                                    <label class="form-check-label" for="enable_2fa">
                                        Enable Two-Factor Authentication
                                    </label>
                                    <div class="form-text">
                                        Add an extra layer of security to your account
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="activity_logging" 
                                           name="activity_logging" checked>
                                    <label class="form-check-label" for="activity_logging">
                                        Enable Activity Logging
                                    </label>
                                    <div class="form-text">
                                        Log all admin actions for security and auditing purposes
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h5>Change Password</h5>
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" 
                                       name="current_password">
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" 
                                       name="new_password">
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="new_password_confirmation" 
                                       name="new_password_confirmation">
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Filter Settings -->
            <div class="tab-pane fade" id="filters">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Filter Settings</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.filters') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="block_vpn" 
                                           name="block_vpn" checked>
                                    <label class="form-check-label" for="block_vpn">
                                        Block VPN/Proxy Traffic
                                    </label>
                                    <div class="form-text">
                                        Automatically detect and filter traffic from VPNs and proxies
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="block_bots" 
                                           name="block_bots" checked>
                                    <label class="form-check-label" for="block_bots">
                                        Block Bot Traffic
                                    </label>
                                    <div class="form-text">
                                        Automatically detect and filter traffic from bots and crawlers
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="suspicious_ips" class="form-label">Suspicious IP Ranges</label>
                                <textarea class="form-control" id="suspicious_ips" name="suspicious_ips" rows="4">173.252.127.
66.220.149.
2a03:2880:
216.58.194.
172.217.</textarea>
                                <div class="form-text">
                                    Enter IP ranges to block, one per line
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bot_signatures" class="form-label">Bot Signatures</label>
                                <textarea class="form-control" id="bot_signatures" name="bot_signatures" rows="4">bot
crawler
spider
lighthouse
slurp
googlebot
bingbot
yandex
baidu
facebookexternalhit</textarea>
                                <div class="form-text">
                                    Enter bot signatures to detect in User-Agent, one per line
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="blocked_referrers" class="form-label">Blocked Referrers</label>
                                <textarea class="form-control" id="blocked_referrers" name="blocked_referrers" rows="4">facebook.com/ads
business.facebook.com
adsmanager
google.com/adsense
adssettings.google.com
ads.google.com</textarea>
                                <div class="form-text">
                                    Enter referrers to block, one per line
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Account Settings -->
            <div class="tab-pane fade" id="account">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Account Settings</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.account') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ auth()->user()->name }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ auth()->user()->email }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="timezone" class="form-label">Timezone</label>
                                <select class="form-select" id="timezone" name="timezone">
                                    <option value="UTC" selected>UTC</option>
                                    <option value="America/New_York">America/New_York</option>
                                    <option value="America/Los_Angeles">America/Los_Angeles</option>
                                    <option value="Europe/London">Europe/London</option>
                                    <option value="Europe/Paris">Europe/Paris</option>
                                    <option value="Asia/Tokyo">Asia/Tokyo</option>
                                </select>
                            </div>
                            
                            <hr>
                            
                            <h5>API Access</h5>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="api_enabled" 
                                           name="api_enabled">
                                    <label class="form-check-label" for="api_enabled">
                                        Enable API Access
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="api_key" class="form-label">API Key</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="api_key" 
                                           value="•••••••••••••••••••••••••••••••" readonly>
                                    <button class="btn btn-outline-secondary" type="button" id="generate_api_key">
                                        Generate New
                                    </button>
                                </div>
                                <div class="form-text">
                                    <strong>Warning:</strong> Generating a new API key will invalidate any existing key
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // API Key Generation
    document.getElementById('generate_api_key').addEventListener('click', function() {
        if (confirm('Are you sure you want to generate a new API key? This will invalidate any existing key.')) {
            // In a real implementation, this would make an AJAX request to generate a new key
            const fakeApiKey = 'key_' + Math.random().toString(36).substring(2, 15);
            document.getElementById('api_key').value = fakeApiKey;
        }
    });
    
    // Handle tab navigation from URL
    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash;
        if (hash) {
            const tab = document.querySelector(`.list-group-item[href="${hash}"]`);
            if (tab) {
                tab.click();
            }
        }
    });
</script>
@endsection