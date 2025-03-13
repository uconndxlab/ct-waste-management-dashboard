@extends('layouts.app')

@section('title', $name . ' Reports')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('municipalities.all') }}">Municipalities</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $name }}</li>
@endsection

@section('content')
    <h1 class="text-primary">{{ $name }} Reports</h1>

    @if($reports->isEmpty())
        <p class="text-muted">No reports available.</p>
    @else
        <div class="list-group mb-4">
            @foreach($reports as $report)
                <a href="{{ route('municipalities.report', ['id' => $report->id]) }}" class="list-group-item list-group-item-action">
                    {{ $report->year !== '' ? 'Report for ' . $report->year : 'Report #' . $report->id . ': Year Not Specified' }}
                </a>
            @endforeach
        </div>
    @endif

    <a href="{{ route('municipalities.all') }}" class="btn btn-secondary">Back to All Municipalities</a>
@endsection
