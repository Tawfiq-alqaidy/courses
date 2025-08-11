<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('ar_SA');
        $studentRole = Role::where('name', 'student')->first();

        if (!$studentRole) {
            $this->command->error('Student role not found. Please run RoleSeeder first.');
            return;
        }

        // Create 20 student users
        $students = [];

        for ($i = 1; $i <= 20; $i++) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $name = $firstName . ' ' . $lastName;

            $students[] = [
                'name' => $name,
                'email' => 'student' . $i . '@example.com',
                'password' => bcrypt('password'),
                'role_id' => $studentRole->id,
                'phone' => '05' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'date_of_birth' => $faker->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d'),
                'gender' => $faker->randomElement(['male', 'female']),
                'address' => $faker->address,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach ($students as $studentData) {
            User::firstOrCreate(
                ['email' => $studentData['email']],
                $studentData
            );
        }

        $this->command->info('Created 20 student users successfully!');
    }
}
