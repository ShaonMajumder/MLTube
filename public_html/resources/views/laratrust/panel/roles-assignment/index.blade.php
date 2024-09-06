{{-- @extends('laratrust::panel.layout') --}}
@extends('layouts.app')

@section('title', 'Roles Assignment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Roles Assignment</li>
@endsection
@section('content')
  <div
    x-data="{ model: @if($modelKey) '{{$modelKey}}' @else 'initial' @endif }"
    x-init="$watch('model', value => value != 'initial' ? window.location = `?model=${value}` : '')"
    class="container"
  >
    <h1>Roles-Assignment</h1>
    <span>Assign Roles to Users</span> 
    </br></br>
    <span class="text-gray-700">User model to assign roles/permissions</span>
    <label class="block w-3/12">
      <select class="form-select block w-full mt-1 select2" x-model="model">
        <option value="initial" disabled selected>Select a user model</option>
        @foreach ($models as $model)
          <option value="{{$model}}">{{ucwords($model)}}</option>
        @endforeach
      </select>
    </label>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th class="th">Id</th>
            <th class="th">Name</th>
            <th class="th"># Roles</th>
            @if(config('laratrust.panel.assign_permissions_to_user'))<th class="th"># Permissions</th>@endif
            <th class="th"></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($users as $user)
          <tr>
            <td class="td text-sm leading-5 text-gray-900">
              {{$user->getKey()}}
            </td>
            <td class="td text-sm leading-5 text-gray-900">
              {{$user->name ?? 'The model doesn\'t have a `name` attribute'}}
            </td>
            <td class="td text-sm leading-5 text-gray-900">
              {{$user->roles_count}}
            </td>
            @if(config('laratrust.panel.assign_permissions_to_user'))
            <td class="td text-sm leading-5 text-gray-900">
              {{$user->permissions_count}}
            </td>
            @endif
            <td class="flex justify-end px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
              <a
                href="{{route('laratrust.roles-assignment.edit', ['roles_assignment' => $user->getKey(), 'model' => $modelKey])}}"
                data-toggle="tooltip" title="Edit"
              >
                <i class="fas fa-edit" aria-hidden="true"></i>
                <span class="sr-only">Edit</span>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="d-flex justify-content-end">
        @if ($modelKey)
          {{ $users->appends(['model' => $modelKey])->links('vendor.pagination.modern') }}
        @endif
      </div>
    </div>

  </div>
@endsection
