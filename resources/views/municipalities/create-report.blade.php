@extends('layouts.app')

@section('title', 'Add New Report for ' . $name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('municipalities.all') }}">Municipalities</a></li>
    <li class="breadcrumb-item"><a href="{{ route('municipalities.view', ['name' => $name]) }}">{{ $name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">New Expenditure Report</li>
@endsection

@section('content')

<h1 class="text-primary">Add New Report for {{ $name }}</h1>

<form action="{{ route('municipalities.report.store', ['name' => $name]) }}" method="POST">
    @csrf

    <div class="form-group mb-3">
        <label for="year"><strong>Year</strong></label>
        <input type="number" name="year" id="year" class="form-control bg-body-secondary" required>
    </div>

    <div class="form-group mb-3">
        <label for="bulky_waste"><strong>Bulky Waste</strong></label>
        <input type="text" name="bulky_waste" id="bulky_waste" class="form-control bg-body-secondary">
    </div>

    <div class="form-group mb-3">
        <label for="recycling"><strong>Recycling</strong></label>
        <input type="text" name="recycling" id="recycling" class="form-control bg-body-secondary">
    </div>

    <div class="form-group mb-3">
        <label for="tipping_fees"><strong>Tipping Fees</strong></label>
        <input type="text" name="tipping_fees" id="tipping_fees" class="form-control bg-body-secondary">
    </div>

    <div class="form-group mb-3">
        <label for="admin_costs"><strong>Admin Costs</strong></label>
        <input type="text" name="admin_costs" id="admin_costs" class="form-control bg-body-secondary">
    </div>

    <div class="form-group mb-3">
        <label for="hazardous_waste"><strong>Hazardous Waste</strong></label>
        <input type="text" name="hazardous_waste" id="hazardous_waste" class="form-control bg-body-secondary">
    </div>

    <div class="form-group mb-3">
        <label for="contractual_services"><strong>Contractual Services</strong></label>
        <input type="text" name="contractual_services" id="contractual_services" class="form-control bg-body-secondary">
    </div>

    <div class="form-group mb-3">
        <label for="landfill_costs"><strong>Landfill Costs</strong></label>
        <input type="text" name="landfill_costs" id="landfill_costs" class="form-control bg-body-secondary">
    </div>

    <div class="form-group mb-3">
        <label for="total_sanitation_refuse"><strong>Total Sanitation Refuse</strong></label>
        <input type="text" name="total_sanitation_refuse" id="total_sanitation_refuse" class="form-control bg-body-secondary">
    </div>

    <div class="form-group mb-3">
        <label for="only_public_works"><strong>Only Public Works</strong></label>
        <input type="text" name="only_public_works" id="only_public_works" class="form-control bg-body-secondary">
    </div>

    <div class="form-group mb-3">
        <label for="transfer_station_wages"><strong>Transfer Station Wages</strong></label>
        <input type="text" name="transfer_station_wages" id="transfer_station_wages" class="form-control bg-body-secondary">
    </div>

    <div class="form-group mb-3">
        <label for="hauling_fees"><strong>Hauling Fees</strong></label>
        <input type="text" name="hauling_fees" id="hauling_fees" class="form-control bg-body-secondary">
    </div>

    <div class="form-group mb-3">
        <label for="curbside_pickup_fees"><strong>Curbside Pickup Fees</strong></label>
        <input type="text" name="curbside_pickup_fees" id="curbside_pickup_fees" class="form-control bg-body-secondary">
    </div>

    <div class="form-group mb-3">
        <label for="waste_collection"><strong>Waste Collection</strong></label>
        <input type="text" name="waste_collection" id="waste_collection" class="form-control bg-body-secondary">
    </div>

    <div class="form-group mb-3">
        <label for="notes"><strong>Notes</strong></label>
        <textarea name="notes" id="notes" class="form-control bg-body-secondary"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Add Report</button>
    <a href="{{ route('municipalities.view', ['name' => $name]) }}" class="btn btn-secondary">Cancel</a>
</form>

@endsection
