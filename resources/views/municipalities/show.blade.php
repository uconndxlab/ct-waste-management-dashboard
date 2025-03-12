@extends('layouts.app')

@section('title', $municipality->name)

@section('content')
    <h1>{{ $municipality->name }}</h1>
    <p><strong>Year:</strong> {{ $municipality->year }}</p>
    <p><strong>Bulky Waste:</strong> ${{ number_format($municipality->bulky_waste, 2) }}</p>
    <p><strong>Recycling:</strong> ${{ number_format($municipality->recycling, 2) }}</p>
    <p><strong>Tipping Fees:</strong> ${{ number_format($municipality->tipping_fees, 2) }}</p>
    <p><strong>Admin Costs:</strong> ${{ number_format($municipality->admin_costs, 2) }}</p>
    <p><strong>Hazardous Waste:</strong> ${{ number_format($municipality->hazardous_waste, 2) }}</p>
    <p><strong>Contractual Services:</strong> ${{ number_format($municipality->contractual_services, 2) }}</p>
    <p><strong>Landfill Costs:</strong> ${{ number_format($municipality->landfill_costs, 2) }}</p>
    <p><strong>Total Sanitation Refuse:</strong> ${{ number_format($municipality->total_sanitation_refuse, 2) }}</p>
    <p><strong>Only Public Works:</strong> ${{ number_format($municipality->only_public_works, 2) }}</p>
    <p><strong>Transfer Station Wages:</strong> ${{ number_format($municipality->transfer_station_wages, 2) }}</p>
    <p><strong>Hauling Fees:</strong> ${{ number_format($municipality->hauling_fees, 2) }}</p>
    <p><strong>Curbside Pickup Fees:</strong> ${{ number_format($municipality->curbside_pickup_fees, 2) }}</p>
    <p><strong>Waste Collection:</strong> ${{ number_format($municipality->waste_collection, 2) }}</p>
    <p><strong>Notes:</strong> {{ $municipality->notes }}</p>

    <a href="/municipalities">Back to Municipalities</a>
@endsection
