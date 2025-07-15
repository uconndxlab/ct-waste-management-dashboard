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
@endsection