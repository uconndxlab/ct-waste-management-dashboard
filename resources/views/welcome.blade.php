@extends('layouts.app')

@section('title', 'Dashboard Home')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="preload" as="image" href="https://a.tile.openstreetmap.org/8/76/95.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-map {
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden; 
        }
        #ctMap { 
            height: 500px; 
        }
        .municipality-popup {
            min-width: 200px;
        }
        .municipality-popup h5 {
            margin-top: 0;
            color: #3490dc; 
        }
        .leaflet-tooltip {
            background-color: rgba(255, 255, 255, 0.95);
            border: 1px solid #3490dc;
            border-radius: 4px;
            padding: 8px 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.4);
            min-width: 200px;
            font-size: 13px;
            line-height: 1.5;
        }

        .leaflet-tooltip strong {
            display: block;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
            margin-bottom: 3px;
            color: #3490dc;
        }
    </style>
@endpush

@section('content')
    <body class="">

        {{-- <a href="/municipalities" class="mb-4">View All Municipalities</a> --}}
        <div class="d-flex align-items-center justify-content-between w-100 mb-4">
            <form action="{{ route('municipalities.all') }}" method="GET" class="flex-grow-1 me-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search municipalities..." value="{{ request('search') }}">
                </div>
            </form>
            <a href="{{ route('municipalities.all') }}" class="btn btn-primary">
                <i class="bi bi-search"></i> View all municipalities
            </a>
        </div>

        

        <div class="container mt-4">
            <div class="row align-items-stretch">
                <!-- Map Section -->
                <section class="col-lg-8 mb-4">
                    <div class="dashboard-map rounded-lg shadow-sm w-100">
                        <div id="ctMap" style="height: 500px; width: 100%;"></div>
                    </div>
                </section>

                <section class="col-lg-3 d-flex justify-content-center flex-column">
                    <!-- Contact Section -->
                    <div class="mb-4 w-100">
                        <h5 class="fw-bold">Contact Information</h5>
                        <div class="p-3 rounded-lg shadow-sm border bg-light">
                            <div class="mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-telephone blue-fill"></i>
                                <p class="mb-0">(123) 456-7890</p>
                            </div>
                            <div class=" mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-envelope blue-fill"></i>
                                <p class="mb-0">ctwaste@uconn.edu</p>
                            </div>
                            <div class="mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt blue-fill"></i>
                                <p class="mb-0">Storrs, Connecticut, US</p>
                            </div>
                        </div>
                    </div>
    
                    <!-- External Resources Section -->
                    <div class="w-100">
                        <h5 class="fw-bold">External Resources & Other Information</h5>
                        <div class="external-content p-3 rounded-lg shadow-sm bg-light">
                            <p class="text-decoration-underline"><a href="#">CT Department of Transportation →</a></p>
                            <p class="text-decoration-underline"><a href="#">CT Data Information →</a></p>
                            <p class="text-decoration-underline"><a href="#">Another Random Link →</a></p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <!-- Regional Analysis Navigation -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-bar-chart-line text-primary me-2"></i>
                            Year-Over-Year Comparison: CT Regional and Municipal Analysis
                        </h5>
                        <p class="card-text text-muted mb-3">
                            Compare waste management data across different regional groupings and municipalities.
                        </p>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <a href="{{ route('regions.list', ['type' => 'county']) }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    Counties
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('regions.list', ['type' => 'planning-region']) }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-map me-2"></i>
                                    Planning Regions
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('regions.list', ['type' => 'classification']) }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-tree me-2"></i>
                                    Urban/Rural
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('municipalities.all') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-building me-2"></i>
                                    Municipalities
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Debug

        @if(isset($countyTotals) && count($countyTotals) > 0)
            <div class="alert alert-info">
                County data available: {{ count($countyTotals) }} counties
            </div>
        @else
            <div class="alert alert-warning">
                No county data available
            </div>
        @endif



        <div class="alert alert-info">
            <p>Test data (first 10 records):</p>
            <ul>
                @foreach($test as $t)
                    <li>{{ $t->name }} - {{ $t->county ?? 'No county' }} - {{ $t->total_sanitation_refuse ?? 'No refuse data' }}</li>
                @endforeach
            </ul>
        </div>

        <div class="alert alert-info">
            <p>County Totals Debug:</p>
            <ul>
                @foreach($countyTotals as $county => $data)
                    <li>{{ $county }}: ${{ number_format($data->total_refuse) }} 
                        ({{ $data->municipalities_with_data }}/{{ $data->total_municipalities }} municipalities with data)</li>
                @endforeach
            </ul>
        </div>

        <div class="alert alert-info">
            <p>GeoJSON vs Database Municipality Names:</p>
            <ul id="nameComparisonList">
            </ul>
        </div>

    -->

    </body>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
    <script>
        const municipalitiesData = @json($municipalities);
        const townClassifications = @json($townClassifications ?? []);
        const countyTotals = @json($countyTotals ?? []);
        const regionTotals = @json($regionTotals ?? []);
        const typeTotals = @json($typeTotals ?? []);
        
        const colorPalettes = {
            county: {
                "Fairfield": "#1f77b4",
                "Hartford": "#ff7f0e",
                "Litchfield": "#2ca02c",
                "Middlesex": "#d62728",
                "New Haven": "#9467bd",
                "New London": "#8c564b",
                "Tolland": "#e377c2",
                "Windham": "#7f7f7f"
            },
            region: {
                "Northwest Hills": "#2ca02c",
                "Capitol": "#ff7f0e",
                "Western Connecticut": "#1f77b4",
                "South Central Connecticut": "#9467bd",
                "Lower Connecticut River Valley": "#d62728",
                "Southeastern Connecticut": "#8c564b",
                "Northeastern Connecticut": "#7f7f7f",
                "Greater Bridgeport": "#FFB302",
                "Naugatuck Valley": "#e377c2",
            },
            type: {
                "Rural": "#2ca02c",
                "Urban": "#1f77b4", 
            }
        };
        
        let geoJSONLayer;
        let currentView = 'default';
        let legendControl;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the map centered on Connecticut
            const map = L.map('ctMap').setView([41.390, -72.700], 8); 

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Create view selector control
            L.Control.ViewSelector = L.Control.extend({
                onAdd: function(map) {
                    // Create control container
                    const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control view-selector-control');
                    container.style.backgroundColor = 'white';
                    container.style.padding = '5px';
                    container.style.cursor = 'auto';
                    
                    const select = L.DomUtil.create('select', 'form-select form-select-sm', container);
                    select.id = 'viewSelector';
                    select.style.minWidth = '200px';
                    
                    // Add options
                    const options = [
                        { value: 'default', text: 'Default View' },
                        { value: 'county', text: 'View by County' },
                        { value: 'region', text: 'View by Planning Region' },
                        { value: 'type', text: 'View by Rural/Urban Classification' }
                    ];
                    
                    options.forEach(opt => {
                        const option = L.DomUtil.create('option', '', select);
                        option.value = opt.value;
                        option.innerText = opt.text;
                    });
                    
                    L.DomEvent.disableClickPropagation(container);
                    L.DomEvent.disableScrollPropagation(container);
                    
                    L.DomEvent.on(select, 'change', function(e) {
                        currentView = e.target.value;
                        updateMap();
                    });
                    
                    return container;
                }
            });
            
            // Create legend control
            L.Control.Legend = L.Control.extend({
                onAdd: function(map) {
                    const container = L.DomUtil.create('div', 'leaflet-control legend-control');
                    container.id = 'mapLegend';
                    container.style.display = 'none';
                    container.style.backgroundColor = 'white';
                    container.style.padding = '8px';
                    container.style.maxWidth = '200px';
                    container.style.borderRadius = '4px';
                    container.style.boxShadow = '0 1px 5px rgba(0,0,0,0.4)';
                    
                    const title = L.DomUtil.create('h6', '', container);
                    title.innerText = 'Legend';
                    title.style.margin = '0 0 5px 0';
                    title.style.fontWeight = 'bold';
                    
                    const items = L.DomUtil.create('div', '', container);
                    items.id = 'legendItems';
                    
                    L.DomEvent.disableClickPropagation(container);
                    
                    return container;
                }
            });
            
            new L.Control.ViewSelector({ position: 'topright' }).addTo(map);
            legendControl = new L.Control.Legend({ position: 'bottomleft' }).addTo(map);
            
            function updateMap() {
                if (geoJSONLayer) {
                    map.removeLayer(geoJSONLayer);
                }

                const loadingDiv = document.createElement('div');
                loadingDiv.id = 'mapLoading';
                loadingDiv.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
                loadingDiv.style.position = 'absolute';
                loadingDiv.style.top = '50%';
                loadingDiv.style.left = '50%';
                loadingDiv.style.transform = 'translate(-50%, -50%)';
                loadingDiv.style.zIndex = '1000';
                document.getElementById('ctMap').appendChild(loadingDiv);
                
                fetch("{{ asset('maps/ct-towns.geojson') }}")
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        geoJSONLayer = L.geoJSON(data, {
                            style: styleFeature,
                            onEachFeature: bindFeatureEvents
                        }).addTo(map);
                        
                        updateLegend();
                    })
                    .catch(error => {
                        console.error('Error loading or parsing GeoJSON:', error);
                        document.getElementById('ctMap').innerHTML = '<div class="alert alert-danger">Could not load map data. Please check the console for errors.</div>';
                    });
                document.getElementById('mapLoading')?.remove();
            }
            
            function styleFeature(feature) {
                const municipalityName = feature.properties.TOWN_NAME || "Unknown";
                const townData = townClassifications[municipalityName];
                
                let fillColor = '#3490dc'; 
                
                if (currentView !== 'default' && townData) {
                    let category;
                    
                    switch(currentView) {
                        case 'county':
                            category = townData.county;
                            break;
                        case 'region':
                            category = townData.geographical_region;
                            break;
                        case 'type':
                            category = townData.region_type;
                            break;
                    }
                    
                    if (category && colorPalettes[currentView][category]) {
                        fillColor = colorPalettes[currentView][category];
                    }
                }
                
                return {
                    fillColor: fillColor,
                    weight: 1,
                    opacity: 1,
                    color: 'white',
                    fillOpacity: 0.7
                };
            }
            
            // Bind events to features
            function bindFeatureEvents(feature, layer) {
                const municipalityName = feature.properties.TOWN_NAME || "Unknown Municipality";
                const municipality = municipalitiesData.find(m => m.name === municipalityName);
                const townData = townClassifications[municipalityName];
                
                let tooltipContent = `<strong>${municipalityName}</strong>`;
                
                // Add latest year if available
                if (municipality && municipality.latest_year) {
                    tooltipContent += `${municipality.latest_year}`;
                }

                
                
                if (currentView !== 'default' && townData) {
                    let categoryValue, totalRefuse = 0, totalAdmin = 0;
                    let categoryLabel = '';
                    
                    switch(currentView) {
                        case 'county':
                            categoryValue = townData.county;
                            categoryLabel = 'County';
                            if (countyTotals[categoryValue]) {
                                totalRefuse = countyTotals[categoryValue].total_refuse;
                                totalAdmin = countyTotals[categoryValue].total_admin;
                            }
                            break;
                        case 'region':
                            categoryValue = townData.geographical_region;
                            categoryLabel = 'Planning Region';
                            if (regionTotals[categoryValue]) {
                                totalRefuse = regionTotals[categoryValue].total_refuse;
                                totalAdmin = regionTotals[categoryValue].total_admin;
                            }
                            break;
                        case 'type':
                            categoryValue = townData.region_type;
                            categoryLabel = 'Classification';
                            if (typeTotals[categoryValue]) {
                                totalRefuse = typeTotals[categoryValue].total_refuse;
                                totalAdmin = typeTotals[categoryValue].total_admin;
                            }
                            break;
                    }
                    
                    tooltipContent += `<br>${categoryLabel}: ${categoryValue}`;
                    
                    // Format and add the refuse total
                    totalRefuse = parseFloat(totalRefuse);
                    if (!isNaN(totalRefuse) && totalRefuse > 0) {
                        const formattedTotal = new Intl.NumberFormat('en-US', { 
                            style: 'currency', 
                            currency: 'USD',
                            maximumFractionDigits: 0
                        }).format(totalRefuse);
                        tooltipContent += `<br>Regional Total Sanitation Refuse: ${formattedTotal}`;
                    } else {
                        tooltipContent += `<br>Regional Total Sanitation Refuse: $0`;
                    }
                    
                    // Format and add the admin costs total
                    totalAdmin = parseFloat(totalAdmin);
                    if (!isNaN(totalAdmin) && totalAdmin > 0) {
                        const formattedAdminTotal = new Intl.NumberFormat('en-US', { 
                            style: 'currency', 
                            currency: 'USD',
                            maximumFractionDigits: 0
                        }).format(totalAdmin);
                        tooltipContent += `<br>Regional Total Admin Costs: ${formattedAdminTotal}`;
                    } else {
                        tooltipContent += `<br>Regional Total Admin Costs: $0`;
                    }
                }
                
                if (municipality && municipality.total_sanitation_refuse) {
                    const refuseString = municipality.total_sanitation_refuse.toString();
                    const cleanValue = refuseString.replace(/[\$,]/g, '');
                    const refuseValue = parseFloat(cleanValue);
                    
                    if (!isNaN(refuseValue) && refuseValue > 0) {
                        const municipalityRefuse = new Intl.NumberFormat('en-US', { 
                            style: 'currency', 
                            currency: 'USD',
                            maximumFractionDigits: 0
                        }).format(refuseValue);
                        
                        tooltipContent += `<br>Municipality Refuse: ${municipalityRefuse}`;
                    } else {
                        tooltipContent += `<br>Municipality Refuse: No data available`;
                    }
                } else {
                    tooltipContent += `<br>Municipality Refuse: No data available`;
                }
                
                // Then also add municipality admin costs to individual tooltips
                if (municipality && municipality.admin_costs) {
                    const adminString = municipality.admin_costs.toString();
                    const cleanAdminValue = adminString.replace(/[\$,]/g, '');
                    const adminValue = parseFloat(cleanAdminValue);
                    
                    if (!isNaN(adminValue) && adminValue > 0) {
                        const municipalityAdmin = new Intl.NumberFormat('en-US', { 
                            style: 'currency', 
                            currency: 'USD',
                            maximumFractionDigits: 0
                        }).format(adminValue);
                        
                        tooltipContent += `<br>Municipality Admin Costs: ${municipalityAdmin}`;
                    } else {
                        tooltipContent += `<br>Municipality Admin Costs: No data available`;
                    }
                } else {
                    tooltipContent += `<br>Municipality Admin Costs: No data available`;
                }
                
                layer.bindTooltip(tooltipContent, { sticky: true });
                
                layer.on('click', function() {
                    if (municipalityName !== "Unknown Municipality") {
                        window.location.href = `/municipalities/${encodeURIComponent(municipalityName)}`;
                    }
                });
            }
            
            function updateLegend() {
                const legendElement = document.getElementById('mapLegend');
                const legendItems = document.getElementById('legendItems');
                
                legendItems.innerHTML = '';
                
                if (currentView === 'default') {
                    legendElement.style.display = 'none';
                    return;
                }
                
                legendElement.style.display = 'block';
                legendElement.style.marginBottom = '110px'
                
                const categories = colorPalettes[currentView];
                
                for (const [category, color] of Object.entries(categories)) {
                    const item = document.createElement('div');
                    item.style.display = 'flex';
                    item.style.alignItems = 'center';
                    item.style.marginBottom = '3px';
                    
                    const colorBox = document.createElement('span');
                    colorBox.style.backgroundColor = color;
                    colorBox.style.width = '15px';
                    colorBox.style.height = '15px';
                    colorBox.style.display = 'inline-block';
                    colorBox.style.marginRight = '5px';
                    
                    const label = document.createElement('span');
                    label.innerText = category;
                    label.style.fontSize = '11.5px';
                    
                    item.appendChild(colorBox);
                    item.appendChild(label);
                    legendItems.appendChild(item);
                }
            }
            
            // Initial load
            updateMap();
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch("{{ asset('maps/ct-towns.geojson') }}")
            .then(response => response.json())
            .then(data => {
                const comparisonList = document.getElementById('nameComparisonList');
                const geoJsonNames = data.features.map(f => f.properties.TOWN_NAME).slice(0, 10);
                
                geoJsonNames.forEach(name => {
                    const found = municipalitiesData.find(m => m.name === name);
                    const li = document.createElement('li');
                    li.textContent = `${name}: ${found ? 'Found in database' : 'NOT FOUND IN DATABASE'}`;
                    comparisonList.appendChild(li);
                });
            });
    });
</script>
@endpush