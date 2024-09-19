@extends('layouts.app')

@section('title', 'Admin Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Admin Settings</li>
@endsection

@section('content')
<div class="container">
    <h1>Administrative Tools</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Clear All Section -->
    <div class="card mt-4">
        <div class="card-header">
            <h3>Clear All Data</h3>
        </div>
        <div class="card-body">
            <a href="{{ route(\App\Enums\RouteEnum::ADMIN_MANAGE_SITE_CLEAR_ALL) }}" class="btn-modern btn-danger mb-2">Clear All (Cache, Sessions, Cookies)</a>
            <div class="d-flex">
                <a href="{{ route(\App\Enums\RouteEnum::ADMIN_MANAGE_SITE_CLEAR_ALL_CACHES) }}" class="btn-modern btn-warning mr-2">Clear All Caches</a>
                <a href="{{ route(\App\Enums\RouteEnum::ADMIN_MANAGE_SITE_CLEAR_ALL_SESSIONS) }}" class="btn-modern btn-warning mr-2">Clear All Sessions</a>
                <a href="{{ route(\App\Enums\RouteEnum::ADMIN_MANAGE_SITE_CLEAR_ALL_COOKIES) }}" class="btn-modern btn-secondary">Clear All Cookies</a>
            </div>
        </div>
    </div>

    <!-- Clear Personal Data Section -->
    <div class="card mt-4">
        <div class="card-header">
            <h3>Clear Personal Data</h3>
        </div>
        <div class="card-body">
            <a href="{{ route(\App\Enums\RouteEnum::ADMIN_MANAGE_SITE_CLEAR_PERSONAL_SESSION) }}" class="btn-modern btn-info mb-2">Clear Personal Session</a>
            <div class="d-flex">
                <a href="{{ route(\App\Enums\RouteEnum::ADMIN_MANAGE_SITE_CLEAR_PERSONAL_COOKIES) }}" class="btn-modern btn-info mr-2">Clear Personal Cookies</a>
                <a href="{{ route(\App\Enums\RouteEnum::ADMIN_MANAGE_SITE_CLEAR_PERSONAL_CACHE) }}" class="btn-modern btn-info">Clear Personal Cache</a>
            </div>
        </div>
    </div>
</div>
@endsection
