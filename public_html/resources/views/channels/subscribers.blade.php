@extends('layouts.app')

@section('title', 'Subscribers')

@include('search_snippet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Subscribers</li>
@endsection

@section('content')
<div class="container">
    <h1>Subscribers</h1>
    These users subscribed you.
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
                @foreach($subscribers as $subscriber)
                    <tr>
                        <td>
                            <avatar-user :image="''" :href="'{{ route(App\Enums\RouteEnum::USERS_SHOW, ['user' => $subscriber->id]) }}'" :username="'{{ addslashes($subscriber->name) }}'" :size=35 :rounded=true ></avatar-user>
                        </td>
                        <td>
                            {{ $subscriber->name }}
                        </td>
                        <td>{{ $subscriber->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $subscribers->links() }}
    </div>
</div>
@endsection
