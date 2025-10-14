<div class="row">
    <!-- Municipality Info Section -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i>Municipality Information</h5>
            </div>
            <div class="card-body">
                @if($townInfo)
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Department</h6>
                            <p class="mb-3">{{ $townInfo->department ?? 'N/A' }}</p>
                            
                            @if($townInfo->contact_1)
                                <h6 class="text-muted">Primary Contact</h6>
                                <p class="mb-1"><strong>{{ $townInfo->contact_1 }}</strong></p>
                                <p class="mb-1 text-muted">{{ $townInfo->title_1 ?? '' }}</p>
                                @if($townInfo->phone_1)
                                    <p class="mb-1"><i class="bi bi-telephone me-2"></i>{{ $townInfo->phone_1 }}</p>
                                @endif
                                @if($townInfo->email_1)
                                    <p class="mb-3"><i class="bi bi-envelope me-2"></i><a href="mailto:{{ $townInfo->email_1 }}">{{ $townInfo->email_1 }}</a></p>
                                @endif
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            @if($townClassification)
                                <h6 class="text-muted">Classification</h6>
                                <p class="mb-2"><strong>County:</strong> {{ $townClassification->county ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>Region:</strong> {{ $townClassification->geographical_region ?? 'N/A' }}</p>
                                <p class="mb-3"><strong>Type:</strong> {{ $townClassification->region_type ?? 'N/A' }}</p>
                            @endif
                            
                            @if($townInfo && $townInfo->contact_2)
                                <h6 class="text-muted">Secondary Contact</h6>
                                <p class="mb-1"><strong>{{ $townInfo->contact_2 }}</strong></p>
                                <p class="mb-1 text-muted">{{ $townInfo->title_2 ?? '' }}</p>
                                @if($townInfo->phone_2)
                                    <p class="mb-1"><i class="bi bi-telephone me-2"></i>{{ $townInfo->phone_2 }}</p>
                                @endif
                                @if($townInfo->email_2)
                                    <p class="mb-3"><i class="bi bi-envelope me-2"></i><a href="mailto:{{ $townInfo->email_2 }}">{{ $townInfo->email_2 }}</a></p>
                                @endif
                            @endif
                        </div>
                    </div>
                    
                    @if($townInfo->notes || $townInfo->other_useful_notes)
                        <hr>
                        @if($townInfo->notes)
                            <h6 class="text-muted">Notes</h6>
                            <p class="mb-3">{{ $townInfo->notes }}</p>
                        @endif
                        @if($townInfo->other_useful_notes)
                            <h6 class="text-muted">Other Useful Notes</h6>
                            <p class="mb-0">{{ $townInfo->other_useful_notes }}</p>
                        @endif
                    @endif
                @else
                    <div class="text-center text-muted">
                        <i class="bi bi-info-circle display-4 mb-3"></i>
                        <p>No additional information available for this municipality.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Financial Data Section -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Financial Reports</h5>
            </div>
            <div class="card-body">
                @if($reports->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>Total Sanitation</th>
                                    <th>Recycling</th>
                                    <th>Tipping Fees</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports->sortByDesc('year') as $report)
                                    <tr>
                                        <td><strong>{{ $report->year }}</strong></td>
                                        <td>{{ $report->total_sanitation_refuse ? '$' . number_format((float)str_replace(['$', ','], '', $report->total_sanitation_refuse)) : 'N/A' }}</td>
                                        <td>{{ $report->recycling ? '$' . number_format((float)str_replace(['$', ','], '', $report->recycling)) : 'N/A' }}</td>
                                        <td>{{ $report->tipping_fees ? '$' . number_format((float)str_replace(['$', ','], '', $report->tipping_fees)) : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($hasAnyPopulationData)
                        <hr>
                        <h6 class="text-muted mb-3">Per Capita Data (Latest Year)</h6>
                        <div class="row text-center">
                            @php $latestReport = $reports->sortByDesc('year')->first(); @endphp
                            <div class="col-6">
                                <div class="border rounded p-2 mb-2">
                                    <small class="text-muted d-block">Population</small>
                                    <strong>{{ $population ? number_format($population) : 'N/A' }}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2 mb-2">
                                    <small class="text-muted d-block">Total Sanitation/Capita</small>
                                    <strong>${{ $latestReport->total_sanitation_refuse_per_capita ?? 'N/A' }}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2 mb-2">
                                    <small class="text-muted d-block">Recycling/Capita</small>
                                    <strong>${{ $latestReport->recycling_per_capita ?? 'N/A' }}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2 mb-2">
                                    <small class="text-muted d-block">Tipping Fees/Capita</small>
                                    <strong>${{ $latestReport->tipping_fees_per_capita ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted">
                        <i class="bi bi-graph-down display-4 mb-3"></i>
                        <p>No financial reports available for this municipality.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($reports->count() > 1)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Historical Trends</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <canvas id="modalChart1" height="200"></canvas>
                        </div>
                        <div class="col-md-6 mb-3">
                            <canvas id="modalChart2" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Chart data for modal
        const modalChartData = {
            years: @json($reports->pluck('year')->map(function($year) { return (string)$year; })->values()),
            totalSanitation: @json($reports->map(function($report) { 
                return (float)str_replace(['$', ','], '', $report->total_sanitation_refuse ?: '0');
            })->values()),
            recycling: @json($reports->map(function($report) { 
                return (float)str_replace(['$', ','], '', $report->recycling ?: '0');
            })->values())
        };

        // Wait for Chart.js to be available and create charts
        setTimeout(() => {
            if (typeof Chart !== 'undefined') {
                // Total Sanitation Chart
                const ctx1 = document.getElementById('modalChart1');
                if (ctx1) {
                    new Chart(ctx1, {
                        type: 'line',
                        data: {
                            labels: modalChartData.years,
                            datasets: [{
                                label: 'Total Sanitation ($)',
                                data: modalChartData.totalSanitation,
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Total Sanitation Costs'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '$' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Recycling Chart
                const ctx2 = document.getElementById('modalChart2');
                if (ctx2) {
                    new Chart(ctx2, {
                        type: 'line',
                        data: {
                            labels: modalChartData.years,
                            datasets: [{
                                label: 'Recycling Costs ($)',
                                data: modalChartData.recycling,
                                borderColor: 'rgb(153, 102, 255)',
                                backgroundColor: 'rgba(153, 102, 255, 0.1)',
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Recycling Costs'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '$' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        }, 100);
    </script>
@endif