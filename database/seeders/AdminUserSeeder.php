<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء Role Admin لو مش موجود
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // إنشاء Admin User لو مش موجود
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('Admin@123'),
            ]
        );

        // ربط الدور بالمستخدم
        if (! $adminUser->hasRole($adminRole)) {
            $adminUser->assignRole($adminRole);
        }
    }
}
