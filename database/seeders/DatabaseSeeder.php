<?php

namespace Database\Seeders;

use App\Models\User;
use App\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Administrátor',
            'email' => 'admin@faktury.sk',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@faktury.sk',
            'password' => Hash::make('password'),
            'role' => UserRole::User,
        ]);
    }
}
