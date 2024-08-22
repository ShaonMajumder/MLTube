@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">User Details</li>
@endsection

@section('content')
<div class="container">
    <h1>User Details</h1>

    <div class="card">
        <div class="card-header">
            User Information
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $user->name }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $user->email }}</dd>

                <dt class="col-sm-3">Account Type</dt>
                <dd class="col-sm-9">{{ $user->account_type ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">{{ $user->status ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $user->created_at->format('Y-m-d H:i:s') }}</dd>

                <dt class="col-sm-3">Updated At</dt>
                <dd class="col-sm-9">{{ $user->updated_at->format('Y-m-d H:i:s') }}</dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ url('/users') }}" class="btn btn-primary">Back to User List</a>
        </div>
    </div>
</div>
@endsection
