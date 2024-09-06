{{-- @extends('laratrust::panel.layout') --}}
@extends('layouts.app')

@section('title', 'Roles')

@include('search_snippet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Roles</li>
@endsection

@section('content')
  <div class="container">
    <h1>Roles</h1>
    <a
      href="{{route('laratrust.roles.create')}}"
      class="btn-modern float-right mb-3"
    >
      + New Role
    </a>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th class="th">Id</th>
            <th class="th">Display Name</th>
            <th class="th">Name</th>
            <th class="th"># Permissions</th>
            <th class="th"></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($roles as $role)
          <tr>
            <td class="td text-sm leading-5 text-gray-900">
              {{$role->getKey()}}
            </td>
            <td class="td text-sm leading-5 text-gray-900">
              {{$role->display_name}}
            </td>
            <td class="td text-sm leading-5 text-gray-900">
              {{$role->name}}
            </td>
            <td class="td text-sm leading-5 text-gray-900">
              {{$role->permissions_count}}
            </td>
            <td class="flex justify-end px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">


              @if (\Laratrust\Helper::roleIsEditable($role))
                <a href="{{ route('laratrust.roles.edit', $role->getKey()) }}" data-toggle="tooltip" title="Edit">
                  <i class="fas fa-edit" aria-hidden="true"></i>
                  <span class="sr-only">Edit</span>
                </a>
              @else
                <a href="{{ route('laratrust.roles.show', $role->getKey()) }}" data-toggle="tooltip" title="Details">
                  <i class="fas fa-info-circle" aria-hidden="true"></i>
                  <span class="sr-only">Details</span>
                </a>
              @endif

              <a
                href="#"
                onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this role?')) { document.getElementById('delete-form-{{ $role->getKey() }}').submit(); }"
                class="{{ \Laratrust\Helper::roleIsDeletable($role) ? 'text-red-600 hover:text-red-900' : 'text-gray-600 hover:text-gray-700 cursor-not-allowed' }} ml-2"
                @if (!\Laratrust\Helper::roleIsDeletable($role)) aria-disabled="true" @endif
                data-toggle="tooltip" title="Delete"
              >
                <i class="fas fa-trash-alt" aria-hidden="true"></i>
                <span class="sr-only">Delete</span>
              </a>

              <form 
                id="delete-form-{{ $role->getKey() }}" 
                action="{{ route('laratrust.roles.destroy', $role->getKey()) }}" 
                method="POST" 
                class="hidden"
              >
                @method('DELETE')
                @csrf
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="d-flex justify-content-end">
        {{ $roles->links('vendor.pagination.modern') }}
      </div>
    </div>
  </div>
  
@endsection
