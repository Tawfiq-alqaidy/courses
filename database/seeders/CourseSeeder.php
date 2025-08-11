<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories and instructors
        $categories = Category::all();
        $instructorRole = \App\Models\Role::where('name', 'instructor')->first();
        $adminRole = \App\Models\Role::where('name', 'admin')->first();

        $instructors = User::whereIn('role_id', [$instructorRole->id ?? 0, $adminRole->id ?? 0])->get();

        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Please run CategorySeeder first.');
            return;
        }

        if ($instructors->isEmpty()) {
            $this->command->error('No instructors found. Please ensure users with instructor or admin role exist.');
            return;
        }

        $courses = [
            // Programming Courses
            [
                'title' => 'أساسيات البرمجة بـ PHP',
                'description' => 'تعلم أساسيات البرمجة باستخدام لغة PHP من الصفر حتى الاحتراف',
                'category_name' => 'البرمجة',
                'duration' => 40,
                'capacity_limit' => 25,
                'price' => 299.99,
                'status' => 'active',
                'image' => 'courses/php-basics.jpg'
            ],
            [
                'title' => 'تطوير المواقع بـ Laravel',
                'description' => 'دورة شاملة لتعلم إطار العمل Laravel لتطوير التطبيقات الحديثة',
                'category_name' => 'البرمجة',
                'duration' => 60,
                'capacity_limit' => 20,
                'price' => 499.99,
                'status' => 'active',
                'image' => 'courses/laravel-development.jpg'
            ],
            [
                'title' => 'JavaScript المتقدم',
                'description' => 'تعلم JavaScript المتقدم وتطوير التطبيقات التفاعلية',
                'category_name' => 'البرمجة',
                'duration' => 45,
                'capacity_limit' => 30,
                'price' => 399.99,
                'status' => 'active',
                'image' => 'courses/javascript-advanced.jpg'
            ],
            [
                'title' => 'تطوير تطبيقات الهاتف المحمول',
                'description' => 'تعلم تطوير تطبيقات الهاتف المحمول باستخدام React Native',
                'category_name' => 'البرمجة',
                'duration' => 50,
                'capacity_limit' => 15,
                'price' => 699.99,
                'status' => 'active',
                'image' => 'courses/mobile-development.jpg'
            ],

            // Design Courses
            [
                'title' => 'أساسيات التصميم الجرافيكي',
                'description' => 'تعلم أساسيات التصميم الجرافيكي باستخدام Adobe Photoshop و Illustrator',
                'category_name' => 'التصميم',
                'duration' => 35,
                'capacity_limit' => 20,
                'price' => 349.99,
                'status' => 'active',
                'image' => 'courses/graphic-design.jpg'
            ],
            [
                'title' => 'تصميم واجهات المستخدم (UI/UX)',
                'description' => 'دورة شاملة لتعلم تصميم واجهات المستخدم وتجربة المستخدم',
                'category_name' => 'التصميم',
                'duration' => 40,
                'capacity_limit' => 18,
                'price' => 449.99,
                'status' => 'active',
                'image' => 'courses/ui-ux-design.jpg'
            ],
            [
                'title' => 'التصميم ثلاثي الأبعاد',
                'description' => 'تعلم التصميم ثلاثي الأبعاد باستخدام Blender',
                'category_name' => 'التصميم',
                'duration' => 55,
                'capacity_limit' => 12,
                'price' => 599.99,
                'status' => 'active',
                'image' => 'courses/3d-design.jpg'
            ],

            // Marketing Courses
            [
                'title' => 'التسويق الرقمي المتكامل',
                'description' => 'تعلم استراتيجيات التسويق الرقمي الحديثة ووسائل التواصل الاجتماعي',
                'category_name' => 'التسويق',
                'duration' => 30,
                'capacity_limit' => 35,
                'price' => 249.99,
                'status' => 'active',
                'image' => 'courses/digital-marketing.jpg'
            ],
            [
                'title' => 'إعلانات فيسبوك وجوجل',
                'description' => 'دورة متخصصة في إدارة حملات الإعلانات على فيسبوك وجوجل',
                'category_name' => 'التسويق',
                'duration' => 25,
                'capacity_limit' => 30,
                'price' => 199.99,
                'status' => 'active',
                'image' => 'courses/facebook-google-ads.jpg'
            ],
            [
                'title' => 'التسويق بالمحتوى',
                'description' => 'تعلم كيفية إنشاء وتسويق المحتوى بطريقة فعالة',
                'category_name' => 'التسويق',
                'duration' => 20,
                'capacity_limit' => 40,
                'price' => 149.99,
                'status' => 'active',
                'image' => 'courses/content-marketing.jpg'
            ],

            // Language Courses
            [
                'title' => 'اللغة الإنجليزية للمبتدئين',
                'description' => 'دورة تأسيسية لتعلم اللغة الإنجليزية من البداية',
                'category_name' => 'اللغات',
                'duration' => 60,
                'capacity_limit' => 25,
                'price' => 199.99,
                'status' => 'active',
                'image' => 'courses/english-beginners.jpg'
            ],
            [
                'title' => 'الإنجليزية للأعمال',
                'description' => 'تعلم اللغة الإنجليزية المتخصصة في بيئة العمل',
                'category_name' => 'اللغات',
                'duration' => 40,
                'capacity_limit' => 20,
                'price' => 299.99,
                'status' => 'active',
                'image' => 'courses/business-english.jpg'
            ],
            [
                'title' => 'اللغة الفرنسية الأساسية',
                'description' => 'تعلم أساسيات اللغة الفرنسية للمحادثة اليومية',
                'category_name' => 'اللغات',
                'duration' => 45,
                'capacity_limit' => 18,
                'price' => 249.99,
                'status' => 'active',
                'image' => 'courses/french-basics.jpg'
            ],

            // Business Management Courses
            [
                'title' => 'إدارة المشاريع الحديثة',
                'description' => 'تعلم أساليب إدارة المشاريع الحديثة والأدوات المطلوبة',
                'category_name' => 'إدارة الأعمال',
                'duration' => 35,
                'capacity_limit' => 25,
                'price' => 349.99,
                'status' => 'active',
                'image' => 'courses/project-management.jpg'
            ],
            [
                'title' => 'ريادة الأعمال والابتكار',
                'description' => 'دورة شاملة لتعلم ريادة الأعمال وبناء الشركات الناشئة',
                'category_name' => 'إدارة الأعمال',
                'duration' => 50,
                'capacity_limit' => 20,
                'price' => 499.99,
                'status' => 'active',
                'image' => 'courses/entrepreneurship.jpg'
            ],
            [
                'title' => 'إدارة الموارد البشرية',
                'description' => 'تعلم أساسيات إدارة الموارد البشرية في المؤسسات',
                'category_name' => 'إدارة الأعمال',
                'duration' => 30,
                'capacity_limit' => 30,
                'price' => 299.99,
                'status' => 'active',
                'image' => 'courses/hr-management.jpg'
            ],

            // Photography Courses
            [
                'title' => 'أساسيات التصوير الفوتوغرافي',
                'description' => 'تعلم أساسيات التصوير الفوتوغرافي والتقنيات المختلفة',
                'category_name' => 'التصوير',
                'duration' => 25,
                'capacity_limit' => 15,
                'price' => 199.99,
                'status' => 'active',
                'image' => 'courses/photography-basics.jpg'
            ],
            [
                'title' => 'تصوير البورتريه المحترف',
                'description' => 'دورة متخصصة في تصوير البورتريه والإضاءة المناسبة',
                'category_name' => 'التصوير',
                'duration' => 30,
                'capacity_limit' => 12,
                'price' => 299.99,
                'status' => 'active',
                'image' => 'courses/portrait-photography.jpg'
            ],

            // Cooking Courses
            [
                'title' => 'أساسيات الطبخ العربي',
                'description' => 'تعلم أساسيات الطبخ العربي والأطباق التقليدية',
                'category_name' => 'الطبخ',
                'duration' => 20,
                'capacity_limit' => 20,
                'price' => 149.99,
                'status' => 'active',
                'image' => 'courses/arabic-cooking.jpg'
            ],
            [
                'title' => 'الحلويات والمعجنات',
                'description' => 'دورة متخصصة في صنع الحلويات والمعجنات الشرقية والغربية',
                'category_name' => 'الطبخ',
                'duration' => 25,
                'capacity_limit' => 15,
                'price' => 199.99,
                'status' => 'active',
                'image' => 'courses/desserts-pastries.jpg'
            ],

            // Sports Courses
            [
                'title' => 'اللياقة البدنية المنزلية',
                'description' => 'برنامج تدريبي شامل للياقة البدنية يمكن ممارسته في المنزل',
                'category_name' => 'الرياضة',
                'duration' => 30,
                'capacity_limit' => 25,
                'price' => 99.99,
                'status' => 'active',
                'image' => 'courses/home-fitness.jpg'
            ],
            [
                'title' => 'اليوغا للمبتدئين',
                'description' => 'تعلم أساسيات اليوغا والتأمل للصحة النفسية والجسدية',
                'category_name' => 'الرياضة',
                'duration' => 20,
                'capacity_limit' => 20,
                'price' => 129.99,
                'status' => 'active',
                'image' => 'courses/yoga-beginners.jpg'
            ]
        ];

        foreach ($courses as $courseData) {
            // Find category by name
            $category = $categories->where('name', $courseData['category_name'])->first();

            if (!$category) {
                $this->command->warn("Category '{$courseData['category_name']}' not found for course '{$courseData['title']}'");
                continue;
            }

            // Get random instructor
            $instructor = $instructors->random();

            // Generate random start and end times
            $startTime = Carbon::now()->addDays(rand(7, 60));
            $endTime = $startTime->copy()->addHours($courseData['duration']);

            Course::firstOrCreate(
                ['title' => $courseData['title']],
                [
                    'description' => $courseData['description'],
                    'category_id' => $category->id,
                    'instructor_id' => $instructor->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'duration' => $courseData['duration'],
                    'capacity_limit' => $courseData['capacity_limit'],
                    'price' => $courseData['price'],
                    'status' => $courseData['status'],
                    'image' => $courseData['image']
                ]
            );
        }

        $this->command->info('Courses seeded successfully!');
    }
}
