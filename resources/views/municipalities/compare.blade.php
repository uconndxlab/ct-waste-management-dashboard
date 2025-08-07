@extends('layouts.app')

@section('title', 'Municipality Comparison')

@section('content')
    <h1 class="text-primary">{{ $municipalities[0]->name }} & {{ $municipalities[1]->name }} Comparison</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Category</th>
                <th>{{ $municipalities[0]->name }}</th>
                <th>{{ $municipalities[1]->name }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Recycling</td>
                <td>{{ $municipalities[0]->recycling ?? 'No data' }}</td>
                <td>{{ $municipalities[1]->recycling ?? 'No data' }}</td>
            </tr>
            <tr>
                <td>Tipping Fees</td>
                <td>{{ $municipalities[0]->tipping_fees ?? 'No data' }}</td>
                <td>{{ $municipalities[1]->tipping_fees ?? 'No data' }}</td>
            </tr>
            <tr>
                <td>Transfer Station Wages</td>
                <td>{{ $municipalities[0]->transfer_station_wages ?? 'No data' }}</td>
                <td>{{ $municipalities[1]->transfer_station_wages ?? 'No data' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Chart Canvas -->
    <div class="row mt-5">
        <div class="col-12">
            <canvas id="myChart" width="400" height="200"></canvas>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Helper function to convert dollar strings to numbers
    function dollarToNumber(value) {
        if (!value || value === 'No data') return 0;
        // Remove $ and commas, then convert to float
        return parseFloat(value.toString().replace(/[$,]/g, '')) || 0;
    }

    const data = {
        labels: ['Recycling', 'Tipping Fees', 'Transfer Station Wages'],
        datasets: [{
            label: @json($municipalities[0]->name),
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
            data: [
                dollarToNumber(@json($municipalities[0]->recycling)),
                dollarToNumber(@json($municipalities[0]->tipping_fees)),
                dollarToNumber(@json($municipalities[0]->transfer_station_wages))
            ]
        }, {
            label: @json($municipalities[1]->name),
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1,
            data: [
                dollarToNumber(@json($municipalities[1]->recycling)),
                dollarToNumber(@json($municipalities[1]->tipping_fees)),
                dollarToNumber(@json($municipalities[1]->transfer_station_wages))
            ]
        }]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Municipality Comparison - Side by Side'
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
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Categories'
                    }
                }
            }
        }
    };

    const myChart = new Chart(
        document.getElementById('myChart'),
        config
    );
</script>
@endpush

