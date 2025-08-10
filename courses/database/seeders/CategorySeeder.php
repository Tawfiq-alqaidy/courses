<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'البرمجة'],
            ['name' => 'التصميم'],
            ['name' => 'التسويق'],
            ['name' => 'اللغات'],
            ['name' => 'إدارة الأعمال'],
            ['name' => 'التصوير'],
            ['name' => 'الطبخ'],
            ['name' => 'الرياضة'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
