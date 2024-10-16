@extends('layouts.app')

@section('title', 'Push Notifications')

@include('search_snippet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Push Notifications</li>
@endsection

@section('content')
<div class="container">
    <h1>Push Notifications</h1>
    Push Notifications to send regularly.

    <form action="{{ route('push-notifications.toggle') }}" method="POST" class="mb-3">
        @csrf
        <label for="pushToggle" class="mr-2">Enable/Disable Push Notifications:</label>
        <input type="checkbox" id="pushToggle" name="push_enabled" class="toggle-switch" 
               onchange="this.form.submit()"
               @if($pushNotificationsEnabled) checked @endif>
    </form>
    <a href="{{ route('push-notifications.create') }}" class="btn-modern float-right mb-3">
        Create Push Notification
    </a>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Url</th>
                    <th>Total Sent</th>
                    <th>Total Received</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pushNotifications as $pushNotification)
                    <tr>
                        <td></td>
                        <td>
                            {{ $pushNotification->title }}
                        </td>
                        <td>
                            {{ $pushNotification->message }}
                        </td>
                        <td>
                            {{ $pushNotification->url }}
                        </td>
                        <td>
                            {{ $pushNotification->total_sent }}
                        </td>
                        <td>
                            {{ $pushNotification->total_received }}
                        </td>
                        <td>
                            <form action="{{ route('push-notifications.send', $pushNotification->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">Send</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $pushNotifications->links() }}
    </div>
</div>
@endsection
