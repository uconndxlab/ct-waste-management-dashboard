@extends('layouts.app')

@php
    // display name mapping so plurals like "County" -> "Counties" are correct
    $regionDisplayNames = [
        'county' => ['singular' => 'County', 'plural' => 'Counties'],
        'planning-region' => ['singular' => 'Planning Region', 'plural' => 'Planning Regions'],
        'classification' => ['singular' => 'Classification', 'plural' => 'Classifications'],
    ];

    $displaySingular = $regionDisplayNames[$regionType]['singular'] ?? ucfirst($regionType);
    $displayPlural = $regionDisplayNames[$regionType]['plural'] ?? ($displaySingular . 's');

    // lowercase variants for inline sentences and JS
    $displaySingularLower = strtolower($displaySingular);
    $displayPluralLower = strtolower($displayPlural);

@endphp

@section('title',  $displayPlural)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">{{ $regionType === 'classification' ? 'Rural & Urban' : $displayPlural }}</h1>
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
                <i class="bi bi-building me-2"></i>Urban/Rural
            </a>
        </li>
    </ul>

    <!-- Summary Information -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total {{ $displayPlural }}</h5>
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
            <small class="text-muted me-3" id="selection-info">Select 2 {{ $displayPluralLower }} to compare</small>
            <form id="compare-form" action="{{ route('regions.compare') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="region_type" value="{{ $regionType }}">
                <button type="submit" id="compare-button" class="btn btn-success" disabled>
                    <i class="bi bi-arrow-left-right me-2"></i>Compare
                </button>
            </form>
        </div>
    </div>

    <!-- Comparison Results Container (initially hidden) -->
    <div id="comparison-results" class="card mb-4" style="display: none;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0" id="comparison-title">Regional Comparison</h5>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="back-to-list">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </button>
        </div>
        <div class="card-body" id="comparison-content">
            <!-- Comparison content will be loaded here -->
        </div>
    </div>

    <!-- Regions List -->
    <div class="list-group shadow-sm" id="regions-list">
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
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="fw-bold text-success">
                                ${{ number_format($region->total_total_sanitation_refuse ?? 0, 0) }}
                            </div>
                            <small class="text-muted">Total Refuse</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="fw-bold text-info">
                                ${{ number_format($region->total_admin_costs ?? 0, 0) }}
                            </div>
                            <small class="text-muted">Admin Costs</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="fw-bold text-primary">
                                @if($region->total_population && $region->total_population > 0)
                                    {{ number_format($region->total_population) }}
                                @else
                                    <span class="text-muted">No data</span>
                                @endif
                            </div>
                            <small class="text-muted">Population (2020)</small>
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
                <h5 class="text-muted mt-3">No {{ $displayPluralLower }} found</h5>
                <p class="text-muted">There are no {{ $displayPluralLower }} available in the system.</p>
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
                <strong>Data Quality Notice:</strong> {{ $regionsWithNoData }} 
                {{ $regionsWithNoData === 1 ? $displaySingularLower : $displayPluralLower }} 
                {{ $regionsWithNoData === 1 ? 'has' : 'have' }} no financial data available and cannot be used for meaningful comparisons.
            </div>
        @endif

        @if($regionsWithLowCoverage > 0)
            <div class="alert alert-info mt-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Coverage Notice:</strong> {{ $regionsWithLowCoverage }} 
                {{ $regionsWithLowCoverage === 1 ? $displaySingularLower : $displayPluralLower }} 
                {{ $regionsWithLowCoverage === 1 ? 'has' : 'have' }} data for less than 50% of their municipalities. 
                Comparisons may not be fully representative.
            </div>
        @endif

        <div class="mt-4 text-center">
            <small class="text-muted">
                Showing {{ count($regions) }} {{ count($regions) === 1 ? $displaySingularLower : $displayPluralLower }} 
                with {{ $regions->sum('total_municipalities') }} total municipalities
                ({{ $regions->sum('municipalities_with_data') }} with financial data)
            </small>
        </div>
    @endif

    <script>
        const checkboxes = document.querySelectorAll('.region-checkbox');
        const compareButton = document.getElementById('compare-button');
        const compareForm = document.getElementById('compare-form');
        const comparisonResults = document.getElementById('comparison-results');
        const comparisonContent = document.getElementById('comparison-content');
        const comparisonTitle = document.getElementById('comparison-title');
        const regionsList = document.getElementById('regions-list');
        const backToListButton = document.getElementById('back-to-list');
        const regionType = '{{ $regionType }}';
        const displaySingular = '{{ $displaySingularLower }}';
        const displayPlural = '{{ $displayPluralLower }}';
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

        function showComparison(data) {
            comparisonTitle.textContent = `${selected[0]} & ${selected[1]} Comparison`;
            comparisonContent.innerHTML = data;
            comparisonResults.style.display = 'block';
            regionsList.style.display = 'none';
            
            // Execute any scripts in the loaded content
            const scripts = comparisonContent.querySelectorAll('script');
            scripts.forEach((script, index) => {
                try {
                    eval(script.textContent);
                } catch (error) {
                    console.error(`Error executing region script ${index + 1}:`, error);
                }
            });
            
            // Scroll to comparison results
            comparisonResults.scrollIntoView({ behavior: 'smooth' });
        }

        // Charts are now handled in the loaded content

        function hideComparison() {
            comparisonResults.style.display = 'none';
            regionsList.style.display = 'block';
            
            // Clear selections
            selected = [];
            checkboxes.forEach(checkbox => checkbox.checked = false);
            compareButton.disabled = true;
            compareButton.innerHTML = '<i class="bi bi-arrow-left-right me-2"></i>Compare';
            
            const selectionInfo = document.getElementById('selection-info');
            selectionInfo.textContent = `Select 2 ${displayPlural} to compare`;
            selectionInfo.className = 'text-muted me-3';
        }

        backToListButton.addEventListener('click', hideComparison);

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const name = checkbox.dataset.name;
                const regionRow = checkbox.closest('.region-row');
                const hasData = regionRow.querySelector('.badge').textContent !== '0%';
                
                if (checkbox.checked) {
                    // Check if region has data
                    if (!hasData) {
                        checkbox.checked = false;
                        alert('This ' + displaySingular + ' has no financial data available and cannot be used for comparison.');
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
                const regionTypeDisplay = displaySingular;
                
                if (selected.length === 0) {
                    compareButton.innerHTML = '<i class="bi bi-arrow-left-right me-2"></i>Compare';
                    selectionInfo.textContent = `Select 2 ${displayPlural} to compare`;
                    selectionInfo.className = 'text-muted me-3';
                } else if (selected.length === 1) {
                    compareButton.innerHTML = `<i class="bi bi-arrow-left-right me-2"></i>Compare (${selected.length}/2)`;
                    selectionInfo.textContent = `${selected[0]} selected - choose 1 more ${displaySingular}`;
                    selectionInfo.className = 'text-info me-3';
                } else {
                    compareButton.innerHTML = '<i class="bi bi-arrow-left-right me-2"></i>Compare Selected';
                    selectionInfo.textContent = `Ready to compare: ${selected[0]} vs ${selected[1]}`;
                    selectionInfo.className = 'text-success me-3';
                }
            });
        });

        // Add form submission handler
        compareForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (selected.length !== 2) {
                alert('Please select exactly 2 ' + displayPlural + ' for comparison.');
                return;
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
                alert('One or more selected ' + displayPlural + ' have no financial data available. Please select regions with available data.');
                return;
            }

            compareButton.disabled = true;
            compareButton.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Comparing...';

            try {
                const formData = new FormData(compareForm);
                
                const response = await fetch('{{ route("regions.compare") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    }
                });

                if (response.ok) {
                    const html = await response.text();
                    showComparison(html);
                } else {
                    const errorText = await response.text();
                    console.error('Response error:', response.status, errorText);
                    alert(`Error loading comparison (${response.status}). Please try again.`);
                }
            } catch (error) {
                console.error('Network error:', error);
                alert('Network error loading comparison. Please check your connection and try again.');
            }

            compareButton.disabled = false;
            compareButton.innerHTML = '<i class="bi bi-arrow-left-right me-2"></i>Compare Selected';
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