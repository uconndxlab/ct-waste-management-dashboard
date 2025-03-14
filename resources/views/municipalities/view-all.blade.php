@extends('layouts.app')

@section('title', 'Municipalities')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Municipalities</li>
@endsection

@section('content')
    <h1 class="text-primary">All Municipalities</h1>

    <form action="{{ route('municipalities.all') }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search Municipality" value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
        </div>
    </form>

    <div class="mb-3">
        <strong>Search by Letter: </strong>
        <a href="{{ route('municipalities.all') }}" 
           class="btn btn-sm {{ !$selectedLetter ? 'btn-primary' : 'btn-outline-primary' }}">
            All
        </a>
        @foreach($letters as $letter)
            <a href="{{ route('municipalities.all', ['letter' => $letter]) }}" 
               class="btn btn-sm {{ $selectedLetter == $letter ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $letter }}
            </a>
        @endforeach
    </div>

    <div class="list-group">
        @foreach($municipalities as $municipality)
            <a href="{{ route('municipalities.view', ['name' => $municipality->name]) }}" class="list-group-item list-group-item-action">
                {{ $municipality->name }}
            </a>
        @endforeach
        <br/>
    </div>

    @if(request('search'))
    <br/>
    <a href="{{ route('municipalities.all') }}" class="btn btn-secondary mb-3">View All Municipalities</a>
    @endif

@endsection
