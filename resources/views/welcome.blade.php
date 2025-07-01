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
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid #3490dc;
            border-radius: 4px;
            padding: 5px 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.4);
        }
    </style>
@endpush

@section('content')
    <body class="">

        {{-- <a href="/municipalities" class="mb-4">View All Municipalities</a> --}}
        <div class="d-flex align-items-center justifiy-content-between w-100">
                        <form action="{{ route('municipalities.all') }}" method="GET" class="flex-grow-1 me-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search municipalities..." value="{{ request('search') }}">
        
                </div>
            </form>
            <a  href="{{ route('municipalities.all') }}" class="btn btn-primary"><i class="bi bi-search"></i>View all municipalities</a>
        </div>

        <div class="container mt-4">
            <div class="row align-items-stretch">
                <!-- Map Section -->
                <section class="col-lg-7 mb-4">
                    <div class="dashboard-map rounded-lg shadow-sm w-100">
                        <div id="ctMap" style="height: 500px; width: 100%;"></div>
                    </div>
                </section>

                <section class="col-lg-5 d-flex justify-content-center flex-column">
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


    </body>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
    <script>
        const municipalitiesData = @json($municipalities);
        const townClassifications = @json($townClassifications ?? []);
        
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
                "Rural": "#1f77b4",
                "Suburban": "#ff7f0e", 
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
                    
                    // Create select element
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
                    
                    // Handle events
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
            
            // Add controls to map
            new L.Control.ViewSelector({ position: 'topright' }).addTo(map);
            legendControl = new L.Control.Legend({ position: 'bottomright' }).addTo(map);
            
            // Function to update the map view
            function updateMap() {
                if (geoJSONLayer) {
                    map.removeLayer(geoJSONLayer);
                }
                
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
            }
            
            // Style features based on current view
            function styleFeature(feature) {
                const municipalityName = feature.properties.TOWN_NAME || "Unknown";
                const townData = townClassifications[municipalityName];
                
                let fillColor = '#3490dc'; // Default color
                
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
                const municipalityHref = `/municipalities/${encodeURIComponent(municipalityName)}`;
                const townData = townClassifications[municipalityName];
                
                let tooltipContent = `<strong>${municipalityName}</strong>`;
                
                if (currentView !== 'default' && townData) {
                    let categoryLabel, categoryValue;
                    
                    switch(currentView) {
                        case 'county':
                            categoryLabel = 'County';
                            categoryValue = townData.county;
                            break;
                        case 'region':
                            categoryLabel = 'Planning Region';
                            categoryValue = townData.geographical_region;
                            break;
                        case 'type':
                            categoryLabel = 'Classification';
                            categoryValue = townData.region_type;
                            break;
                    }
                    
                    if (categoryValue) {
                        tooltipContent += `<br>${categoryLabel}: ${categoryValue}`;
                    }
                }
                
                layer.bindTooltip(tooltipContent, { sticky: true });
                
                layer.on('click', function() {
                    if (municipalityName !== "Unknown Municipality") {
                        window.location.href = municipalityHref;
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
                    label.style.fontSize = '12px';
                    
                    item.appendChild(colorBox);
                    item.appendChild(label);
                    legendItems.appendChild(item);
                }
            }
            
            // Initial load
            updateMap();
        });
    </script>
@endpush