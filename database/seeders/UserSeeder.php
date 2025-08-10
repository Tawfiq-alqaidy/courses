<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $studentRole = Role::where('name', 'student')->first();
        $instructorRole = Role::where('name', 'instructor')->first();

        // Create admin user
        User::firstOrCreate([
            'email' => 'admin@courses.com'
        ], [
            'name' => 'مدير النظام',
            'email' => 'admin@courses.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
            'is_active' => true
        ]);

        // Create sample instructor
        User::firstOrCreate([
            'email' => 'instructor@courses.com'
        ], [
            'name' => 'أحمد محمد',
            'email' => 'instructor@courses.com',
            'password' => Hash::make('password'),
            'role_id' => $instructorRole->id,
            'email_verified_at' => now(),
            'is_active' => true,
            'phone' => '+966501234567'
        ]);

        // Create sample student
        User::firstOrCreate([
            'email' => 'student@courses.com'
        ], [
            'name' => 'سارة علي',
            'email' => 'student@courses.com',
            'password' => Hash::make('password'),
            'role_id' => $studentRole->id,
            'email_verified_at' => now(),
            'is_active' => true,
            'phone' => '+966507654321',
            'gender' => 'female'
        ]);
    }
}
