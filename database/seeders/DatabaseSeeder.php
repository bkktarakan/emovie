<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@emovie.go.id',
                'password' => Hash::make('Admin@2024!'),
                'level' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['username' => 'operator'],
            [
                'name' => 'Operator',
                'username' => 'operator',
                'email' => 'operator@emovie.go.id',
                'password' => Hash::make('Operator@2024!'),
                'level' => 'operator',
            ]
        );
    }
}
