<!-- Main Content Row -->
<div class="row">
    <!-- Per Capita Comparison -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Per Capita Comparison (Latest Year)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
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
    @else
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Historical Data</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Limited Historical Data:</strong> Trend analysis requires at least two years of overlapping data between municipalities.
                </div>
            </div>
        </div>
    </div>
    @endif
</div>



@if(count($commonYears) > 1)
<script type="text/javascript">
// Store chart data in global variables
window.chartData = {
    commonYears: @json($commonYears),
    municipality1Name: @json($municipalities[0]->name),
    municipality2Name: @json($municipalities[1]->name),
    municipality1RecyclingData: @json($municipality1TrendData['recycling'] ?? []),
    municipality2RecyclingData: @json($municipality2TrendData['recycling'] ?? []),
    municipality1TippingData: @json($municipality1TrendData['tipping_fees'] ?? []),
    municipality2TippingData: @json($municipality2TrendData['tipping_fees'] ?? []),
    municipality1TransferData: @json($municipality1TrendData['transfer_station_wages'] ?? []),
    municipality2TransferData: @json($municipality2TrendData['transfer_station_wages'] ?? []),
    municipality1TotalData: @json($municipality1TrendData['total_sanitation_refuse'] ?? []),
    municipality2TotalData: @json($municipality2TrendData['total_sanitation_refuse'] ?? [])
};

// Initialize charts immediately
(function() {
    
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded');
        return;
    }

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: false,  // Don't force zero, let Chart.js auto-scale
                ticks: {
                    callback: function(value) {
                        return '$' + value.toFixed(2);
                    }
                }
            }
        },
        elements: {
            point: {
                radius: 4,  // Make points more visible
                hoverRadius: 6
            },
            line: {
                borderWidth: 3  // Make lines thicker
            }
        }
    };

    // Create charts
    setTimeout(() => {
        // Recycling Chart
        const recyclingCanvas = document.getElementById('recyclingChart');
        if (recyclingCanvas) {
            
            new Chart(recyclingCanvas, {
                type: 'line',
                data: {
                    labels: window.chartData.commonYears,
                    datasets: [{
                        label: window.chartData.municipality1Name,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        data: window.chartData.municipality1RecyclingData,
                        tension: 0.4
                    }, {
                        label: window.chartData.municipality2Name,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        data: window.chartData.municipality2RecyclingData,
                        tension: 0.4
                    }]
                },
                options: chartOptions
            });
        }

        // Tipping Fees Chart
        const tippingCanvas = document.getElementById('tippingChart');
        if (tippingCanvas) {
            console.log('Creating tipping chart');
            new Chart(tippingCanvas, {
                type: 'line',
                data: {
                    labels: window.chartData.commonYears,
                    datasets: [{
                        label: window.chartData.municipality1Name,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        data: window.chartData.municipality1TippingData,
                        tension: 0.4
                    }, {
                        label: window.chartData.municipality2Name,
                        borderColor: 'rgba(255, 205, 86, 1)',
                        data: window.chartData.municipality2TippingData,
                        tension: 0.4
                    }]
                },
                options: chartOptions
            });
        }

        // Transfer Station Chart
        const transferCanvas = document.getElementById('transferChart');
        if (transferCanvas) {
            console.log('Creating transfer chart');
            new Chart(transferCanvas, {
                type: 'line',
                data: {
                    labels: window.chartData.commonYears,
                    datasets: [{
                        label: window.chartData.municipality1Name,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        data: window.chartData.municipality1TransferData,
                        tension: 0.4
                    }, {
                        label: window.chartData.municipality2Name,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        data: window.chartData.municipality2TransferData,
                        tension: 0.4
                    }]
                },
                options: chartOptions
            });
        }

        // Total Sanitation Chart
        const totalCanvas = document.getElementById('totalChart');
        if (totalCanvas) {
            console.log('Creating total chart');
            new Chart(totalCanvas, {
                type: 'line',
                data: {
                    labels: window.chartData.commonYears,
                    datasets: [{
                        label: window.chartData.municipality1Name,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        data: window.chartData.municipality1TotalData,
                        tension: 0.4
                    }, {
                        label: window.chartData.municipality2Name,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        data: window.chartData.municipality2TotalData,
                        tension: 0.4
                    }]
                },
                options: chartOptions
            });
        }
    }, 100);
})();
</script>
@endif