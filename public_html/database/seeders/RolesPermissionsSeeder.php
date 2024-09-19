<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

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
                PermissionEnum::ROLE_MANAGEMENT_PERMISSIONS,
                PermissionEnum::ADMIN_MANAGE_PERMISSIONS
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
    }
}
