<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $resourceNames = [
            'cities',
            'branches',
            'departments',
            'job_titles',
            'branch_departments',
            'branch_job_titles',
        ];

        foreach ($resourceNames as $resource) {
            Permission::firstOrCreate(['name' => "{$resource}.view_any", 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => "{$resource}.view", 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => "{$resource}.create", 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => "{$resource}.update", 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => "{$resource}.delete", 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => "{$resource}.delete_any", 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $adminRole->syncPermissions(Permission::where('guard_name', 'web')->get());
    }
}
