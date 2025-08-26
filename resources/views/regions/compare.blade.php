@extends('layouts.app')

@section('title', 'Regional Comparison')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">{{ $regions[0]->region_name }} & {{ $regions[1]->region_name }} Comparison</h1>
        <a href="{{ route('regions.list', $regionType) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to {{ $regionTypeLabel }}
        </a>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Per Capita Comparison -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Per Capita Comparison (Latest Year)</h5>
                </div>
                <div class="card-body" style="overflow-y: auto; max-height: 600px;">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead class="sticky-top bg-white">
                                <tr>
                                    <th>Category</th>
                                    <th>{{ $regions[0]->region_name }}</th>
                                    <th>{{ $regions[1]->region_name }}</th>
                                    <th>Difference</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Total Population ({{ $regions[0]->population_year ?? 'Latest' }})</strong></td>
                                    <td>
                                        @if($regions[0]->total_population && $regions[0]->total_population > 0)
                                            {{ number_format($regions[0]->total_population) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[1]->total_population && $regions[1]->total_population > 0)
                                            {{ number_format($regions[1]->total_population) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[0]->total_population && $regions[1]->total_population)
                                            {{ number_format($regions[0]->total_population - $regions[1]->total_population) }}
                                        @else
                                            <span class="text-muted">Cannot calculate</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Municipalities</strong></td>
                                    <td>{{ $regions[0]->total_municipalities ?? 0 }}</td>
                                    <td>{{ $regions[1]->total_municipalities ?? 0 }}</td>
                                    <td>{{ ($regions[0]->total_municipalities ?? 0) - ($regions[1]->total_municipalities ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Municipalities with Data</strong></td>
                                    <td>{{ $regions[0]->municipalities_with_data ?? 0 }}</td>
                                    <td>{{ $regions[1]->municipalities_with_data ?? 0 }}</td>
                                    <td>{{ ($regions[0]->municipalities_with_data ?? 0) - ($regions[1]->municipalities_with_data ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <td>Bulky Waste per Capita</td>
                                    <td>
                                        @if($regions[0]->total_bulky_waste_per_capita !== null)
                                            ${{ number_format($regions[0]->total_bulky_waste_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[1]->total_bulky_waste_per_capita !== null)
                                            ${{ number_format($regions[1]->total_bulky_waste_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[0]->total_bulky_waste_per_capita !== null && $regions[1]->total_bulky_waste_per_capita !== null)
                                            @php
                                                $difference = $regions[0]->total_bulky_waste_per_capita - $regions[1]->total_bulky_waste_per_capita;
                                                $class = $difference > 0 ? 'text-danger' : 'text-success';
                                            @endphp
                                            <span class="{{ $class }}">${{ number_format($difference, 2) }}</span>
                                        @else
                                            <span class="text-muted">Cannot calculate</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Recycling per Capita</td>
                                    <td>
                                        @if($regions[0]->total_recycling_per_capita !== null)
                                            ${{ number_format($regions[0]->total_recycling_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[1]->total_recycling_per_capita !== null)
                                            ${{ number_format($regions[1]->total_recycling_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[0]->total_recycling_per_capita !== null && $regions[1]->total_recycling_per_capita !== null)
                                            @php
                                                $difference = $regions[0]->total_recycling_per_capita - $regions[1]->total_recycling_per_capita;
                                                $class = $difference > 0 ? 'text-danger' : 'text-success';
                                            @endphp
                                            <span class="{{ $class }}">${{ number_format($difference, 2) }}</span>
                                        @else
                                            <span class="text-muted">Cannot calculate</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tipping Fees per Capita</td>
                                    <td>
                                        @if($regions[0]->total_tipping_fees_per_capita !== null)
                                            ${{ number_format($regions[0]->total_tipping_fees_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[1]->total_tipping_fees_per_capita !== null)
                                            ${{ number_format($regions[1]->total_tipping_fees_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[0]->total_tipping_fees_per_capita !== null && $regions[1]->total_tipping_fees_per_capita !== null)
                                            @php
                                                $difference = $regions[0]->total_tipping_fees_per_capita - $regions[1]->total_tipping_fees_per_capita;
                                                $class = $difference > 0 ? 'text-danger' : 'text-success';
                                            @endphp
                                            <span class="{{ $class }}">${{ number_format($difference, 2) }}</span>
                                        @else
                                            <span class="text-muted">Cannot calculate</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Admin Costs per Capita</td>
                                    <td>
                                        @if($regions[0]->total_admin_costs_per_capita !== null)
                                            ${{ number_format($regions[0]->total_admin_costs_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[1]->total_admin_costs_per_capita !== null)
                                            ${{ number_format($regions[1]->total_admin_costs_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[0]->total_admin_costs_per_capita !== null && $regions[1]->total_admin_costs_per_capita !== null)
                                            @php
                                                $difference = $regions[0]->total_admin_costs_per_capita - $regions[1]->total_admin_costs_per_capita;
                                                $class = $difference > 0 ? 'text-danger' : 'text-success';
                                            @endphp
                                            <span class="{{ $class }}">${{ number_format($difference, 2) }}</span>
                                        @else
                                            <span class="text-muted">Cannot calculate</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hazardous Waste per Capita</td>
                                    <td>
                                        @if($regions[0]->total_hazardous_waste_per_capita !== null)
                                            ${{ number_format($regions[0]->total_hazardous_waste_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[1]->total_hazardous_waste_per_capita !== null)
                                            ${{ number_format($regions[1]->total_hazardous_waste_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[0]->total_hazardous_waste_per_capita !== null && $regions[1]->total_hazardous_waste_per_capita !== null)
                                            @php
                                                $difference = $regions[0]->total_hazardous_waste_per_capita - $regions[1]->total_hazardous_waste_per_capita;
                                                $class = $difference > 0 ? 'text-danger' : 'text-success';
                                            @endphp
                                            <span class="{{ $class }}">${{ number_format($difference, 2) }}</span>
                                        @else
                                            <span class="text-muted">Cannot calculate</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Contractual Services per Capita</td>
                                    <td>
                                        @if($regions[0]->total_contractual_services_per_capita !== null)
                                            ${{ number_format($regions[0]->total_contractual_services_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[1]->total_contractual_services_per_capita !== null)
                                            ${{ number_format($regions[1]->total_contractual_services_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[0]->total_contractual_services_per_capita !== null && $regions[1]->total_contractual_services_per_capita !== null)
                                            @php
                                                $difference = $regions[0]->total_contractual_services_per_capita - $regions[1]->total_contractual_services_per_capita;
                                                $class = $difference > 0 ? 'text-danger' : 'text-success';
                                            @endphp
                                            <span class="{{ $class }}">${{ number_format($difference, 2) }}</span>
                                        @else
                                            <span class="text-muted">Cannot calculate</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Landfill Costs per Capita</td>
                                    <td>
                                        @if($regions[0]->total_landfill_costs_per_capita !== null)
                                            ${{ number_format($regions[0]->total_landfill_costs_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[1]->total_landfill_costs_per_capita !== null)
                                            ${{ number_format($regions[1]->total_landfill_costs_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[0]->total_landfill_costs_per_capita !== null && $regions[1]->total_landfill_costs_per_capita !== null)
                                            @php
                                                $difference = $regions[0]->total_landfill_costs_per_capita - $regions[1]->total_landfill_costs_per_capita;
                                                $class = $difference > 0 ? 'text-danger' : 'text-success';
                                            @endphp
                                            <span class="{{ $class }}">${{ number_format($difference, 2) }}</span>
                                        @else
                                            <span class="text-muted">Cannot calculate</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Transfer Station Wages per Capita</td>
                                    <td>
                                        @if($regions[0]->total_transfer_station_wages_per_capita !== null)
                                            ${{ number_format($regions[0]->total_transfer_station_wages_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[1]->total_transfer_station_wages_per_capita !== null)
                                            ${{ number_format($regions[1]->total_transfer_station_wages_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[0]->total_transfer_station_wages_per_capita !== null && $regions[1]->total_transfer_station_wages_per_capita !== null)
                                            @php
                                                $difference = $regions[0]->total_transfer_station_wages_per_capita - $regions[1]->total_transfer_station_wages_per_capita;
                                                $class = $difference > 0 ? 'text-danger' : 'text-success';
                                            @endphp
                                            <span class="{{ $class }}">${{ number_format($difference, 2) }}</span>
                                        @else
                                            <span class="text-muted">Cannot calculate</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hauling Fees per Capita</td>
                                    <td>
                                        @if($regions[0]->total_hauling_fees_per_capita !== null)
                                            ${{ number_format($regions[0]->total_hauling_fees_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[1]->total_hauling_fees_per_capita !== null)
                                            ${{ number_format($regions[1]->total_hauling_fees_per_capita, 2) }}
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($regions[0]->total_hauling_fees_per_capita !== null && $regions[1]->total_hauling_fees_per_capita !== null)
                                            @php
                                                $difference = $regions[0]->total_hauling_fees_per_capita - $regions[1]->total_hauling_fees_per_capita;
                                                $class = $difference > 0 ? 'text-danger' : 'text-success';
                                            @endphp
                                            <span class="{{ $class }}">${{ number_format($difference, 2) }}</span>
                                        @else
                                            <span class="text-muted">Cannot calculate</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Curbside Pickup Fees per Capita</td>
                                    <td>${{ number_format($regions[0]->total_curbside_pickup_fees_per_capita ?? 0, 2) }}</td>
                                    <td>${{ number_format($regions[1]->total_curbside_pickup_fees_per_capita ?? 0, 2) }}</td>
                                    <td class="{{ ($regions[0]->total_curbside_pickup_fees_per_capita ?? 0) > ($regions[1]->total_curbside_pickup_fees_per_capita ?? 0) ? 'text-danger' : 'text-success' }}">
                                        ${{ number_format(($regions[0]->total_curbside_pickup_fees_per_capita ?? 0) - ($regions[1]->total_curbside_pickup_fees_per_capita ?? 0), 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Waste Collection per Capita</td>
                                    <td>${{ number_format($regions[0]->total_waste_collection_per_capita ?? 0, 2) }}</td>
                                    <td>${{ number_format($regions[1]->total_waste_collection_per_capita ?? 0, 2) }}</td>
                                    <td class="{{ ($regions[0]->total_waste_collection_per_capita ?? 0) > ($regions[1]->total_waste_collection_per_capita ?? 0) ? 'text-danger' : 'text-success' }}">
                                        ${{ number_format(($regions[0]->total_waste_collection_per_capita ?? 0) - ($regions[1]->total_waste_collection_per_capita ?? 0), 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Sanitation per Capita</strong></td>
                                    <td><strong>${{ number_format($regions[0]->total_total_sanitation_refuse_per_capita ?? 0, 2) }}</strong></td>
                                    <td><strong>${{ number_format($regions[1]->total_total_sanitation_refuse_per_capita ?? 0, 2) }}</strong></td>
                                    <td class="{{ ($regions[0]->total_total_sanitation_refuse_per_capita ?? 0) > ($regions[1]->total_total_sanitation_refuse_per_capita ?? 0) ? 'text-danger' : 'text-success' }}">
                                        <strong>${{ number_format(($regions[0]->total_total_sanitation_refuse_per_capita ?? 0) - ($regions[1]->total_total_sanitation_refuse_per_capita ?? 0), 2) }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Year-over-Year Trends -->
        @if(count($commonYears) > 1)
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Year-over-Year Trends ({{ min($commonYears) }} - {{ max($commonYears) }})</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <h6 class="text-center mb-2 small">Recycling per Capita</h6>
                            <div style="height: 200px;">
                                <canvas id="recyclingChart"></canvas>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <h6 class="text-center mb-2 small">Tipping Fees per Capita</h6>
                            <div style="height: 200px;">
                                <canvas id="tippingChart"></canvas>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <h6 class="text-center mb-2 small">Transfer Station Wages per Capita</h6>
                            <div style="height: 200px;">
                                <canvas id="transferChart"></canvas>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <h6 class="text-center mb-2 small">Total Sanitation per Capita</h6>
                            <div style="height: 200px;">
                                <canvas id="totalChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @elseif(count($commonYears) == 1)
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Historical Data</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Limited Historical Data:</strong> Only one year of common data ({{ $commonYears[0] }}) is available for both regions. Trend analysis requires at least two years of data.
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Historical Data</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>No Common Historical Data:</strong> These regions do not have overlapping years of financial data, so trend comparison is not available.
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
    // Helper function to convert dollar strings to numbers
    function dollarToNumber(value) {
        if (!value || value === 'No data') return 0;
        return parseFloat(value.toString().replace(/[$,]/g, '')) || 0;
    }

    @if(count($commonYears) > 1)
    // Common chart configuration
    const commonChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    padding: 10,
                    usePointStyle: true
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toFixed(2);
                    }
                }
            },
            x: {
                ticks: {
                    maxRotation: 45
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        },
        elements: {
            point: {
                radius: 3,
                hoverRadius: 5
            },
            line: {
                borderWidth: 2
            }
        }
    };

    // Recycling Chart
    new Chart(document.getElementById('recyclingChart'), {
        type: 'line',
        data: {
            labels: @json($commonYears),
            datasets: [{
                label: @json($regions[0]->region_name),
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                data: @json($region1TrendData['total_recycling']),
                tension: 0.4,
                fill: true
            }, {
                label: @json($regions[1]->region_name),
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                data: @json($region2TrendData['total_recycling']),
                tension: 0.4,
                fill: true
            }]
        },
        options: commonChartOptions
    });

    // Tipping Fees Chart
    new Chart(document.getElementById('tippingChart'), {
        type: 'line',
        data: {
            labels: @json($commonYears),
            datasets: [{
                label: @json($regions[0]->region_name),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                data: @json($region1TrendData['total_tipping_fees']),
                tension: 0.4,
                fill: true
            }, {
                label: @json($regions[1]->region_name),
                borderColor: 'rgba(255, 205, 86, 1)',
                backgroundColor: 'rgba(255, 205, 86, 0.1)',
                data: @json($region2TrendData['total_tipping_fees']),
                tension: 0.4,
                fill: true
            }]
        },
        options: commonChartOptions
    });

    // Transfer Station Wages Chart
    new Chart(document.getElementById('transferChart'), {
        type: 'line',
        data: {
            labels: @json($commonYears),
            datasets: [{
                label: @json($regions[0]->region_name),
                borderColor: 'rgba(153, 102, 255, 1)',
                backgroundColor: 'rgba(153, 102, 255, 0.1)',
                data: @json($region1TrendData['total_transfer_station_wages']),
                tension: 0.4,
                fill: true
            }, {
                label: @json($regions[1]->region_name),
                borderColor: 'rgba(255, 159, 64, 1)',
                backgroundColor: 'rgba(255, 159, 64, 0.1)',
                data: @json($region2TrendData['total_transfer_station_wages']),
                tension: 0.4,
                fill: true
            }]
        },
        options: commonChartOptions
    });

    // Total Sanitation Chart
    new Chart(document.getElementById('totalChart'), {
        type: 'line',
        data: {
            labels: @json($commonYears),
            datasets: [{
                label: @json($regions[0]->region_name),
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                data: @json($region1TrendData['total_total_sanitation_refuse']),
                tension: 0.4,
                fill: true
            }, {
                label: @json($regions[1]->region_name),
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                data: @json($region2TrendData['total_total_sanitation_refuse']),
                tension: 0.4,
                fill: true
            }]
        },
        options: commonChartOptions
    });
    @endif

    // Helper function for PHP usage in JavaScript
    function dollarToNumber(value) {
        if (!value || value === 'No data') return 0;
        return parseFloat(value.toString().replace(/[$,]/g, '')) || 0;
    }
</script>
@endpush