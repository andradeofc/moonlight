@extends('layouts.app')

@section('title', 'Edit Campaign')

@section('content')
<div class="mb-4">
    <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Campaigns
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Campaign: {{ $campaign->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('campaigns.update', $campaign) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-4">
                <div class="col-12">
                    <h4>Campaign Information</h4>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Campaign Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $campaign->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="domain_id" class="form-label">Domain</label>
                        <select class="form-select @error('domain_id') is-invalid @enderror" 
                                id="domain_id" name="domain_id" required>
                            <option value="">Select a Domain</option>
                            @foreach($domains as $domain)
                                <option value="{{ $domain->id }}" @if(old('domain_id', $campaign->domain_id) == $domain->id) selected @endif>
                                    {{ $domain->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('domain_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="language" class="form-label">Language</label>
                        <select class="form-select @error('language') is-invalid @enderror" 
                                id="language" name="language" required>
                            <option value="">Select Language</option>
                            <option value="en" @if(old('language', $campaign->language) == 'en') selected @endif>English</option>
                            <option value="es" @if(old('language', $campaign->language) == 'es') selected @endif>Spanish</option>
                            <option value="pt" @if(old('language', $campaign->language) == 'pt') selected @endif>Portuguese</option>
                            <option value="fr" @if(old('language', $campaign->language) == 'fr') selected @endif>French</option>
                            <option value="de" @if(old('language', $campaign->language) == 'de') selected @endif>German</option>
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
                            <option value="facebook" @if(old('traffic_source', $campaign->traffic_source) == 'facebook') selected @endif>Facebook</option>
                            <option value="google" @if(old('traffic_source', $campaign->traffic_source) == 'google') selected @endif>Google Ads</option>
                            <option value="tiktok" @if(old('traffic_source', $campaign->traffic_source) == 'tiktok') selected @endif>TikTok</option>
                            <option value="native" @if(old('traffic_source', $campaign->traffic_source) == 'native') selected @endif>Native Ads</option>
                            <option value="other" @if(old('traffic_source', $campaign->traffic_source) == 'other') selected @endif>Other</option>
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
                               id="safe_url" name="safe_url" value="{{ old('safe_url', $campaign->safe_url) }}" required>
                        @error('safe_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                               id="offer_url" name="offer_url" value="{{ old('offer_url', $campaign->offer_url) }}" required>
                        @error('offer_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Offer Method</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="offer_method" 
                                   id="redirect" value="redirect" 
                                   @if(old('offer_method', $campaign->offer_method) == 'redirect') checked @endif>
                            <label class="form-check-label" for="redirect">
                                <strong>Redirect</strong> <span class="badge bg-success">Recommended</span>
                                <div class="text-muted">Direct redirect to your offer page</div>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="offer_method" 
                                   id="twr_mirror_offer" value="twr_mirror" 
                                   @if(old('offer_method', $campaign->offer_method) == 'twr_mirror') checked @endif>
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
                            <option value="US" @if(in_array('US', json_decode($campaign->countries))) selected @endif>United States</option>
                            <option value="CA" @if(in_array('CA', json_decode($campaign->countries))) selected @endif>Canada</option>
                            <option value="GB" @if(in_array('GB', json_decode($campaign->countries))) selected @endif>United Kingdom</option>
                            <option value="AU" @if(in_array('AU', json_decode($campaign->countries))) selected @endif>Australia</option>
                            <option value="BR" @if(in_array('BR', json_decode($campaign->countries))) selected @endif>Brazil</option>
                            <option value="FR" @if(in_array('FR', json_decode($campaign->countries))) selected @endif>France</option>
                            <option value="DE" @if(in_array('DE', json_decode($campaign->countries))) selected @endif>Germany</option>
                            <option value="IN" @if(in_array('IN', json_decode($campaign->countries))) selected @endif>India</option>
                            <option value="JP" @if(in_array('JP', json_decode($campaign->countries))) selected @endif>Japan</option>
                            <option value="MX" @if(in_array('MX', json_decode($campaign->countries))) selected @endif>Mexico</option>
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
                            <input class="form-check-input select-all" type="checkbox" id="select_all_devices">
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
                <div class="col-12">
                    <h4>Advanced Settings</h4>
                </div>
                
                <div class="col-md-12 mt-3">
                    <label for="tags" class="form-label">Tags</label>
                    <input type="text" class="form-control" id="tags" name="tags" 
                           placeholder="Enter tags separated by commas" 
                           value="{{ old('tags', $campaign->tags ? implode(', ', json_decode($campaign->tags)) : '') }}">
                    <div class="form-text">
                        Tags help you organize and filter your campaigns
                    </div>
                </div>
            </div>
            <div class="form-check form-switch mt-4">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                {{ old('is_active', $campaign->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
                Ativar Campanha
            </label>
        </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Update Campaign
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Select All Devices
    const selectAllDevices = document.getElementById('select_all_devices');
    const deviceCheckboxes = document.querySelectorAll('.device-checkbox');
    
    // Initialize Select All checkbox state
    function updateSelectAllState() {
        const allChecked = Array.from(deviceCheckboxes).every(cb => cb.checked);
        selectAllDevices.checked = allChecked;
    }
    
    // Initialize on page load
    updateSelectAllState();
    
    selectAllDevices.addEventListener('change', function() {
        deviceCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    deviceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAllState);
    });
</script>
@endsection