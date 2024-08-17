<?php

namespace App\Http\Controllers\Laratrust;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

use Laratrust\Http\Controllers\PermissionsController as OrginalController;

class PermissionsController extends OrginalController
{
    public function index()
    {
        $dashboardType = app('dashboard_suffix');
        if(isTMAdmin()){
            $permissions = $this->permissionModel::where('name', 'not like', $dashboardType.'.%');
        } elseif(isGamingAdmin()){
            $permissions = $this->permissionModel::where('name', 'like', $dashboardType.'.%');
        } else{
            $permissions = $this->permissionModel::query();
        }
        return View::make('laratrust::panel.permissions.index', [
            'permissions' => $permissions->orderBy('id', 'asc')->paginate(10)
        ]);
    }
}