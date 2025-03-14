@extends('layouts.app')

@section('title', $municipality->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('municipalities.all') }}">Municipalities</a></li>
    <li class="breadcrumb-item"><a href="{{ route('municipalities.view', ['name' => $municipality->name]) }}">{{ $municipality->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $municipality->year !== '' ? $municipality->year : 'Undated' }} Report</li>
@endsection

@section('content')
    <h1 class="text-primary">{{ $municipality->name }}</h1>
    <h3 class="text-muted">{{ $municipality->year !== '' ? $municipality->year : 'Year Not Specified' }}</h3>

    <table class="table table-striped">
        <tbody>
            <tr><th>Bulky Waste</th><td>{{ $municipality->bulky_waste !== '' ?  $municipality->bulky_waste : 'No data' }}</td></tr>
            <tr><th>Recycling</th><td>{{ $municipality->recycling !== '' ?  $municipality->recycling : 'No data' }}</td></tr>
            <tr><th>Tipping Fees</th><td>{{ $municipality->tipping_fees !== '' ?  $municipality->tipping_fees : 'No data' }}</td></tr>
            <tr><th>Admin Costs</th><td>{{ $municipality->admin_costs !== '' ?  $municipality->admin_costs : 'No data' }}</td></tr>
            <tr><th>Hazardous Waste</th><td>{{ $municipality->hazardous_waste !== '' ?  $municipality->hazardous_waste : 'No data' }}</td></tr>
            <tr><th>Contractual Services</th><td>{{ $municipality->contractual_services !== '' ?  $municipality->contractual_services : 'No data' }}</td></tr>
            <tr><th>Landfill Costs</th><td>{{ $municipality->landfill_costs !== '' ?  $municipality->landfill_costs : 'No data' }}</td></tr>
            <tr><th>Total Sanitation Refuse</th><td>{{ $municipality->total_sanitation_refuse !== '' ?  $municipality->total_sanitation_refuse : 'No data' }}</td></tr>
            <tr><th>Only Public Works</th><td>{{ $municipality->only_public_works !== '' ?  $municipality->only_public_works : 'No data' }}</td></tr>
            <tr><th>Transfer Station Wages</th><td>{{ $municipality->transfer_station_wages !== '' ?  $municipality->transfer_station_wages : 'No data' }}</td></tr>
            <tr><th>Hauling Fees</th><td>{{ $municipality->hauling_fees !== '' ?  $municipality->hauling_fees : 'No data' }}</td></tr>
            <tr><th>Curbside Pickup Fees</th><td>{{ $municipality->curbside_pickup_fees !== '' ?  $municipality->curbside_pickup_fees : 'No data' }}</td></tr>
            <tr><th>Waste Collection</th><td>{{ $municipality->waste_collection !== '' ?  $municipality->waste_collection : 'No data' }}</td></tr>
        </tbody>
    </table>

    <p><strong>Notes:</strong> {{ $municipality->notes !== '' ? $municipality->notes : 'None' }}</p>

    <a href="{{ route('municipalities.view', ['name' => $municipality->name]) }}" class="btn btn-secondary">Back to Overview</a>
    <a href="{{ route('municipalities.all') }}" class="btn btn-primary">Back to All Municipalities</a>
@endsection
