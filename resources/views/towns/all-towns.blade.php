@extends('layouts.app')

@section('title', 'Towns')

@section('content')
    <h1>All Towns</h1>
    <ol>
        @foreach ($towns as $town)
            <li><a href="{{ route('towns.show', $town->id) }}">{{ $town->name }}</a></li>
        @endforeach
    </ol>
@endsection
