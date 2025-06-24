@extends('layouts.app')

@section('title', 'Dashboard Home')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
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

        <main class="dashboard-home">

            <a href="/municipalities" class="view-all-button-link">
                <div class="view-all-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                    </svg>
                    <h5>View & Search<br/>Municipalities</h5>
                </div>
            </a> 


            <div class="contact-section dashboard-section">
                <h5 class="contact-header blue-fill">Contact Information</h5>
                <hr class="hr-margin"/>
                <div class="contact-section-content">
                    <div class="phone-number contact-snip">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="blue-fill" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                          </svg>
                          <p>123-456-7890</p>
                    </div>
                    <div class="email contact-snip">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="blue-fill" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
                          </svg>
                          <p>ctwaste@uconn.edu</p>
                    </div>
                    <div class="phone-number contact-snip">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="blue-fill" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                          </svg>
                          <p>123-456-7890</p>
                    </div>
                    <div class="phone-number contact-snip">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="blue-fill" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                          </svg>
                          <p>123-456-7890</p>
                    </div>
                </div>
            </div>

            <div class="external-resources dashboard-section">
                <h5 class="blue-fill">External Resources</h5>
                <hr class="hr-margin"/>

                <div class="external-content">
                    <p><a href="#">CT Department of Transportation</a></p>
                    <p><a href="#">CT Data & Information</a></p>
                    <p><a href="#">Some Other Link</a></p>
                </div>
                
            </div>


        </main>

        <section class="dashboard-home mt-5">
            <div class="dashboard-section other-info">
                <h5 class="blue-fill">Welcome Message</h5>
                <hr class="hr-margin"/>
                <div class="external-content">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                </div>
            </div>

            <div class="dashboard-map">
                <div id="ctMap" style="height: 500px; width: 100%;"></div>
            </div>
        </section>



    </body>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
    <script>
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
                    function getColor(value) { // Example: Color based on a hypothetical 'density' property
                        // Replace 'density' and the logic with your actual data property and coloring scheme
                        return value > 1000 ? '#800026' :
                               value > 500  ? '#BD0026' :
                               value > 200  ? '#E31A1C' :
                               value > 100  ? '#FC4E2A' :
                               value > 50   ? '#FD8D3C' :
                               value > 20   ? '#FEB24C' :
                               value > 10   ? '#FED976' :
                                            '#FFEDA0';
                    }

                    L.geoJSON(data, {
                        style: function(feature) {
                            // Example: using a 'density' property from GeoJSON for styling
                            // Replace 'density' with a relevant property from your GeoJSON
                            const value = feature.properties.density || 0;
                            return {
                                fillColor: getColor(value),
                                weight: 1,
                                opacity: 1,
                                color: 'white',
                                fillOpacity: 0.7
                            };
                        },
                        onEachFeature: function(feature, layer) {
                            // Adjust property name based on your GeoJSON (e.g., NAME, town, municipality_name)
                            const municipalityName = feature.properties.NAME || feature.properties.town || "Unknown Municipality";

                            let tooltipContent = `<strong>${municipalityName}</strong>`;

                            // Example: Accessing data passed from controller if you set it up
                            // const additionalData = municipalitiesData[municipalityName] || {};
                            // if (additionalData.population) {
                            //     tooltipContent += `<br/>Population: ${additionalData.population}`;
                            // }
                            // Add more data from your $municipalitiesData if needed

                            layer.bindTooltip(tooltipContent, { sticky: true });

                            // Optional: Add a popup on click
                            // let popupContent = `<h5>${municipalityName}</h5><p>More details here...</p>`;
                            // layer.bindPopup(popupContent);

                            layer.on({
                                mouseover: function(e) {
                                    const currentLayer = e.target;
                                    currentLayer.setStyle({
                                        weight: 3,
                                        color: '#666',
                                        fillOpacity: 0.9
                                    });
                                    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                                        currentLayer.bringToFront();
                                    }
                                },
                                mouseout: function(e) {
                                    // Reset style - this requires the geojson layer reference or re-applying original style
                                    // For simplicity, you might need to store the original style or re-evaluate it
                                    // This is a simplified reset:
                                    const originalValue = feature.properties.density || 0;
                                     e.target.setStyle({
                                        fillColor: getColor(originalValue),
                                        weight: 1,
                                        color: 'white',
                                        fillOpacity: 0.7
                                    });
                                }
                                // click: function(e) { map.fitBounds(e.target.getBounds()); } // Optional: zoom on click
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