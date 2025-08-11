<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get students and courses
        $studentRole = Role::where('name', 'student')->first();
        $students = User::where('role_id', $studentRole->id ?? 0)->get();
        $courses = Course::where('status', 'active')->get();

        if ($students->isEmpty()) {
            $this->command->error('No students found. Please run UserSeeder first.');
            return;
        }

        if ($courses->isEmpty()) {
            $this->command->error('No active courses found. Please run CourseSeeder first.');
            return;
        }

        // Create sample enrollments
        $enrollmentData = [];
        $createdEnrollments = 0;

        foreach ($courses as $course) {
            // Randomly enroll 60-80% of course capacity
            $targetEnrollments = rand(
                ceil($course->capacity_limit * 0.6),
                ceil($course->capacity_limit * 0.8)
            );

            $selectedStudents = $students->random(min($targetEnrollments, $students->count()));

            foreach ($selectedStudents as $index => $student) {
                // Check if already enrolled
                $existingEnrollment = Enrollment::where('student_id', $student->id)
                    ->where('course_id', $course->id)
                    ->first();

                if ($existingEnrollment) {
                    continue;
                }

                // Determine status based on course capacity
                $approvedCount = Enrollment::where('course_id', $course->id)
                    ->whereIn('status', ['approved', 'completed'])
                    ->count();

                $status = 'pending';
                if ($approvedCount < $course->capacity_limit) {
                    $status = $index < $course->capacity_limit ? 'approved' : 'pending';
                }

                // Random status distribution for realism
                $statusOptions = ['pending', 'approved', 'completed', 'rejected'];
                $statusWeights = [30, 50, 15, 5]; // percentage distribution

                if ($status === 'approved') {
                    // For approved students, add some variety
                    $rand = rand(1, 100);
                    if ($rand <= 70) {
                        $status = 'approved';
                    } elseif ($rand <= 85) {
                        $status = 'completed';
                    }
                }

                $enrollmentDate = Carbon::now()->subDays(rand(1, 30));

                Enrollment::create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'status' => $status,
                    'enrollment_date' => $enrollmentDate,
                    'amount_paid' => $status === 'rejected' ? 0 : $course->price,
                    'progress_percentage' => $status === 'completed' ? 100 : rand(0, 80),
                    'grade' => $status === 'completed' ? rand(70, 100) : null,
                    'notes' => $this->getRandomNote($status),
                ]);

                $createdEnrollments++;
            }
        }

        $this->command->info("Created {$createdEnrollments} enrollment records successfully!");

        // Display enrollment statistics
        $this->displayEnrollmentStats();
    }

    /**
     * Get random notes based on status
     */
    private function getRandomNote($status)
    {
        $notes = [
            'pending' => [
                'في انتظار المراجعة',
                'تم استلام الطلب',
                'في قائمة الانتظار',
            ],
            'approved' => [
                'تم قبول الطالب',
                'مؤهل للانضمام',
                'تم تأكيد التسجيل',
            ],
            'completed' => [
                'أكمل الدورة بنجاح',
                'أداء ممتاز',
                'تخرج بتقدير جيد جداً',
            ],
            'rejected' => [
                'لا يستوفي المتطلبات',
                'تم الرفض لعدم اكتمال الوثائق',
                'تم إلغاء التسجيل بناءً على طلب الطالب',
            ]
        ];

        $statusNotes = $notes[$status] ?? ['تم التسجيل'];
        return $statusNotes[array_rand($statusNotes)];
    }

    /**
     * Display enrollment statistics
     */
    private function displayEnrollmentStats()
    {
        $courses = Course::with('enrollments')->get();

        $this->command->info("\n📊 Enrollment Statistics:");
        $this->command->info("+" . str_repeat("-", 70) . "+");
        $this->command->info("| Course Title                    | Capacity | Enrolled | Available |");
        $this->command->info("+" . str_repeat("-", 70) . "+");

        foreach ($courses as $course) {
            $enrolled = $course->getCurrentEnrolledCount();
            $available = $course->getAvailableSpotsCount();

            $title = mb_substr($course->title, 0, 30);
            $this->command->info(sprintf(
                "| %-30s | %-8d | %-8d | %-9d |",
                $title,
                $course->capacity_limit,
                $enrolled,
                $available
            ));
        }

        $this->command->info("+" . str_repeat("-", 70) . "+");
    }
}
