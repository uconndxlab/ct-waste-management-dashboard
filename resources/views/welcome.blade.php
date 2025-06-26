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
        document.addEventListener('DOMContentLoaded', function() {
            // Get data passed from controller (if you are passing any)
            // const municipalitiesData = @json($municipalitiesData ?? []); // Uncomment and use if needed

            // Initialize the map centered on Connecticut
            const map = L.map('ctMap').setView([41.390, -72.700], 8); // arbitary, can tweak if you want

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Load GeoJSON data
            // Ensure 'connecticut-municipalities.geojson' is in your public/geojson/ directory
            fetch("{{ asset('maps/ct-towns.geojson') }}") // Use asset() helper for correct path
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {

                    L.geoJSON(data, {
                        style: function(feature) {
                            const value = feature.properties.OBJECTID || 0;
                            return {
                                fillColor: '#3490dc',
                                weight: 1,
                                opacity: 1,
                                color: 'white',
                                fillOpacity: 0.7
                            };
                        },
                        onEachFeature: function(feature, layer) {
                            // Adjust property name based on your GeoJSON (e.g., NAME, town, municipality_name)
                            const municipalityName = feature.properties.TOWN_NAME || "Unknown Municipality";

                            const municipality = municipalitiesData.find(m => m.name === municipalityName);

                            const municipalityHref = `/municipalities/${encodeURIComponent(municipalityName)}`;

                            let tooltipContent = `<strong>${municipalityName}</strong>`;

                            layer.bindTooltip(tooltipContent, { sticky: true });

                            layer.on({
                                click: function() {
                                    if (municipalityName !== "Unknown Municipality") {
                                        window.location.href = municipalityHref;
                                    }
                                },

                            });
                        }
                    }).addTo(map);
                })
                .catch(error => {
                    console.error('Error loading or parsing GeoJSON:', error);
                    document.getElementById('ctMap').innerHTML = '<div class="alert alert-danger" role="alert">Could not load map data. Please check the console for errors.</div>';
                });
        });
    </script>
@endpush