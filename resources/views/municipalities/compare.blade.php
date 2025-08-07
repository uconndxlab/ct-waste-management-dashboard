@extends('layouts.app')

@section('title', 'Municipality Comparison')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">{{ $municipalities[0]->name }} & {{ $municipalities[1]->name }} Comparison</h1>
        <a href="{{ route('municipalities.all') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to All Municipalities
        </a>
    </div>


    <!-- Per Capita Comparison -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-person me-2"></i>Per Capita Comparison (Latest Year)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>{{ $municipalities[0]->name }}</th>
                            <th>{{ $municipalities[1]->name }}</th>
                            <th>Difference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Population ({{ $municipalities[0]->year ?? 'Latest' }})</strong></td>
                            <td>{{ number_format($municipalities[0]->latest_population ?? 0) }}</td>
                            <td>{{ number_format($municipalities[1]->latest_population ?? 0) }}</td>
                            <td>{{ number_format(($municipalities[0]->latest_population ?? 0) - ($municipalities[1]->latest_population ?? 0)) }}</td>
                        </tr>
                        <tr>
                            <td>Recycling per Capita</td>
                            <td>${{ number_format($municipalities[0]->recycling_per_capita ?? 0, 2) }}</td>
                            <td>${{ number_format($municipalities[1]->recycling_per_capita ?? 0, 2) }}</td>
                            <td class="{{ ($municipalities[0]->recycling_per_capita ?? 0) > ($municipalities[1]->recycling_per_capita ?? 0) ? 'text-danger' : 'text-success' }}">
                                ${{ number_format(($municipalities[0]->recycling_per_capita ?? 0) - ($municipalities[1]->recycling_per_capita ?? 0), 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Tipping Fees per Capita</td>
                            <td>${{ number_format($municipalities[0]->tipping_fees_per_capita ?? 0, 2) }}</td>
                            <td>${{ number_format($municipalities[1]->tipping_fees_per_capita ?? 0, 2) }}</td>
                            <td class="{{ ($municipalities[0]->tipping_fees_per_capita ?? 0) > ($municipalities[1]->tipping_fees_per_capita ?? 0) ? 'text-danger' : 'text-success' }}">
                                ${{ number_format(($municipalities[0]->tipping_fees_per_capita ?? 0) - ($municipalities[1]->tipping_fees_per_capita ?? 0), 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Transfer Station Wages per Capita</td>
                            <td>${{ number_format($municipalities[0]->transfer_station_wages_per_capita ?? 0, 2) }}</td>
                            <td>${{ number_format($municipalities[1]->transfer_station_wages_per_capita ?? 0, 2) }}</td>
                            <td class="{{ ($municipalities[0]->transfer_station_wages_per_capita ?? 0) > ($municipalities[1]->transfer_station_wages_per_capita ?? 0) ? 'text-danger' : 'text-success' }}">
                                ${{ number_format(($municipalities[0]->transfer_station_wages_per_capita ?? 0) - ($municipalities[1]->transfer_station_wages_per_capita ?? 0), 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Total Sanitation per Capita</td>
                            <td>${{ number_format($municipalities[0]->total_sanitation_refuse_per_capita ?? 0, 2) }}</td>
                            <td>${{ number_format($municipalities[1]->total_sanitation_refuse_per_capita ?? 0, 2) }}</td>
                            <td class="{{ ($municipalities[0]->total_sanitation_refuse_per_capita ?? 0) > ($municipalities[1]->total_sanitation_refuse_per_capita ?? 0) ? 'text-danger' : 'text-success' }}">
                                ${{ number_format(($municipalities[0]->total_sanitation_refuse_per_capita ?? 0) - ($municipalities[1]->total_sanitation_refuse_per_capita ?? 0), 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Year-over-Year Trends -->
    @if(count($commonYears) > 1)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Year-over-Year Trends ({{ min($commonYears) }} - {{ max($commonYears) }})</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <h6 class="text-center mb-3">Recycling per Capita</h6>
                    <div style="height: 300px;">
                        <canvas id="recyclingChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <h6 class="text-center mb-3">Tipping Fees per Capita</h6>
                    <div style="height: 300px;">
                        <canvas id="tippingChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <h6 class="text-center mb-3">Transfer Station Wages per Capita</h6>
                    <div style="height: 300px;">
                        <canvas id="transferChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <h6 class="text-center mb-3">Total Sanitation per Capita</h6>
                    <div style="height: 300px;">
                        <canvas id="totalChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

@endsection

@push('scripts')
<script>
    // Helper function to convert dollar strings to numbers
    function dollarToNumber(value) {
        if (!value || value === 'No data') return 0;
        return parseFloat(value.toString().replace(/[$,]/g, '')) || 0;
    }

    // Current Year Per Capita Comparison Chart
    const currentData = {
        labels: ['Recycling per Capita', 'Tipping Fees per Capita', 'Transfer Station Wages per Capita', 'Total Sanitation per Capita'],
        datasets: [{
            label: @json($municipalities[0]->name),
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            data: [
                @json($municipalities[0]->recycling_per_capita ?? 0),
                @json($municipalities[0]->tipping_fees_per_capita ?? 0),
                @json($municipalities[0]->transfer_station_wages_per_capita ?? 0),
                @json($municipalities[0]->total_sanitation_refuse_per_capita ?? 0)
            ]
        }, {
            label: @json($municipalities[1]->name),
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            data: [
                @json($municipalities[1]->recycling_per_capita ?? 0),
                @json($municipalities[1]->tipping_fees_per_capita ?? 0),
                @json($municipalities[1]->transfer_station_wages_per_capita ?? 0),
                @json($municipalities[1]->total_sanitation_refuse_per_capita ?? 0)
            ]
        }]
    };

    const currentConfig = {
        type: 'bar',
        data: currentData,
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Per Capita Comparison - Current Year'
                },
                legend: {
                    display: true,
                    position: 'top'
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
                }
            }
        }
    };

    new Chart(document.getElementById('currentChart'), currentConfig);

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
                label: @json($municipalities[0]->name),
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                data: @json($municipality1TrendData['recycling']),
                tension: 0.4,
                fill: true
            }, {
                label: @json($municipalities[1]->name),
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                data: @json($municipality2TrendData['recycling']),
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
                label: @json($municipalities[0]->name),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                data: @json($municipality1TrendData['tipping_fees']),
                tension: 0.4,
                fill: true
            }, {
                label: @json($municipalities[1]->name),
                borderColor: 'rgba(255, 205, 86, 1)',
                backgroundColor: 'rgba(255, 205, 86, 0.1)',
                data: @json($municipality2TrendData['tipping_fees']),
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
                label: @json($municipalities[0]->name),
                borderColor: 'rgba(153, 102, 255, 1)',
                backgroundColor: 'rgba(153, 102, 255, 0.1)',
                data: @json($municipality1TrendData['transfer_station_wages']),
                tension: 0.4,
                fill: true
            }, {
                label: @json($municipalities[1]->name),
                borderColor: 'rgba(255, 159, 64, 1)',
                backgroundColor: 'rgba(255, 159, 64, 0.1)',
                data: @json($municipality2TrendData['transfer_station_wages']),
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
                label: @json($municipalities[0]->name),
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                data: @json($municipality1TrendData['total_sanitation_refuse']),
                tension: 0.4,
                fill: true
            }, {
                label: @json($municipalities[1]->name),
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                data: @json($municipality2TrendData['total_sanitation_refuse']),
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

