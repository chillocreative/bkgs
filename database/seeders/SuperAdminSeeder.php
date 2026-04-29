<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'super@school.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '60100000000',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (! $user->hasRole('super-admin')) {
            $user->assignRole('super-admin');
        }
    }
}
