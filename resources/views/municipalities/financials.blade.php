@extends('layouts.app')

@section('title', 'Financial Data for ' . $financialData->municipality)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('municipalities.all') }}">Municipalities</a></li>
    <li class="breadcrumb-item"><a href="{{ route('municipalities.view', ['name' => $financialData->municipality]) }}">{{ $financialData->municipality }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Financial Infromation for {{ $financialData->time_period ?? 'Time Period Not Specified' }}</li>
@endsection

@section('content')
    <h1 class="text-primary">{{ $financialData->municipality }}</h1>
    <h3 class="text-muted">Financial Information for {{ $financialData->time_period ?? 'Time Period Not Specified' }}</h3>

    <table class="table table-striped">
        <tbody>
            <tr><th>Time Period</th><td>{{ $financialData->time_period ?? 'No data' }}</td></tr>
            <tr><th>Town Population (2022)</th><td>{{ $financialData->population ? number_format($financialData->population) : 'No data' }}</td></tr>
            <tr><th>Town Size (Square Miles, 2010)</th><td>{{ $financialData->size ?? 'No data' }}</td></tr>
            <tr><th>Link to Town Budget</th><td><a href="{{ $financialData->link }}" target="_blank">View Town Budget</a></td></tr>
            <tr><th>Notes</th><td>{{ $financialData->notes ?? 'No additional notes' }}</td></tr>
        </tbody>
    </table>

    <a href="{{ route('municipalities.view', ['name' => $financialData->municipality]) }}" class="btn btn-secondary">Back to Municipality Overview</a>
    <a href="{{ route('municipalities.all') }}" class="btn btn-primary">Back to All Municipalities</a>
    <br/><br/>
@endsection
