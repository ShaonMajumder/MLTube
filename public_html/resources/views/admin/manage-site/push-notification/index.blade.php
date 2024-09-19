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
    <!-- Table for Subscribers -->
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
