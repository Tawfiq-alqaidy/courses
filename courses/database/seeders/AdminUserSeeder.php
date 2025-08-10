<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin'], [
            'name' => 'admin',
            'display_name' => 'مدير النظام',
            'description' => 'مدير النظام له صلاحيات كاملة'
        ]);
        
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@courses.com'],
            [
                'name' => 'مدير النظام',
                'email' => 'admin@courses.com',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );

        // Create another admin for demo
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );
    }
}
