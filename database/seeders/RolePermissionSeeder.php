<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sa = Role::create(['name' => 'superadmin']);
        $a = Role::create(['name' => 'admin']);
        $u = Role::create(['name' => 'User']);


        $saPermissions = [
            'dashboard',
            'user-page',
            'user-create',
            'user-edit',
            'user-delete',
            'role-page',
            'role-create',
            'role-edit',
            'role-delete',
            'permission-page',
            'permission-create',
            'permission-edit',
            'permission-delete',

            'appearance-edit',

            'category-page',
            'category-create',
            'category-edit',
            'category-delete',

            'wallet-page',
            'wallet-create',
            'wallet-edit',
            'wallet-delete',

            'transaction-page',
            'transaction-create',
            'transaction-edit',
            'transaction-delete',
        ];

        $aPermissions = [
            'dashboard',
            'user-page',
            'user-create',
            'user-edit',
        ];

        $uPermissions = [
            'dashboard',

            'category-page',
            'category-create',
            'category-edit',
            'category-delete',

            'wallet-page',
            'wallet-create',
            'wallet-edit',
            'wallet-delete',

            'transaction-page',
            'transaction-create',
            'transaction-edit',
            'transaction-delete',
        ];

        foreach ($saPermissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

       $sa->givePermissionTo($saPermissions);
       $a->givePermissionTo($aPermissions);
       $u->givePermissionTo($uPermissions);
    }
}
