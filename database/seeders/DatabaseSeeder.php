<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\TestUserSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminPermissionsSeeder::class,
            AdminUserSeeder::class,
            TestUserSeeder::class,
        ]);

    }
}
