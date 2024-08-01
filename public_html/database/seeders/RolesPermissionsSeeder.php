<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleWisePermissions = [
            RoleEnum::SYSTEM => array_merge(
                [],
                // another array
            ),
            RoleEnum::ADMIN => array_merge(
                [],
                // another array
            ),
            RoleEnum::CHANNEL_OWNER => array_merge(
                [],
                PermissionEnum::CHANNEL_OWNER_PERMISSIONS,
            ),

            RoleEnum::VIEWER => array_merge(
                [],
                PermissionEnum::VIEWER_PERMISSIONS,
            ),
        ];

        foreach ($roleWisePermissions as $roleName => $permissions) {
            $roleModel = Role::where('name', $roleName)->first();

            if ($roleModel) {
                foreach ($permissions as $permission_name) {
                    $permission = Permission::where('name', $permission_name)->first();
                    if ($permission) {
                        if( !$roleModel->hasPermission($permission_name) ) {
                            $roleModel->attachPermission($permission_name);
                        }
                    }
                }
            }
        }

        $allUsers = User::all();
        foreach ($allUsers as $user) {
            if(!$user->hasRole(RoleEnum::VIEWER)){
               $user->attachRole(RoleEnum::VIEWER);
            }
        }

        // $superAdminUser = User::where('email', 'superadmin@admin.tmweb.local')->first();
        // if(!$superAdminUser->hasRole(RoleEnum::SUPER_ADMIN)){
        //     $superAdminUser->attachRole(RoleEnum::SUPER_ADMIN);
        // }

        // $gameAdminUser = User::where('email', 'gameadmin@admin.tmweb.local')->first();
        // if(!$gameAdminUser->hasRole(RoleEnum::GAME_ADMIN)){
        //     $gameAdminUser->attachRole(RoleEnum::GAME_ADMIN);
        // }
    }
}
