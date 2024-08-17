<?php

namespace App\Http\Controllers\Laratrust;

use App\Enums\User;
use Laratrust\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Laratrust\Http\Controllers\RolesAssignmentController as OrginalController;

class RolesAssignmentController extends OrginalController
{
    public function index(Request $request)
    {
        // $dashboardType = app('dashboard_suffix');

        $modelsKeys = array_keys(Config::get('laratrust.user_models'));
        $modelKey = $request->get('model') ?? $modelsKeys[0] ?? null;
        $userModel = Config::get('laratrust.user_models')[$modelKey] ?? null;

        if(isTMAdmin()){
            $userModel = $userModel::query()->where('type', User::TYPE['tm_admin']);
        } elseif(isGamingAdmin()){
            $userModel = $userModel::query()->where('type', User::TYPE['gaming_admin']);
        } else {
            $userModel = $userModel::query();
        }

        if (!$userModel) {
            abort(404);
        }

        return View::make('laratrust::panel.roles-assignment.index', [
            'models' => $modelsKeys,
            'modelKey' => $modelKey,
            'users' => $userModel->withCount(['roles', 'permissions'])
                ->paginate(10),
        ]);
    }

    public function edit(Request $request, $modelId)
    {
        $modelKey = $request->get('model');
        $userModel = Config::get('laratrust.user_models')[$modelKey] ?? null;

        if (!$userModel) {
            Session::flash('laratrust-error', 'Model was not specified in the request');
            return redirect(route('laratrust.roles-assignment.index'));
        }

        $user = $userModel::query()
            ->with(['roles:id,name', 'permissions:id,name'])
            ->findOrFail($modelId);

        $dashboardType = app('dashboard_suffix');
        if(isTMAdmin()){
            $roles = $this->rolesModel::where('name', 'not like', $dashboardType.'.%');
        } elseif(isGamingAdmin()){
            $roles = $this->rolesModel::where('name', 'like', $dashboardType.'.%');
        } else {
            $roles = $this->rolesModel;
        }

        $roles = $roles->orderBy('name')->get(['id', 'name', 'display_name'])
            ->map(function ($role) use ($user) {
                $role->assigned = $user->roles
                ->pluck('id')
                    ->contains($role->id);
                $role->isRemovable = Helper::roleIsRemovable($role);

                return $role;
            });
        if ($this->assignPermissions) {
            $permissions = $this->permissionModel::orderBy('name')
                ->get(['id', 'name', 'display_name'])
                ->map(function ($permission) use ($user) {
                    $permission->assigned = $user->permissions
                        ->pluck('id')
                        ->contains($permission->id);

                    return $permission;
                });
        }


        return View::make('laratrust::panel.roles-assignment.edit', [
            'modelKey' => $modelKey,
            'roles' => $roles,
            'permissions' => $this->assignPermissions ? $permissions : null,
            'user' => $user,
            'readonly' => $user->id == auth()->user()->id ? true:false,
        ]);
    }

    public function update(Request $request, $modelId)
    {
        $modelKey = $request->get('model');
        $userModel = Config::get('laratrust.user_models')[$modelKey] ?? null;

        if (!$userModel) {
            Session::flash('laratrust-error', 'Model was not specified in the request');
            return redirect()->back();
        }

        $user = $userModel::findOrFail($modelId);
        if( $user->id == auth()->user()->id ){
            Session::flash('laratrust-error', 'You can not update your own role');
            return redirect(route('laratrust.roles-assignment.index', ['model' => $modelKey]));
        }

        $user->syncRoles($request->get('roles') ?? []);
        if ($this->assignPermissions) {
            $user->syncPermissions($request->get('permissions') ?? []);
        }

        Session::flash('laratrust-success', 'Roles and permissions assigned successfully');
        return redirect(route('laratrust.roles-assignment.index', ['model' => $modelKey]));
    }
}
