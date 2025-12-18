<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $resourcePath = base_path('Modules/Core/app/Filament/Resources');
        $permissions = [];

        if (File::exists($resourcePath)) {
            foreach (File::files($resourcePath) as $file) {
                $className = pathinfo($file->getFilename(), PATHINFO_FILENAME);

                if (! str_ends_with($className, 'Resource')) {
                    continue;
                }

                $resourceClass = "Modules\\Core\\Filament\\Resources\\{$className}";

                if (! class_exists($resourceClass)) {
                    continue;
                }

                $resourceName = Str::of($className)
                    ->beforeLast('Resource')
                    ->snake()
                    ->plural();

                $permissions[] = "{$resourceName}.view_any";
                $permissions[] = "{$resourceName}.view";
                $permissions[] = "{$resourceName}.create";
                $permissions[] = "{$resourceName}.update";
                $permissions[] = "{$resourceName}.delete";
                $permissions[] = "{$resourceName}.delete_any";
            }
        }

        foreach (array_unique($permissions) as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $adminRole->syncPermissions(Permission::where('guard_name', 'web')->get());
    }
}
