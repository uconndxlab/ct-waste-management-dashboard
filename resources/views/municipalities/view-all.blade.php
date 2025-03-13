@extends('layouts.app')

@section('title', 'Municipalities')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Municipalities</li>
@endsection

@section('content')
    <h1 class="text-primary">All Municipalities</h1>
    
    <form action="{{ route('municipalities.all') }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search Municipality">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <div class="list-group">
        @foreach($municipalities as $municipality)
            <a href="{{ route('municipalities.view', ['name' => $municipality->name]) }}" class="list-group-item list-group-item-action">
                {{ $municipality->name }}
            </a>
        @endforeach
    </div>
@endsection
