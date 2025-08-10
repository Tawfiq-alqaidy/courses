<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'مدير النظام',
                'description' => 'مدير النظام له صلاحيات كاملة'
            ],
            [
                'name' => 'student',
                'display_name' => 'طالب',
                'description' => 'الطالب يمكنه التسجيل في الدورات'
            ],
            [
                'name' => 'instructor',
                'display_name' => 'مدرب',
                'description' => 'المدرب يمكنه إنشاء وإدارة الدورات'
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
