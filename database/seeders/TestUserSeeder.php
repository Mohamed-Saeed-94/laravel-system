<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء Test User لو مش موجود
        $testUser = User::firstOrCreate(
            ['email' => 'test@test.com'],
            [
                'name' => 'Test',
                'password' => bcrypt('Test@123'),
            ]
        );
    }
}
