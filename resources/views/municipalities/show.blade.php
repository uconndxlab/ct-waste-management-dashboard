@extends('layouts.app')

@section('title', $municipality->name)

@section('content')
    <p><strong>Bulky Waste:</strong> $ {{ $municipality->bulky_waste }}</p>
    <p><strong>Recycling:</strong> ${{ $municipality->recycling }}</p>
    <p><strong>Tipping Fees:</strong> ${{ $municipality->tipping_fees }}</p>
    <p><strong>Admin Costs:</strong> ${{ $municipality->admin_costs }}</p>
    <p><strong>Hazardous Waste:</strong> ${{ $municipality->hazardous_waste }}</p>
    <p><strong>Contractual Services:</strong> ${{ $municipality->contractual_services }}</p>
    <p><strong>Landfill Costs:</strong> ${{ $municipality->landfill_costs }}</p>
    <p><strong>Total Sanitation Refuse:</strong> ${{ $municipality->total_sanitation_refuse }}</p>
    <p><strong>Only Public Works:</strong> ${{ $municipality->only_public_works }}</p>
    <p><strong>Transfer Station Wages:</strong> ${{ $municipality->transfer_station_wages }}</p>
    <p><strong>Hauling Fees:</strong> ${{ $municipality->hauling_fees }}</p>
    <p><strong>Curbside Pickup Fees:</strong> ${{ $municipality->curbside_pickup_fees }}</p>
    <p><strong>Waste Collection:</strong> ${{ $municipality->waste_collection }}</p>


    <a href="/municipalities">Back to Municipalities</a>
@endsection
