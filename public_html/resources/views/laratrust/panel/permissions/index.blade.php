@extends('layouts.app')

@section('title', 'Permissions')

@include('search_snippet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Permissions</li>
@endsection

@section('content')
  <div class="container">
    @if (config('laratrust.panel.create_permissions'))
    <h1>Permissions</h1>
      <a href="{{ route('laratrust.permissions.create') }}" class="btn-modern float-right mb-3">
        + New Permission
      </a>
    @endif
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th class="th">Id</th>
            <th class="th">Name/Code</th>
            <th class="th">Display Name</th>
            <th class="th">Description</th>
            <th class="th"></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($permissions as $permission)
          <tr>
            <td class="td text-sm leading-5 text-gray-900">
              {{$permission->getKey()}}
            </td>
            <td class="td text-sm leading-5 text-gray-900">
              {{$permission->name}}
            </td>
            <td class="td text-sm leading-5 text-gray-900">
              {{$permission->display_name}}
            </td>
            <td class="td text-sm leading-5 text-gray-900">
              {{$permission->description}}
            </td>
            <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
              <a href="{{route('laratrust.permissions.edit', $permission->getKey())}}" data-toggle="tooltip" title="Edit">
                <i class="fas fa-edit" aria-hidden="true"></i>
                <span class="sr-only">Edit</span>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="d-flex justify-content-end">
        {{ $permissions->links('vendor.pagination.modern') }}
      </div>

  </div>
@endsection