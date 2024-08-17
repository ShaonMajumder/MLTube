<?php

namespace App\Http\Controllers\Laratrust;

use App\Enums\GameAdmin\GameAdminPermissions;
use App\Enums\Roles;
use Illuminate\Http\Request;
use Laratrust\Http\Controllers\RolesController as OrginalController;
use Laratrust\Helper;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class RolesController extends OrginalController
{
    private $dashboardType;
    public function __construct()
    {
        $this->dashboardType = app('dashboard_suffix') . '.';
        $this->rolesModel = Config::get('laratrust.models.role');
        $this->permissionModel = Config::get('laratrust.models.permission');
    }

    public function setDependencies(){
        dd(isGamingAdmin(),isTMAdmin());
    }

    public function edit($id)
    {
        $reservedRole = app('reserved_role');
        $reservedPermissionsForReservedRole = app('reserved_permissions_for_reserved_role');

        if(isTMAdmin()){
            $permissions = $this->permissionModel::where('name', 'not like', $this->dashboardType.'%');
        } elseif(isGamingAdmin()){
            $permissions = $this->permissionModel::where('name', 'like', $this->dashboardType.'%');
        } else {
            $permissions = $this->permissionModel;
        }
                                                
        $role = $this->rolesModel::query()
            ->with('permissions:id')
            ->findOrFail($id);

        if (!Helper::roleIsEditable($role)) {
            Session::flash('laratrust-error', 'The role is not editable');
            return redirect()->back();
        }

        $permissions = $permissions->get(['id', 'name', 'display_name'])
            ->map(function ($permission) use ($role) {
                $permission->assigned = $role->permissions
                    ->pluck('id')
                    ->contains($permission->id);

                return $permission;
            });

        return View::make('laratrust::panel.edit', [
            'model' => $role,
            'permissions' => $permissions,
            'type' => 'role',
            'reservedRole' => $reservedRole,
            'reservedPermissionsForReservedRole' => $reservedPermissionsForReservedRole
        ]);
    }

    public function create()
    {
        if(isTMAdmin()){
            $permissions = $this->permissionModel::where('name', 'not like', $this->dashboardType.'%');
        } elseif(isGamingAdmin()){
            $permissions = $this->permissionModel::where('name', 'like', $this->dashboardType.'%');
        } else {
            $permissions = $this->permissionModel;
        }

        return View::make('laratrust::panel.edit', [
            'model' => null,
            'permissions' => $permissions->get(['id', 'name', 'display_name']),
            'type' => 'role',
        ]);
    }

    public function index()
    {
        if(isTMAdmin()){
            $roles = $this->rolesModel::where('name', 'not like', $this->dashboardType.'%');
        } elseif(isGamingAdmin()){
            $roles = $this->rolesModel::where('name', 'like', $this->dashboardType.'%');
        } else {
            $roles = $this->rolesModel;
        }

        return View::make('laratrust::panel.roles.index', [
            'roles' => $roles->withCount('permissions')
                ->paginate(10),
        ]);
    }
    // check for update and delete

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $data['name'] = $this->dashboardType . $data['name'];

        $role = $this->rolesModel::create($data);
        $role->syncPermissions($request->get('permissions') ?? []);

        Session::flash('laratrust-success', 'Role created successfully');
        return redirect(route('laratrust.roles.index'));
    }

    public function update(Request $request, $id)
    {
        $reservedPermissionsForReservedRole = app('reserved_permissions_for_reserved_role');
        
        $role = $this->rolesModel::findOrFail($id);
        if($role->name == Roles::GAME_ADMIN){
            $reservedIds = $this->permissionModel::where(function($q) use ($reservedPermissionsForReservedRole){
                foreach($reservedPermissionsForReservedRole as $name){
                    $q->orWhere('name',$name);
                }
            })->pluck('id')
            ->toArray();
        } else {
            $reservedIds = [];
        }
        
        $permissionsToSync = array_merge($request->get('permissions'), $reservedIds);

        if (!Helper::roleIsEditable($role)) {
            Session::flash('laratrust-error', 'The role is not editable');
            return redirect()->back();
        }

        $data = $request->validate([
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $role->update($data);
        $role->syncPermissions($permissionsToSync ?? []);

        Session::flash('laratrust-success', 'Role updated successfully');
        return redirect(route('laratrust.roles.index'));
    }
}
