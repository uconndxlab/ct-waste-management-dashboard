@extends('layouts.app')

@section('title', 'Municipalities')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">All Municipalities</h1>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bar-chart-line me-2"></i>Regional Analysis
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('regions.list', ['type' => 'county']) }}">
                    <i class="bi bi-geo-alt me-2"></i>Counties
                </a></li>
                <li><a class="dropdown-item" href="{{ route('regions.list', ['type' => 'planning-region']) }}">
                    <i class="bi bi-map me-2"></i>Planning Regions
                </a></li>
                <li><a class="dropdown-item" href="{{ route('regions.list', ['type' => 'classification']) }}">
                    <i class="bi bi-building me-2"></i>Classifications
                </a></li>
            </ul>
        </div>
    </div>

    <form action="{{ route('municipalities.all') }}" method="GET" class="mb-4 mt-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search Municipality" value="{{ request('search') }}">
 
            @php
                $filters = [
                    'region_type' => $regionType,
                    'geographical_region' => $geographicalRegion,
                    'county' => $county,
                    'letter' => $selectedLetter,
                ];
            @endphp

            @foreach($filters as $key => $value)
                @if($value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
        </div>
    </form>

    <div class="mb-3">
        <strong>Search by Letter: </strong>
        <a href="{{ route('municipalities.all', array_merge(request()->except('letter'), ['letter' => null])) }}" 
        class="btn btn-sm {{ !$selectedLetter ? 'btn-primary' : 'btn-outline-primary' }}">
            All
        </a>
        @foreach($letters as $letter)
            <a href="{{ route('municipalities.all', array_merge(request()->except('letter'), ['letter' => $letter])) }}" 
            class="btn btn-sm {{ $selectedLetter == $letter ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $letter }}
            </a>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapseContent" aria-expanded="false" aria-controls="filterCollapseContent">
            <i class="bi bi-funnel me-2"></i>Filter Options
        </button>

        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('regions.list', ['type' => 'planning-region']) }}" class="btn btn-outline-primary">
                <i class="bi bi-map me-2"></i>Regional Analysis
            </a>
            <small class="text-muted me-3" id="selection-info">Select 2 municipalities to compare</small>
            <form id="compare-form" action="{{ route('municipalities.compare') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" id="compare-button" class="btn btn-success" disabled>
                    <i class="bi bi-arrow-left-right me-2"></i>Compare
                </button>
            </form>
        </div>
    </div>

    <div class="collapse mb-3" id="filterCollapseContent">
        <div class="card card-body">
            <div class="d-flex flex-row">
                <div class="d-flex align-items-center gap-4 w-100">
                    <strong>Filter by Index: </strong>
                    <div class="dropdown">
                        <a class="btn btn-light dropdown-toggle border" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $regionType ? $regionType : 'Region Type' }}
                        </a>
            
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('municipalities.all', array_merge(request()->except('region_type'), ['region_type' => null])) }}">All</a></li>
                            @foreach ($regionTypes as $type)
                                <li><a class="dropdown-item" href="{{ route( 'municipalities.all', array_merge(request()->except('region_type'), ['region_type' => $type])) }}"> {{ $type }}</a></li>  
                            @endforeach
                        </ul>
                    </div>
                    <div class="dropdown">
                        <a class="btn btn-light dropdown-toggle border" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $geographicalRegion ? $geographicalRegion : 'Geographical Region' }}
                        </a>
            
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('municipalities.all', array_merge(request()->except('geographical_region'), ['geographical_region' => null])) }}"> All </a></li>
                            @foreach ($geographicalRegions as $region)
                                <li><a class="dropdown-item" href="{{ route('municipalities.all', array_merge(request()->except('geographical_region'), ['geographical_region' => $region])) }}"> {{ $region }} </a></li>
                            @endforeach

                        </ul>
                    </div>
                    <div class="dropdown">
                        <a class="btn btn-light dropdown-toggle border" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                             {{ $county ? $county : 'County'  }}
                        </a>
            
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('municipalities.all', array_merge(request()->except('county'), ['county' => null])) }}"> All </a></li>
                            @foreach ($counties as $c)
                                 <li><a class="dropdown-item" href="{{ route('municipalities.all', array_merge(request()->except('county'), ['county' => $c])) }}"> {{ $c }} </a></li>
                            @endforeach
                        </ul>
                    </div>

                    @if ($regionType || $geographicalRegion || $county) 
                        <a href="{{ route('municipalities.all', array_intersect_key(request()->all(), array_flip(['letter', 'search']))) }}" class="btn btn-outline-danger btn-sm">Clear Filters</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <form id="compare-form" action="{{ route('municipalities.compare') }}" method="POST">
        @csrf

        <!-- Comparison Results Container (initially hidden) -->
        <div id="comparison-results" class="card mb-4" style="display: none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0" id="comparison-title">Municipality Comparison</h5>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="back-to-list">
                    <i class="bi bi-arrow-left me-2"></i>Back to List
                </button>
            </div>
            <div class="card-body" id="comparison-content">
                <!-- Comparison content will be loaded here -->
            </div>
        </div>

        <div class="list-group shadow-sm" id="municipalities-list">
            @foreach($municipalities as $municipality)
                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 municipality-row">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-geo-alt text-primary me-3"></i>
                        <a href="{{ route('municipalities.view', ['name' => $municipality->name]) }}" class="text-decoration-none text-dark fw-medium municipality-link">
                            {{ $municipality->name }}
                        </a>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input municipality-checkbox" type="checkbox" value="{{ $municipality->name }}" data-name="{{ $municipality->name }}" id="check-{{ $loop->index }}">
                        <label class="form-check-label text-muted small ms-2" for="check-{{ $loop->index }}">
                            Compare
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </form>

    <script>
        const checkboxes = document.querySelectorAll('.municipality-checkbox');
        const compareButton = document.getElementById('compare-button');
        const compareForm = document.getElementById('compare-form');
        const comparisonResults = document.getElementById('comparison-results');
        const comparisonContent = document.getElementById('comparison-content');
        const comparisonTitle = document.getElementById('comparison-title');
        const municipalitiesList = document.getElementById('municipalities-list');
        const backToListButton = document.getElementById('back-to-list');
        let selected = [];

        function rebuildHiddenInputs() {
            // remove old dynamic inputs
            compareForm.querySelectorAll('input[name="municipalities[]"]').forEach(i => i.remove());
            // add one hidden input per selected municipality
            selected.forEach(name => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'municipalities[]';
                inp.value = name;
                compareForm.appendChild(inp);
            });
        }

        function showComparison(data) {
            comparisonTitle.textContent = `${selected[0]} & ${selected[1]} Comparison`;
            comparisonContent.innerHTML = data;
            comparisonResults.style.display = 'block';
            municipalitiesList.style.display = 'none';
            
            // Execute any scripts in the loaded content
            const scripts = comparisonContent.querySelectorAll('script');
            scripts.forEach((script, index) => {
                try {
                    eval(script.textContent);
                } catch (error) {
                    console.error(`Error executing script ${index + 1}:`, error);
                }
            });
            
            // Scroll to comparison results
            comparisonResults.scrollIntoView({ behavior: 'smooth' });
        }

        // Charts are now handled in the loaded content

        function hideComparison() {
            comparisonResults.style.display = 'none';
            municipalitiesList.style.display = 'block';
            
            // Clear selections
            selected = [];
            checkboxes.forEach(checkbox => checkbox.checked = false);
            compareButton.disabled = true;
            compareButton.innerHTML = '<i class="bi bi-arrow-left-right me-2"></i>Compare';
            
            const selectionInfo = document.getElementById('selection-info');
            selectionInfo.textContent = 'Select 2 municipalities to compare';
            selectionInfo.className = 'text-muted me-3';
        }

        backToListButton.addEventListener('click', hideComparison);

        compareForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (selected.length !== 2) return;

            compareButton.disabled = true;
            compareButton.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Comparing...';

            try {
                const formData = new FormData(compareForm);
                
                const response = await fetch('{{ route("municipalities.compare") }}', {
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

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const name = checkbox.dataset.name;
                if (checkbox.checked) {
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
                if (selected.length === 0) {
                    compareButton.innerHTML = '<i class="bi bi-arrow-left-right me-2"></i>Compare';
                    selectionInfo.textContent = 'Select 2 municipalities to compare';
                    selectionInfo.className = 'text-muted me-3';
                } else if (selected.length === 1) {
                    compareButton.innerHTML = `<i class="bi bi-arrow-left-right me-2"></i>Compare (${selected.length}/2)`;
                    selectionInfo.textContent = `${selected[0]} selected - choose 1 more`;
                    selectionInfo.className = 'text-info me-3';
                } else {
                    compareButton.innerHTML = '<i class="bi bi-arrow-left-right me-2"></i>Compare Selected';
                    selectionInfo.textContent = `Ready to compare: ${selected[0]} vs ${selected[1]}`;
                    selectionInfo.className = 'text-success me-3';
                }
            });
        });
    </script>

    <style>
        .municipality-row {
            border-left: 4px solid transparent;
            transition: all 0.2s ease;
        }
        
        .municipality-row:hover {
            border-left-color: #007bff;
            background-color: #f8f9fa;
        }
        
        .municipality-link:hover {
            color: #007bff !important;
            text-decoration: none;
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
    </style>

    @if(request('search'))
        @if($municipalities->isEmpty())
        <p class="text-muted">No Results Found</p>
        @endif
    <br/>
    <a href="{{ route('municipalities.all') }}" class="btn btn-secondary mb-3"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
      </svg> View All Municipalities</a>
    @endif

@endsection
