@extends('layouts.app')

@section('title', $name . ' Overview')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('municipalities.all') }}">Municipalities</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $name }}</li>
@endsection

@section('content')
    <h1 class="text-primary">{{ $name }} Overview</h1>

    @if($townInfo)
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Town Contact Information
        </div>
        <div class="card-body">
            @if(!empty($townInfo->department))
                <p><strong>Department:</strong> {{ $townInfo->department }}</p>
            @endif

            <h5>Primary Contact</h5>
            @if(!empty($townInfo->contact_1)) 
                <p><strong>Name:</strong> {{ $townInfo->contact_1 }}</p>
            @endif
            @if(!empty($townInfo->title_1)) 
                <p><strong>Title:</strong> {{ $townInfo->title_1 }}</p>
            @endif
            @if(!empty($townInfo->phone_1)) 
                <p><strong>Phone:</strong> {{ $townInfo->phone_1 }}</p>
            @endif
            @if(!empty($townInfo->email_1)) 
                <p><strong>Email:</strong> <a href="mailto:{{ $townInfo->email_1 }}">{{ $townInfo->email_1 }}</a></p>
            @endif

            @if(!empty($townInfo->contact_2) || !empty($townInfo->title_2) || !empty($townInfo->phone_2) || !empty($townInfo->email_2))
                <h5>Secondary Contact</h5>
                @if(!empty($townInfo->contact_2)) 
                    <p><strong>Name:</strong> {{ $townInfo->contact_2 }}</p>
                @endif
                @if(!empty($townInfo->title_2)) 
                    <p><strong>Title:</strong> {{ $townInfo->title_2 }}</p>
                @endif
                @if(!empty($townInfo->phone_2)) 
                    <p><strong>Phone:</strong> {{ $townInfo->phone_2 }}</p>
                @endif
                @if(!empty($townInfo->email_2)) 
                    <p><strong>Email:</strong> <a href="mailto:{{ $townInfo->email_2 }}">{{ $townInfo->email_2 }}</a></p>
                @endif
            @endif

            @if(!empty($townInfo->notes) || !empty($townInfo->other_useful_notes))
                <h5>Additional Notes</h5>
                @if(!empty($townInfo->notes)) 
                    <p>{{ $townInfo->notes }}</p>
                @endif
                @if(!empty($townInfo->other_useful_notes)) 
                    <p>{{ $townInfo->other_useful_notes }}</p>
                @endif
            @endif
        </div>
    </div>
@else
    <p class="text-muted">No contact information available.</p>
@endif

    <h2 class="text-secondary">Expenditure Reports</h2>
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
