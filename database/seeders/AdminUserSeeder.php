<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin role
        $adminRole = Role::where('slug', 'admin')->first();

        if ($adminRole) {
            User::firstOrCreate(
                ['email' => env('ADMIN_EMAIL', 'admin@ebs.com')],
                [
                    'name' => env('ADMIN_NAME', 'Admin User'),
                    'password' => Hash::make(env('ADMIN_PASSWORD', '!Admin123')),
                    'role_id' => $adminRole->id,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
