@extends('layouts.app')

@section('title', 'Municipalities')

@section('content')
    <h1>All Municipalities</h1>
    <ol>
        @foreach ($municipalities as $municipality)
            <li>
                <a href="{{ route('municipalities.show', $municipality->id) }}">{{ $municipality->name }}</a>
            </li>
        @endforeach
    </ol>
@endsection