@extends('layouts.app')

@section('title', 'Municipalities')

@section('content')
    <h1>All Municipalities</h1>
    <form action="{{ route('municipalities.all') }}" method="GET">
        <input type="text" name="search" placeholder="Search Municipality">
        <button type="submit">Search</button>
    </form>

    <ol>
        @foreach($municipalities as $municipality)
            <li>
                <a href="{{ route('municipalities.view', ['name' => $municipality->name]) }}">{{ $municipality->name }}</a>
            </li>
        @endforeach
    </ol>
@endsection
