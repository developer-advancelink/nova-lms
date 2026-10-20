@extends('layouts.app')

@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
    {{-- Profile details and password live on one page: both are short, both
         are "my account", and splitting them meant two navigations to change
         two things. The two forms post to separate endpoints and have
         separate ids, so they stay independent. --}}
    <div class="space-y-6">
        @include('pages.shared.profile')
        @include('pages.shared.password')
    </div>
@endsection
