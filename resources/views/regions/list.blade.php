@extends('layouts.app')

@section('title', ucfirst($regionType) . 's')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">{{ ucfirst($regionType) }}s</h1>
        <a href="{{ route('municipalities.all') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Municipalities
        </a>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4" id="regionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $regionType === 'county' ? 'active' : '' }}" 
               href="{{ route('regions.counties') }}" 
               role="tab">
                <i class="bi bi-geo-alt me-2"></i>Counties
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $regionType === 'planning-region' ? 'active' : '' }}" 
               href="{{ route('regions.planning-regions') }}" 
               role="tab">
                <i class="bi bi-map me-2"></i>Planning Regions
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $regionType === 'classification' ? 'active' : '' }}" 
               href="{{ route('regions.classifications') }}" 
               role="tab">
                <i class="bi bi-building me-2"></i>Classifications
            </a>
        </li>
    </ul>

    <!-- Summary Information -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total {{ ucfirst($regionType) }}s</h5>
                    <h3 class="mb-0">{{ count($regions) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Municipalities</h5>
                    <h3 class="mb-0">{{ $regions->sum('total_municipalities') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">With Data</h5>
                    <h3 class="mb-0">{{ $regions->sum('municipalities_with_data') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Data Coverage</h5>
                    <h3 class="mb-0">
                        {{ $regions->sum('total_municipalities') > 0 ? round(($regions->sum('municipalities_with_data') / $regions->sum('total_municipalities')) * 100, 1) : 0 }}%
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparison Controls -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <small class="text-muted me-3" id="selection-info">Select 2 {{ $regionType }}s to compare</small>
            <form id="compare-form" action="{{ route('regions.compare') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="region_type" value="{{ $regionType }}">
                <button type="submit" id="compare-button" class="btn btn-success" disabled>
                    <i class="bi bi-arrow-left-right me-2"></i>Compare
                </button>
            </form>
        </div>
    </div>

    <!-- Regions List -->
    <div class="list-group shadow-sm">
        @forelse($regions as $region)
            <div class="list-group-item list-group-item-action py-3 region-row">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-{{ $regionType === 'county' ? 'geo-alt' : ($regionType === 'planning-region' ? 'map' : 'building') }} text-primary me-3"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $region->name }}</h6>
                                <small class="text-muted">
                                    {{ $region->municipalities_with_data }}/{{ $region->total_municipalities }} municipalities with data
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="fw-bold text-success">
                                ${{ number_format($region->total_refuse ?? 0, 0) }}
                            </div>
                            <small class="text-muted">Total Refuse</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="fw-bold text-info">
                                ${{ number_format($region->total_admin ?? 0, 0) }}
                            </div>
                            <small class="text-muted">Admin Costs</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex justify-content-end align-items-center">
                            <!-- Data Availability Badge -->
                            @php
                                $coverage = $region->total_municipalities > 0 ? ($region->municipalities_with_data / $region->total_municipalities) * 100 : 0;
                            @endphp
                            <span class="badge {{ $coverage >= 75 ? 'bg-success' : ($coverage >= 50 ? 'bg-warning' : 'bg-danger') }} me-3">
                                {{ round($coverage, 0) }}%
                            </span>
                            
                            <!-- Comparison Checkbox -->
                            <div class="form-check form-switch">
                                <input class="form-check-input region-checkbox" 
                                       type="checkbox" 
                                       value="{{ $region->name }}" 
                                       data-name="{{ $region->name }}" 
                                       id="check-{{ $loop->index }}">
                                <label class="form-check-label text-muted small ms-2" for="check-{{ $loop->index }}">
                                    Compare
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="list-group-item text-center py-5">
                <i class="bi bi-exclamation-circle text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">No {{ $regionType }}s found</h5>
                <p class="text-muted">There are no {{ $regionType }}s available in the system.</p>
            </div>
        @endforelse
    </div>

    @if(count($regions) > 0)
        <!-- Data Quality Warnings -->
        @php
            $regionsWithNoData = $regions->where('municipalities_with_data', 0)->count();
            $regionsWithLowCoverage = $regions->filter(function($region) {
                return $region->total_municipalities > 0 && ($region->municipalities_with_data / $region->total_municipalities) < 0.5;
            })->count();
        @endphp

        @if($regionsWithNoData > 0)
            <div class="alert alert-warning mt-4">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Data Quality Notice:</strong> {{ $regionsWithNoData }} {{ $regionType }}{{ $regionsWithNoData !== 1 ? 's' : '' }} 
                {{ $regionsWithNoData === 1 ? 'has' : 'have' }} no financial data available and cannot be used for meaningful comparisons.
            </div>
        @endif

        @if($regionsWithLowCoverage > 0)
            <div class="alert alert-info mt-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Coverage Notice:</strong> {{ $regionsWithLowCoverage }} {{ $regionType }}{{ $regionsWithLowCoverage !== 1 ? 's' : '' }} 
                {{ $regionsWithLowCoverage === 1 ? 'has' : 'have' }} data for less than 50% of their municipalities. 
                Comparisons may not be fully representative.
            </div>
        @endif

        <div class="mt-4 text-center">
            <small class="text-muted">
                Showing {{ count($regions) }} {{ $regionType }}{{ count($regions) !== 1 ? 's' : '' }} 
                with {{ $regions->sum('total_municipalities') }} total municipalities
                ({{ $regions->sum('municipalities_with_data') }} with financial data)
            </small>
        </div>
    @endif

    <script>
        const checkboxes = document.querySelectorAll('.region-checkbox');
        const compareButton = document.getElementById('compare-button');
        const compareForm = document.getElementById('compare-form');
        const regionType = '{{ $regionType }}';
        let selected = [];

        function rebuildHiddenInputs() {
            // Remove old dynamic inputs
            compareForm.querySelectorAll('input[name="regions[]"]').forEach(i => i.remove());
            // Add one hidden input per selected region
            selected.forEach(name => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'regions[]';
                inp.value = name;
                compareForm.appendChild(inp);
            });
        }

        function getRegionTypeDisplayName() {
            switch(regionType) {
                case 'county': return 'county';
                case 'planning-region': return 'planning region';
                case 'classification': return 'classification';
                default: return 'region';
            }
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const name = checkbox.dataset.name;
                const regionRow = checkbox.closest('.region-row');
                const hasData = regionRow.querySelector('.badge').textContent !== '0%';
                
                if (checkbox.checked) {
                    // Check if region has data
                    if (!hasData) {
                        checkbox.checked = false;
                        alert('This ' + getRegionTypeDisplayName() + ' has no financial data available and cannot be used for comparison.');
                        return;
                    }
                    
                    if (selected.length < 2) {
                        selected.push(name);
                    } else {
                        // If already 2 selected, uncheck this one
                        checkbox.checked = false;
                        return;
                    }
                } else {
                    selected = selected.filter(n => n !== name);
                }

                rebuildHiddenInputs();
                compareButton.disabled = selected.length !== 2;
                
                // Update button text and info
                const selectionInfo = document.getElementById('selection-info');
                const regionTypeDisplay = getRegionTypeDisplayName();
                
                if (selected.length === 0) {
                    compareButton.innerHTML = '<i class="bi bi-arrow-left-right me-2"></i>Compare';
                    selectionInfo.textContent = `Select 2 ${regionTypeDisplay}s to compare`;
                    selectionInfo.className = 'text-muted me-3';
                } else if (selected.length === 1) {
                    compareButton.innerHTML = `<i class="bi bi-arrow-left-right me-2"></i>Compare (${selected.length}/2)`;
                    selectionInfo.textContent = `${selected[0]} selected - choose 1 more ${regionTypeDisplay}`;
                    selectionInfo.className = 'text-info me-3';
                } else {
                    compareButton.innerHTML = '<i class="bi bi-arrow-left-right me-2"></i>Compare Selected';
                    selectionInfo.textContent = `Ready to compare: ${selected[0]} vs ${selected[1]}`;
                    selectionInfo.className = 'text-success me-3';
                }
            });
        });

        // Add form submission validation
        document.getElementById('compare-form').addEventListener('submit', function(e) {
            if (selected.length !== 2) {
                e.preventDefault();
                alert('Please select exactly 2 ' + getRegionTypeDisplayName() + 's for comparison.');
                return false;
            }

            // Double-check that selected regions have data
            let hasInvalidSelection = false;
            selected.forEach(regionName => {
                const checkbox = document.querySelector(`input[data-name="${regionName}"]`);
                if (checkbox) {
                    const regionRow = checkbox.closest('.region-row');
                    const hasData = regionRow.querySelector('.badge').textContent !== '0%';
                    if (!hasData) {
                        hasInvalidSelection = true;
                    }
                }
            });

            if (hasInvalidSelection) {
                e.preventDefault();
                alert('One or more selected ' + getRegionTypeDisplayName() + 's have no financial data available. Please select regions with available data.');
                return false;
            }
        });
    </script>

    <style>
        .region-row {
            border-left: 4px solid transparent;
            transition: all 0.2s ease;
        }
        
        .region-row:hover {
            border-left-color: #007bff;
            background-color: #f8f9fa;
        }
        
        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }
        
        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
        }
        
        .list-group {
            border-radius: 0.5rem;
        }
        
        .list-group-item:first-child {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        
        .list-group-item:last-child {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }
        
        #compare-button:disabled {
            opacity: 0.6;
        }
        
        #selection-info {
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            border-bottom: 3px solid transparent;
            background: none;
            padding: 0.75rem 1.5rem;
        }
        
        .nav-tabs .nav-link:hover {
            color: #007bff;
            border-bottom-color: #007bff;
            background: none;
        }
        
        .nav-tabs .nav-link.active {
            color: #007bff;
            border-bottom-color: #007bff;
            background: none;
            font-weight: 600;
        }
        
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }
    </style>

@endsection