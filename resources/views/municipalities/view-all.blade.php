@extends('layouts.app')

@section('title', 'Municipalities')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/htmx.org@1.9.10/dist/htmx.min.css">
@endpush

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

    <form id="search-form" class="mb-4 mt-3" hx-get="{{ route('htmx.municipalities.grid') }}" hx-target="#municipalities-grid" hx-trigger="keyup changed delay:300ms from:input[name='search'], change from:select">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search Municipality" value="{{ request('search') }}">
            <button type="button" class="btn btn-primary" onclick="htmx.trigger('#search-form', 'change')">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
        
        <!-- Hidden inputs for filters -->
        <input type="hidden" name="region_type" id="filter-region-type" value="{{ $regionType }}">
        <input type="hidden" name="geographical_region" id="filter-geographical-region" value="{{ $geographicalRegion }}">
        <input type="hidden" name="county" id="filter-county" value="{{ $county }}">
        <input type="hidden" name="letter" id="filter-letter" value="{{ $selectedLetter }}">
    </form>

    <div class="mb-3">
        <strong>Search by Letter: </strong>
        <button type="button" onclick="filterByLetter(null)" class="btn btn-sm {{ !$selectedLetter ? 'btn-primary' : 'btn-outline-primary' }}">
            All
        </button>
        @foreach($letters as $letter)
            <button type="button" onclick="filterByLetter('{{ $letter }}')" class="btn btn-sm {{ $selectedLetter == $letter ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $letter }}
            </button>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapseContent" aria-expanded="false" aria-controls="filterCollapseContent">
                <i class="bi bi-funnel me-2"></i>Filter Options
            </button>
            <a href="{{ route('regions.list', ['type' => 'planning-region']) }}" class="btn btn-outline-primary">
                <i class="bi bi-map me-2"></i>Regional Analysis
            </a>
        </div>

        <div class="d-flex align-items-center">
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
                        <button class="btn btn-light dropdown-toggle border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $regionType ? $regionType : 'Region Type' }}
                        </button>
            
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item" type="button" onclick="filterByRegionType(null)">All</button></li>
                            @foreach ($regionTypes as $type)
                                <li><button class="dropdown-item" type="button" onclick="filterByRegionType('{{ $type }}')">{{ $type }}</button></li>  
                            @endforeach
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $geographicalRegion ? $geographicalRegion : 'Geographical Region' }}
                        </button>
            
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item" type="button" onclick="filterByGeographicalRegion(null)">All</button></li>
                            @foreach ($geographicalRegions as $region)
                                <li><button class="dropdown-item" type="button" onclick="filterByGeographicalRegion('{{ $region }}')">{{ $region }}</button></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                             {{ $county ? $county : 'County'  }}
                        </button>
            
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item" type="button" onclick="filterByCounty(null)">All</button></li>
                            @foreach ($counties as $c)
                                 <li><button class="dropdown-item" type="button" onclick="filterByCounty('{{ $c }}')">{{ $c }}</button></li>
                            @endforeach
                        </ul>
                    </div>

                    @if ($regionType || $geographicalRegion || $county) 
                        <button type="button" onclick="clearAllFilters()" class="btn btn-outline-danger btn-sm">Clear Filters</button>
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

        <!-- Municipality Grid -->
        <div class="row" id="municipalities-grid" hx-get="{{ route('htmx.municipalities.grid') }}" hx-trigger="load">
            @include('municipalities.partials.municipality-grid', ['municipalities' => $municipalities])
        </div>
    </form>

    <!-- Municipality Modal -->
    <div id="municipality-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="municipalityModalLabel">Municipality Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Modal content will be loaded here via HTMX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    </form>



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

@push('scripts')
    <script src="https://unpkg.com/htmx.org@1.9.10/dist/htmx.min.js"></script>
    <script>
        let selected = [];
        
        // Filter functions
        function filterByLetter(letter) {
            document.getElementById('filter-letter').value = letter || '';
            htmx.trigger('#search-form', 'change');
            updateLetterButtons(letter);
        }
        
        function filterByRegionType(type) {
            document.getElementById('filter-region-type').value = type || '';
            htmx.trigger('#search-form', 'change');
        }
        
        function filterByGeographicalRegion(region) {
            document.getElementById('filter-geographical-region').value = region || '';
            htmx.trigger('#search-form', 'change');
        }
        
        function filterByCounty(county) {
            document.getElementById('filter-county').value = county || '';
            htmx.trigger('#search-form', 'change');
        }
        
        function clearAllFilters() {
            document.getElementById('filter-region-type').value = '';
            document.getElementById('filter-geographical-region').value = '';
            document.getElementById('filter-county').value = '';
            htmx.trigger('#search-form', 'change');
        }
        
        function updateLetterButtons(selectedLetter) {
            document.querySelectorAll('.mb-3 button').forEach(btn => {
                btn.className = btn.className.replace('btn-primary', 'btn-outline-primary');
                if ((selectedLetter === null && btn.textContent.trim() === 'All') || 
                    (selectedLetter && btn.textContent.trim() === selectedLetter)) {
                    btn.className = btn.className.replace('btn-outline-primary', 'btn-primary');
                }
            });
        }
        
        // Comparison functionality
        function rebuildHiddenInputs() {
            const compareForm = document.getElementById('compare-form');
            compareForm.querySelectorAll('input[name="municipalities[]"]').forEach(i => i.remove());
            selected.forEach(name => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'municipalities[]';
                inp.value = name;
                compareForm.appendChild(inp);
            });
        }
        
        function updateCompareUI() {
            const compareButton = document.getElementById('compare-button');
            const selectionInfo = document.getElementById('selection-info');
            
            if (!compareButton || !selectionInfo) return;
            
            compareButton.disabled = selected.length !== 2;
            
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
        }
        
        function showComparison(data) {
            const comparisonResults = document.getElementById('comparison-results');
            const comparisonContent = document.getElementById('comparison-content');
            const comparisonTitle = document.getElementById('comparison-title');
            const municipalitiesGrid = document.getElementById('municipalities-grid').parentElement;
            
            comparisonTitle.textContent = `${selected[0]} & ${selected[1]} Comparison`;
            comparisonContent.innerHTML = data;
            comparisonResults.style.display = 'block';
            municipalitiesGrid.style.display = 'none';
            
            // Execute any scripts in the loaded content
            const scripts = comparisonContent.querySelectorAll('script');
            scripts.forEach((script, index) => {
                try {
                    eval(script.textContent);
                } catch (error) {
                    console.error(`Error executing script ${index + 1}:`, error);
                }
            });
            
            comparisonResults.scrollIntoView({ behavior: 'smooth' });
        }
        
        function hideComparison() {
            const comparisonResults = document.getElementById('comparison-results');
            const municipalitiesGrid = document.getElementById('municipalities-grid').parentElement;
            
            comparisonResults.style.display = 'none';
            municipalitiesGrid.style.display = 'block';
            
            // Clear selections
            selected = [];
            document.querySelectorAll('.municipality-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateCompareUI();
        }
        
        // Event delegation for dynamically loaded checkboxes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('municipality-checkbox')) {
                const name = e.target.dataset.name;
                if (e.target.checked) {
                    if (selected.length < 2) {
                        selected.push(name);
                    } else {
                        e.target.checked = false;
                        return;
                    }
                } else {
                    selected = selected.filter(n => n !== name);
                }
                
                rebuildHiddenInputs();
                updateCompareUI();
            }
        });
        
        // Compare form submission
        document.addEventListener('submit', async function(e) {
            if (e.target.id === 'compare-form') {
                e.preventDefault();
                
                if (selected.length !== 2) return;
                
                const compareButton = document.getElementById('compare-button');
                compareButton.disabled = true;
                compareButton.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Comparing...';
                
                try {
                    const formData = new FormData(e.target);
                    
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
                        alert(`Error loading comparison (${response.status}). Please try again.`);
                    }
                } catch (error) {
                    console.error('Network error:', error);
                    alert('Network error loading comparison. Please check your connection and try again.');
                }
                
                compareButton.disabled = false;
                compareButton.innerHTML = '<i class="bi bi-arrow-left-right me-2"></i>Compare Selected';
            }
        });
        
        // Back to list button
        document.addEventListener('click', function(e) {
            if (e.target.id === 'back-to-list') {
                hideComparison();
            }
        });
        
        // Initialize Bootstrap modal for municipality details
        document.addEventListener('click', function(e) {
            if (e.target.closest('[hx-target="#municipality-modal .modal-body"]')) {
                setTimeout(() => {
                    const modal = new bootstrap.Modal(document.getElementById('municipality-modal'));
                    modal.show();
                }, 100);
            }
        });
    </script>
@endpush
