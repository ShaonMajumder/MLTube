@extends('layouts.app')

@section('title', 'Create Push Notification')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route( \App\Enums\RouteEnum::ADMIN_MANAGE_SITE_PUSH_NOTIFICATION ) }}">Push Notifications</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create Push Notification</li>
@endsection

@section('content')
<div class="container">
    <h1>Create Push Notification</h1>
    
    <form action="{{ route('push-notifications.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Title Input -->
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required>
            @error('title')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Message Input -->
        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" class="form-control" rows="3">{{ old('message') }}</textarea>
            @error('message')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Status Checkbox -->
        {{-- <div class="form-group form-check">
            <input type="checkbox" id="status" name="status" class="form-check-input" checked>
            <label for="status" class="form-check-label">Active</label>
        </div> --}}

        <!-- Thumbnail (Desktop) Input -->
        {{-- <div class="form-group">
            <label for="thumbnail_desktop">Thumbnail (Desktop)</label>
            <input type="file" id="thumbnail_desktop" name="thumbnail_desktop" class="form-control-file">
            @error('thumbnail_desktop')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Thumbnail (Mobile) Input -->
        <div class="form-group">
            <label for="thumbnail_mobile">Thumbnail (Mobile)</label>
            <input type="file" id="thumbnail_mobile" name="thumbnail_mobile" class="form-control-file">
            @error('thumbnail_mobile')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div> --}}

        <!-- URL Input -->
        <div class="form-group">
            <label for="url">URL</label>
            <input type="url" id="url" name="url" class="form-control" value="{{ old('url') }}" required>
            @error('url')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- <!-- Activate At Input -->
        <div class="form-group">
            <label for="activate_at">Activate At</label>
            <input type="text" id="activate_at" name="activate_at" class="form-control date_time" value="{{ old('activate_at') }}">
            @error('activate_at')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Inactivate At Input -->
        <div class="form-group">
            <label for="inactivate_at">Inactivate At</label>
            <input type="text" id="inactivate_at" name="inactivate_at" class="form-control date_time" value="{{ old('inactivate_at') }}">
            @error('inactivate_at')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Schedule Time Input -->
        <div class="form-group">
            <label for="schedule_time">Schedule Time</label>
            <input type="text" id="schedule_time" name="schedule_time" class="form-control only_time" value="{{ old('schedule_time') }}">
            @error('schedule_time')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div> --}}

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Push Notification</button>
    </form>
</div>
@endsection