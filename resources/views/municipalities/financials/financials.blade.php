@extends('layouts.app')

@section('title', 'Financial Data for ' . $financialData->municipality)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('municipalities.all') }}">Municipalities</a></li>
    <li class="breadcrumb-item"><a href="{{ route('municipalities.view', ['name' => $financialData->municipality]) }}">{{ $financialData->municipality }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Financial Infromation for {{ $financialData->time_period ?? 'Time Period Not Specified' }}</li>
@endsection

@section('content')
    <h1 class="text-primary">{{ $financialData->municipality }}</h1>
    <h3 class="text-muted">Financial Information for {{ $financialData->time_period == "" ? 'Time Period Not Specified' : $financialData->time_period }} <a style="text-decoration: none;" href="{{ route('municipalities.financials.edit', ['municipality' => $financialData->municipality]) }}" class="badge bg-danger badge-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
      </svg> Edit</a></h3>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <p style="margin-bottom: 0;"> Financial Information Successfully Updated</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <table class="table table-striped">
        <tbody>
            <tr><th>Time Period</th><td>{{ $financialData->time_period == "" ? 'No data' : $financialData->time_period }}</td></tr>
            <tr><th>Town Population (2022)</th><td>{{ $financialData->population == "" ? 'No data' : number_format($financialData->population) }}</td></tr>
            <tr><th>Town Size (Square Miles, 2010)</th><td>{{ $financialData->size == "" ? 'No data' : $financialData->size }}</td></tr>
            <tr><th>Link to Town Budget</th><td><a href="{{ $financialData->link == "" ? '#' : $financialData->link }}" target="_blank">View Town Budget</a></td></tr>
            <tr><th>Notes</th><td>{{ $financialData->notes == "" ? 'No additional notes' : $financialData->notes }}</td></tr>
        </tbody>
    </table>


@endsection
