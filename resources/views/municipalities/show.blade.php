@extends('layouts.app')

@section('title', $municipality->name)

@section('content')
    <h1>{{ $municipality->name }}</h1>
    <p><strong>Year:</strong> {{ $municipality->year }}</p>
    <p><strong>Bulky Waste:</strong> ${{ number_format($municipality->bulky_waste, 2) }}</p>
    <p><strong>Recycling:</strong> ${{ number_format($municipality->recycling, 2) }}</p>
    <p><strong>Tipping Fees:</strong> ${{ number_format($municipality->tipping_fees, 2) }}</p>
    <p><strong>Notes:</strong> {{ $municipality->notes }}</p>

    <a href="/municipalities">Back to Municipalities</a>
@endsection
