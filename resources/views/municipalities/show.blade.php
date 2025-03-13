@extends('layouts.app')

@section('title', $municipality->name)

@section('content')
    <p><strong>Bulky Waste:</strong> {{ $municipality->bulky_waste !== '' ? '$' . $municipality->bulky_waste : 'No data' }}</p>
    <p><strong>Recycling:</strong> {{ $municipality->recycling !== '' ? '$' . $municipality->recycling : 'No data' }}</p>
    <p><strong>Tipping Fees:</strong> {{ $municipality->tipping_fees !== '' ? '$' . $municipality->tipping_fees : 'No data' }}</p>
    <p><strong>Admin Costs:</strong> {{ $municipality->admin_costs !== '' ? '$' . $municipality->admin_costs : 'No data' }}</p>
    <p><strong>Hazardous Waste:</strong> {{ $municipality->hazardous_waste !== '' ? '$' . $municipality->hazardous_waste : 'No data' }}</p>
    <p><strong>Contractual Services:</strong> {{ $municipality->contractual_services !== '' ? '$' . $municipality->contractual_services : 'No data' }}</p>
    <p><strong>Landfill Costs:</strong> {{ $municipality->landfill_costs !== '' ? '$' . $municipality->landfill_costs : 'No data' }}</p>
    <p><strong>Total Sanitation Refuse:</strong> {{ $municipality->total_sanitation_refuse !== '' ? '$' . $municipality->total_sanitation_refuse : 'No data' }}</p>
    <p><strong>Only Public Works:</strong> {{ $municipality->only_public_works !== '' ? '$' . $municipality->only_public_works : 'No data' }}</p>
    <p><strong>Transfer Station Wages:</strong> {{ $municipality->transfer_station_wages !== '' ? '$' . $municipality->transfer_station_wages : 'No data' }}</p>
    <p><strong>Hauling Fees:</strong> {{ $municipality->hauling_fees !== '' ? '$' . $municipality->hauling_fees : 'No data' }}</p>
    <p><strong>Curbside Pickup Fees:</strong> {{ $municipality->curbside_pickup_fees !== '' ? '$' . $municipality->curbside_pickup_fees : 'No data' }}</p>
    <p><strong>Waste Collection:</strong> {{ $municipality->waste_collection !== '' ? '$' . $municipality->waste_collection : 'No data' }}</p>

    <p><strong>Notes:</strong> {{ $municipality->notes !== '' ? $municipality->notes : 'None' }}</p>

    <a href="/municipalities">Back to Municipalities</a>
@endsection
