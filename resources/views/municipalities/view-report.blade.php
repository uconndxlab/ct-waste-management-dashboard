@extends('layouts.app')

@section('title', $municipality->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('municipalities.all') }}">Municipalities</a></li>
    <li class="breadcrumb-item"><a href="{{ route('municipalities.view', ['name' => $municipality->name]) }}">{{ $municipality->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $municipality->year !== '' ? $municipality->year : 'Undated' }} Expenditure Report</li>
@endsection


@section('content')
    <h1 class="text-primary">{{ $municipality->name }}</h1>
    <h3 class="text-muted mb-4">Expenditure Report: {{ $municipality->year !== '' ? $municipality->year : 'Year Not Specified' }} <a style="text-decoration: none;" href="{{ route('municipalities.report.edit', ['id' => $municipality->id]) }}" class="badge bg-success badge-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
      </svg> Edit</a></h3>    
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <p style="margin-bottom: 0;"> Expenditure Report Successfully Updated</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <table class="table table-striped mb-4">
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

    <p class="mb-4"><strong>Notes:</strong> {{ $municipality->notes !== '' ? $municipality->notes : 'None' }}</p>

    <a href="{{ route('municipalities.view', ['name' => $municipality->name]) }}" class="btn btn-secondary">Back to Overview</a>
    <a href="{{ route('municipalities.all') }}" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
</svg> Back to All Municipalities</a>
    
    <form action="{{ route('municipalities.report.delete', ['name' => $municipality->name, 'reportId' => $municipality->id]) }}" method="POST" class="d-inline-block" style="float: right;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
          </svg> Delete Report</button>
    </form>

    <br/><br/>
@endsection