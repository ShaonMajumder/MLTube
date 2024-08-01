<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (trans('roles') as $name => $displayName) {
            $role = Role::where('name', $name)->first();
            if (!$role) {
                Role::create([
                    'name' => $name,
                    'display_name' => $displayName
                ]);
            }
        }
    }
}
