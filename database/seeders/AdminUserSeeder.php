<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'admin@palgoals.com'],
            [
                'name' => 'Palgoals Admin',
                'password' => 'password',
            ],
        );

        $user->assignRole('super_admin');
    }
}
