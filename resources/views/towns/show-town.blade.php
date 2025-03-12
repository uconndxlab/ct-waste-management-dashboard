@extends('layouts.app')

@section('content')
    <h1>{{ $town->name }}</h1>
    <p><strong>Description:</strong> {{ $town->description }}</p>
    <a href="{{ route('towns.index') }}">Back to towns</a>
@endsection
