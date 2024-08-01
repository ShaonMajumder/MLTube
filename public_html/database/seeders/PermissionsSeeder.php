<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //        echo "\033[31mTruncating permissions table.\n";
        //
        //        Permission::truncate();
        //
        //        echo "\033[32mTruncated permissions table.\n";
        
        $adminpermissions = [];
        $viewerPermissions = trans('permissions');
        $permissions = array_merge($adminpermissions, $viewerPermissions);

        foreach ($permissions as $name => $displayName) {
            $permission = Permission::where('name', $name)->first();
            if (!$permission) {
                Permission::create([
                    'name' => $name,
                    'display_name' => $displayName
                ]);
            }
        }
    }
}
