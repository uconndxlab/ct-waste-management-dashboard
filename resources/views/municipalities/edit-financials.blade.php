@extends('layouts.app')

@section('title', 'Edit Financial Data for ' . $financialData->municipality)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('municipalities.all') }}">Municipalities</a></li>
    <li class="breadcrumb-item"><a href="{{ route('municipalities.view', ['name' => $financialData->municipality]) }}">{{ $financialData->municipality }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Financial Information</li>
@endsection

@section('content')
    <h1 class="text-primary">Edit Financial Data for {{ $financialData->municipality }}</h1>

    <form action="{{ route('municipalities.financials.update', ['municipality' => $financialData->municipality]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-2">
            <label><strong>Time Period</strong></label>
            <input type="text" name="time_period" class="form-control bg-body-secondary" value="{{ old('time_period', $financialData->time_period) }}">
        </div>

        <div class="form-group mb-2">
            <label><strong>Town Population (2022)</strong></label>
            <input type="number" name="population" class="form-control bg-body-secondary" value="{{ old('population', $financialData->population) }}">
        </div>

        <div class="form-group mb-2">
            <label><strong>Town Size (Square Miles, 2010)</strong></label>
            <input type="text" name="size" class="form-control bg-body-secondary" value="{{ old('size', $financialData->size) }}">
        </div>

        <div class="form-group mb-2">
            <label><strong>Link to Town Budget</strong></label>
            <input type="url" name="link" class="form-control bg-body-secondary" value="{{ old('link', $financialData->link) }}">
        </div>

        <div class="form-group mb-2">
            <label><strong>Notes</strong></label>
            <textarea name="notes" class="form-control bg-body-secondary">{{ old('notes', $financialData->notes) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update Financial Data</button>
        <a href="{{ route('municipalities.financials', ['municipality' => $financialData->municipality]) }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
