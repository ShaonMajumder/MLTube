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
        return View::make('laratrust::panel.permissions.index', [
            'permissions' => $this->permissionModel::paginate(10),
        ]);
    }
}