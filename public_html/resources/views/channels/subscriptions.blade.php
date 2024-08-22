@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Gaming Dashboard</li>
@endsection

@include('search_snippet')

@section('content')
<div class="container">
    <h1>Subscribers</h1>

    <!-- Table for Subscribers -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>User Profile</th>
                    <th>Subscribed At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptions as $subscription)
                    <tr>
                        <td>
                            <avatar-user :image="''" :href="'{{ route('user', ['user' => $subscription->subscriber->id]) }}'" :username="'{{ addslashes($subscription->subscriber->name) }}'" :size=35 :rounded=true ></avatar-user>
                        </td>
                        <td>
                            {{ $subscription->subscriber->name }}
                        </td>
                        <td>{{ $subscription->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $subscriptions->links() }}
    </div>
</div>
@endsection
