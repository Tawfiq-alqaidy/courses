<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;
use App\Models\Role;
use App\Models\Enrollment;
use App\Services\EnrollmentService;

class TestEnrollmentScenario extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧪 Testing Enrollment System Scenarios...');

        $enrollmentService = new EnrollmentService();

        // Get test data
        $course = Course::where('status', 'active')->first();
        $studentRole = Role::where('name', 'student')->first();
        $students = User::where('role_id', $studentRole->id)->limit(10)->get();

        if (!$course || $students->count() < 5) {
            $this->command->error('Insufficient test data. Need at least 1 course and 5 students.');
            return;
        }

        $this->command->info("📚 Testing with course: {$course->title} (Capacity: {$course->capacity_limit})");

        // Clear existing enrollments for this test
        Enrollment::where('course_id', $course->id)->delete();

        // Scenario 1: Enroll students up to capacity
        $this->command->info("\n📝 Scenario 1: Enrolling students up to capacity...");

        $enrollCount = min($course->capacity_limit + 2, $students->count()); // Enroll more than capacity to test waiting list

        for ($i = 0; $i < $enrollCount; $i++) {
            $student = $students[$i];
            $result = $enrollmentService->enrollStudent($student, $course);

            $status = $result['success'] ? '✅' : '❌';
            $this->command->info("{$status} Student {$student->name}: {$result['message']}");
        }

        // Show stats after enrollment
        $this->showCourseStats($enrollmentService, $course);

        // Scenario 2: Cancel an approved enrollment and check waiting list promotion
        $this->command->info("\n📝 Scenario 2: Testing cancellation and waiting list promotion...");

        $approvedEnrollment = Enrollment::where('course_id', $course->id)
            ->where('status', 'approved')
            ->first();

        if ($approvedEnrollment) {
            $this->command->info("📤 Cancelling enrollment for: {$approvedEnrollment->student->name}");
            $result = $enrollmentService->cancelEnrollment($approvedEnrollment, 'Testing scenario');

            if ($result['success']) {
                $this->command->info("✅ {$result['message']}");
                $this->command->info("🔄 Checking if waiting students were promoted...");
                $this->showCourseStats($enrollmentService, $course);
            }
        }

        $this->command->info("\n✅ All enrollment scenarios tested successfully!");
    }

    private function showCourseStats($enrollmentService, $course)
    {
        $stats = $enrollmentService->getCourseEnrollmentStats($course);

        $this->command->info("\n📊 Current Statistics:");
        $this->command->info("Enrolled: {$stats['current_enrolled']}/{$stats['capacity_limit']} ({$stats['enrollment_percentage']}%)");
        $this->command->info("Available: {$stats['available_spots']} spots");

        $breakdown = $stats['status_breakdown'];
        $this->command->info("Status: Pending({$breakdown['pending']}) | Approved({$breakdown['approved']}) | Completed({$breakdown['completed']}) | Rejected({$breakdown['rejected']}) | Cancelled({$breakdown['cancelled']})");
    }
}
