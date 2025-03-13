@extends('layouts.app')

@section('title', $name)

@section('content')
    <h1>{{ $name }} Reports</h1>

    <ul>
        @foreach($reports as $report)
        <li>
            <a href="{{ route('municipalities.report', ['id' => $report->id]) }}">
                {{ $report->year !== '' ? 'Report for ' . $report->year : 'Report #' . $report->id . ': Year Not Specified' }}
            </a>
        </li>
        
        @endforeach
    </ul>

    <a href="{{ route('municipalities.all') }}">Back to All Municipalities</a>
@endsection
