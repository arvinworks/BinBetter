<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        // Admin user
        User::create([
            'username' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'superadmin',
        ]);

        User::create([
            'username' => 'LGU User',
            'email' => 'lgu@example.com',
            'password' => Hash::make('password123'),
            'role' => 'LGU'
        ]);

        User::create([
            'username' => 'NGO User',
            'email' => 'ngo@example.com',
            'password' => Hash::make('password123'),
            'role' => 'NGO'
        ]);

        User::create([
            'username' => 'Resident User',
            'email' => 'resident@example.com',
            'password' => Hash::make('password123'),
            'role' => 'Resident',
        ]);
    }
}
