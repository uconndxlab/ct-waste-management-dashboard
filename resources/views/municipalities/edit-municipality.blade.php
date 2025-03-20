@extends('layouts.app')

@section('title', 'Edit ' . $name . ' Contacts')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('municipalities.all') }}">Municipalities</a></li>
    <li class="breadcrumb-item"><a href="{{ route('municipalities.view', $name) }}">{{ $name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Contacts</li>
@endsection

@section('content')
    <h1 class="text-primary">Edit {{ $name }} Contacts</h1>

    <form action="{{ route('municipalities.update', $name) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="department" class="form-label"><strong>Department</strong></label>
            <input type="text" class="form-control bg-body-secondary" id="department" name="department" value="{{ old('department', $townInfo->department) }}">
        </div>

        <h4 class="mt-4">Primary Contact</h4>
        <div class="mb-3">
            <label for="contact_1" class="form-label"><strong>Name</strong></label>
            <input type="text" class="form-control bg-body-secondary" id="contact_1" name="contact_1" value="{{ old('contact_1', $townInfo->contact_1) }}">
        </div>
        <div class="mb-3">
            <label for="title_1" class="form-label"><strong>Title</strong></label>
            <input type="text" class="form-control bg-body-secondary" id="title_1" name="title_1" value="{{ old('title_1', $townInfo->title_1) }}">
        </div>
        <div class="mb-3">
            <label for="phone_1" class="form-label"><strong>Phone</strong></label>
            <input type="text" class="form-control bg-body-secondary" id="phone_1" name="phone_1" value="{{ old('phone_1', $townInfo->phone_1) }}">
        </div>
        <div class="mb-3">
            <label for="email_1" class="form-label"><strong>Email</strong></label>
            <input type="email" class="form-control bg-body-secondary" id="email_1" name="email_1" value="{{ old('email_1', $townInfo->email_1) }}">
        </div>

        <h4 class="mt-4">Secondary Contact</h4>
        <div class="mb-3">
            <label for="contact_2" class="form-label"><strong>Name</strong></label>
            <input type="text" class="form-control bg-body-secondary" id="contact_2" name="contact_2" value="{{ old('contact_2', $townInfo->contact_2) }}">
        </div>
        <div class="mb-3">
            <label for="title_2" class="form-label"><strong>Title</strong></label>
            <input type="text" class="form-control bg-body-secondary" id="title_2" name="title_2" value="{{ old('title_2', $townInfo->title_2) }}">
        </div>
        <div class="mb-3">
            <label for="phone_2" class="form-label"><strong>Phone</strong></label>
            <input type="text" class="form-control bg-body-secondary" id="phone_2" name="phone_2" value="{{ old('phone_2', $townInfo->phone_2) }}">
        </div>
        <div class="mb-3">
            <label for="email_2" class="form-label"><strong>Email</strong></label>
            <input type="email" class="form-control bg-body-secondary" id="email_2" name="email_2" value="{{ old('email_2', $townInfo->email_2) }}">
        </div>

        <h4 class="mt-4">Additional Notes</h4>
        <div class="mb-3">
            <label for="notes" class="form-label"><strong>Notes</strong></label>
            <textarea class="form-control bg-body-secondary" id="notes" name="notes">{{ old('notes', $townInfo->notes) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="other_useful_notes" class="form-label"><strong>Other Useful Notes</strong></label>
            <textarea class="form-control bg-body-secondary" id="other_useful_notes" name="other_useful_notes">{{ old('other_useful_notes', $townInfo->other_useful_notes) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('municipalities.view', $name) }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
