@extends('layouts.app')

@section('title', 'Subscriptions')

@include('search_snippet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Subscriptions</li>
@endsection

@section('content')
<div class="container">
    <h1>Subscriptions</h1>
    You have been subscribed to these channels.
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
                @foreach($subscriptions as $channel)
                    <tr>
                        <td>
                            <avatar-user :image="''" :href="'{{ route(App\Enums\RouteEnum::CHANNELS_SHOW, ['channel' => $channel->id]) }}'" :username="'{{ addslashes($channel->name) }}'" :size=35 :rounded=true ></avatar-user>
                        </td>
                        <td>
                            {{ $channel->name }}
                        </td>
                        <td>{{ $channel->created_at->format('Y-m-d H:i:s') }}</td>
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
