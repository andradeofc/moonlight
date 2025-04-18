
@extends('layouts.app')

@section('title', 'Create Campaign')

@section('content')
<div class="mb-4">
    <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Campaigns
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Create New Campaign</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('campaigns.store') }}" method="POST">
            @csrf
            
            <div class="row mb-4">
                <div class="col-12">
                    <h4>Campaign Information</h4>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Campaign Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Choose a name to easily identify this campaign</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="domain_id" class="form-label">Domain</label>
                        <select class="form-select @error('domain_id') is-invalid @enderror" 
                                id="domain_id" name="domain_id" required>
                            <option value="">Select a Domain</option>
                            @foreach($domains as $domain)
                                <option value="{{ $domain->id }}" @if(old('domain_id') == $domain->id) selected @endif>
                                    {{ $domain->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('domain_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Select a verified domain</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="language" class="form-label">Language</label>
                        <select class="form-select @error('language') is-invalid @enderror" 
                                id="language" name="language" required>
                            <option value="">Select Language</option>
                            <option value="en" @if(old('language') == 'en') selected @endif>English</option>
                            <option value="es" @if(old('language') == 'es') selected @endif>Spanish</option>
                            <option value="pt" @if(old('language') == 'pt') selected @endif>Portuguese</option>
                            <option value="fr" @if(old('language') == 'fr') selected @endif>French</option>
                            <option value="de" @if(old('language') == 'de') selected @endif>German</option>
                            <option value="it" @if(old('language') == 'it') selected @endif>Italian</option>
                            <option value="ru" @if(old('language') == 'ru') selected @endif>Russian</option>
                        </select>
                        @error('language')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="traffic_source" class="form-label">Traffic Source</label>
                        <select class="form-select @error('traffic_source') is-invalid @enderror" 
                                id="traffic_source" name="traffic_source" required>
                            <option value="">Select Traffic Source</option>
                            <option value="facebook" @if(old('traffic_source') == 'facebook') selected @endif>Facebook</option>
                            <option value="google" @if(old('traffic_source') == 'google') selected @endif>Google Ads</option>
                            <option value="tiktok" @if(old('traffic_source') == 'tiktok') selected @endif>TikTok</option>
                            <option value="native" @if(old('traffic_source') == 'native') selected @endif>Native Ads</option>
                            <option value="other" @if(old('traffic_source') == 'other') selected @endif>Other</option>
                        </select>
                        @error('traffic_source')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="row mb-4">
                <div class="col-12">
                    <h4>Safe Page Configuration</h4>
                    <p class="text-muted">This is the page that will be shown to bots and reviewers.</p>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="safe_url" class="form-label">Safe Page URL</label>
                        <input type="url" class="form-control @error('safe_url') is-invalid @enderror" 
                               id="safe_url" name="safe_url" value="{{ old('safe_url') }}" required
                               placeholder="https://example.com/safe-page">
                        @error('safe_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">URL of your policy-compliant page</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Safe Page Method</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="safe_method" 
                            id="ml_redirect" value="ml_redirect" 
                            @if(old('safe_method', 'ml_redirect') == 'ml_redirect') checked @endif>
                        <label class="form-check-label" for="ml_redirect">
                            <strong>ML Redirect</strong> <span class="badge bg-success">Recommended</span>
                            <div class="text-muted">Direct redirect to your safe page</div>
                        </label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="radio" name="safe_method" 
                            id="pre_page" value="pre_page" 
                            @if(old('safe_method') == 'pre_page') checked @endif>
                        <label class="form-check-label" for="pre_page">
                            <strong>Pre Page</strong>
                            <div class="text-muted">Shows a policy-compliant page before redirecting to your safe page</div>
                        </label>
                    </div>
                    @error('safe_method')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <hr>
            
            <div class="row mb-4">
                <div class="col-12">
                    <h4>Offer Page Configuration</h4>
                    <p class="text-muted">This is the page that will be shown to real visitors.</p>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="offer_url" class="form-label">Offer Page URL</label>
                        <input type="url" class="form-control @error('offer_url') is-invalid @enderror" 
                               id="offer_url" name="offer_url" value="{{ old('offer_url') }}" required
                               placeholder="https://example.com/real-offer">
                        @error('offer_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">URL of your real offer page</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Offer Method</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="offer_method" 
                                   id="redirect" value="redirect" 
                                   @if(old('offer_method', 'redirect') == 'redirect') checked @endif>
                            <label class="form-check-label" for="redirect">
                                <strong>Redirect</strong> <span class="badge bg-success">Recommended</span>
                                <div class="text-muted">Direct redirect to your offer page</div>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="offer_method" 
                                   id="twr_mirror_offer" value="twr_mirror" 
                                   @if(old('offer_method') == 'twr_mirror') checked @endif>
                            <label class="form-check-label" for="twr_mirror_offer">
                                <strong>TWR Mirror</strong>
                                <div class="text-muted">Uses an iframe to display your offer page</div>
                            </label>
                        </div>
                        @error('offer_method')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-12 mt-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ab_testing" name="ab_testing" value="1">
                        <label class="form-check-label" for="ab_testing">
                            Enable A/B Testing for this campaign
                        </label>
                    </div>
                </div>
                
                <div id="ab_testing_section" class="col-md-12 mt-3" style="display: none;">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5>A/B Testing Configuration</h5>
                            <p>Add multiple offer URLs to test which one performs better.</p>
                            
                            <div id="ab_urls_container">
                                <div class="row mb-3 ab-url-row">
                                    <div class="col-md-8">
                                        <input type="url" class="form-control" name="ab_urls[]" 
                                               placeholder="https://example.com/variant-1">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" name="ab_weights[]" 
                                               placeholder="Weight (%)" min="1" max="100" value="50">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger remove-url-btn">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-sm btn-primary" id="add_url_btn">
                                <i class="fas fa-plus me-2"></i> Add Variant
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="row mb-4">
                <div class="col-12">
                    <h4>Traffic Targeting</h4>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="countries" class="form-label">Target Countries</label>
                        <select class="form-select" id="countries" name="countries[]" multiple required>
                        <option value="US">United States</option>
                            <option value="CA">Canada</option>
                            <option value="GB">United Kingdom</option>
                            <option value="AU">Australia</option>
                            <option value="DE">Germany</option>
                            <option value="FR">France</option>
                            <option value="IT">Italy</option>
                            <option value="ES">Spain</option>
                            <option value="BR">Brazil</option>
                            <option value="MX">Mexico</option>
                            <option value="JP">Japan</option>
                            <option value="IN">India</option>
                            <option value="RU">Russia</option>
                            <option value="CN">China</option>
                            <option value="AR">Argentina</option>
                            <option value="CL">Chile</option>
                            <option value="NL">Netherlands</option>
                            <option value="BE">Belgium</option>
                            <option value="KR">South Korea</option>
                            <option value="ZA">South Africa</option>
                            <!-- Add more countries as needed -->
                        </select>
                        @error('countries')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Hold Ctrl/Cmd to select multiple countries</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="devices" class="form-label">Target Devices</label>
                        <div class="form-check">
                            <input class="form-check-input select-all" type="checkbox" id="select_all_devices" checked>
                            <label class="form-check-label" for="select_all_devices">
                                <strong>Select All Devices</strong>
                            </label>
                        </div>
                        <div class="mt-2">
                        @php
                                $selectedDevices = old('devices', ['mobile', 'desktop', 'tablet']);
                        @endphp
                        <div class="form-check">
                            <input class="form-check-input device-checkbox" type="checkbox" name="devices[]" 
                                id="mobile" value="mobile" @if(in_array('mobile', $selectedDevices)) checked @endif>
                            <label class="form-check-label" for="mobile">Mobile</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input device-checkbox" type="checkbox" name="devices[]" 
                                id="desktop" value="desktop" @if(in_array('desktop', $selectedDevices)) checked @endif>
                            <label class="form-check-label" for="desktop">Desktop</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input device-checkbox" type="checkbox" name="devices[]" 
                                id="tablet" value="tablet" @if(in_array('tablet', $selectedDevices)) checked @endif>
                            <label class="form-check-label" for="tablet">Tablet</label>
                        </div>
                    </div>
                        @error('devices')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h4>Advanced Settings</h4>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="toggle_advanced">
                        <i class="fas fa-cog me-2"></i> Show Advanced Settings
                    </button>
                </div>
                
                <div id="advanced_settings" class="col-12 mt-3" style="display: none;">
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="unique_token" 
                                               name="unique_token" value="1" 
                                               @if(old('unique_token')) checked @endif>
                                        <label class="form-check-label" for="unique_token">
                                            Enable UniqueToken + TOK <span class="badge bg-warning">Anti-Plagiarism</span>
                                        </label>
                                        <div class="form-text">
                                            Adds extra security to protect your campaign against copying
                                        </div>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="cookie_tracking" 
                                               name="cookie_tracking" value="1" 
                                               @if(old('cookie_tracking', true)) checked @endif>
                                        <label class="form-check-label" for="cookie_tracking">
                                            Enable Cookie Tracking
                                        </label>
                                        <div class="form-text">
                                            Uses cookies to remember returning visitors
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="block_vpn" 
                                               name="block_vpn" value="1" 
                                               @if(old('block_vpn')) checked @endif>
                                        <label class="form-check-label" for="block_vpn">
                                            Block VPN/Proxy Traffic <span class="badge bg-info">Pro Feature</span>
                                        </label>
                                        <div class="form-text">
                                            Detects and blocks traffic coming from VPNs and proxies
                                        </div>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="cache_results" 
                                               name="cache_results" value="1" 
                                               @if(old('cache_results', true)) checked @endif>
                                        <label class="form-check-label" for="cache_results">
                                            Cache Filter Results
                                        </label>
                                        <div class="form-text">
                                            Caches filter results for better performance
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-12 mt-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" class="form-control" id="tags" name="tags" 
                                           placeholder="Enter tags separated by commas" value="{{ old('tags') }}">
                                    <div class="form-text">
                                        Tags help you organize and filter your campaigns
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-lg btn-primary">
                    <i class="fas fa-plus me-2"></i> Create Campaign
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // A/B Testing Toggle
    const abTestingCheckbox = document.getElementById('ab_testing');
    const abTestingSection = document.getElementById('ab_testing_section');
    
    abTestingCheckbox.addEventListener('change', function() {
        abTestingSection.style.display = this.checked ? 'block' : 'none';
    });
    
    // Add Variant URL
    const addUrlBtn = document.getElementById('add_url_btn');
    const abUrlsContainer = document.getElementById('ab_urls_container');
    
    addUrlBtn.addEventListener('click', function() {
        const urlRow = document.createElement('div');
        urlRow.className = 'row mb-3 ab-url-row';
        urlRow.innerHTML = `
            <div class="col-md-8">
                <input type="url" class="form-control" name="ab_urls[]" 
                       placeholder="https://example.com/variant-${document.querySelectorAll('.ab-url-row').length + 1}">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="ab_weights[]" 
                       placeholder="Weight (%)" min="1" max="100" value="50">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger remove-url-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        abUrlsContainer.appendChild(urlRow);
        
        // Add event listener to the new remove button
        urlRow.querySelector('.remove-url-btn').addEventListener('click', function() {
            urlRow.remove();
        });
    });
    
    // Initialize Remove URL buttons
    document.querySelectorAll('.remove-url-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.ab-url-row').remove();
        });
    });
    
    // Toggle Advanced Settings
    const toggleAdvancedBtn = document.getElementById('toggle_advanced');
    const advancedSettings = document.getElementById('advanced_settings');
    
    toggleAdvancedBtn.addEventListener('click', function() {
        const isHidden = advancedSettings.style.display === 'none';
        advancedSettings.style.display = isHidden ? 'block' : 'none';
        this.innerHTML = isHidden 
            ? '<i class="fas fa-cog me-2"></i> Hide Advanced Settings'
            : '<i class="fas fa-cog me-2"></i> Show Advanced Settings';
    });
    
    // Select All Devices
    const selectAllDevices = document.getElementById('select_all_devices');
    const deviceCheckboxes = document.querySelectorAll('.device-checkbox');
    
    selectAllDevices.addEventListener('change', function() {
        deviceCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    deviceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(deviceCheckboxes).every(cb => cb.checked);
            selectAllDevices.checked = allChecked;
        });
    });
</script>
@endsection