<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\User;
use App\Models\Role;
use App\Models\Enrollment;
use App\Services\EnrollmentService;

class TestEnrollmentSystem extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:enrollment-system';

    /**
     * The console command description.
     */
    protected $description = 'Test the enrollment system functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Course Enrollment System...');
        $this->newLine();

        $enrollmentService = new EnrollmentService();

        // Get test data
        $course = Course::where('status', 'active')->first();
        $studentRole = Role::where('name', 'student')->first();
        $students = User::where('role_id', $studentRole->id)->take(5)->get();

        if (!$course) {
            $this->error('No active courses found. Please run CourseSeeder first.');
            return;
        }

        if ($students->isEmpty()) {
            $this->error('No students found. Please run UserSeeder first.');
            return;
        }

        $this->info("📚 Testing with course: {$course->title}");
        $this->info("👥 Course capacity: {$course->capacity_limit}");
        $this->newLine();

        // Display initial stats
        $this->displayCourseStats($enrollmentService, $course);

        // Test enrolling students
        $this->info('🔄 Testing student enrollments...');
        foreach ($students as $index => $student) {
            $result = $enrollmentService->enrollStudent($student, $course);

            if ($result['success']) {
                $this->info("✅ Student {$student->name}: {$result['message']}");
            } else {
                $this->error("❌ Student {$student->name}: {$result['message']}");
            }
        }

        $this->newLine();
        $this->displayCourseStats($enrollmentService, $course);

        // Test status changes
        $this->testStatusChanges($enrollmentService, $course);

        $this->newLine();
        $this->info('✅ Enrollment system test completed!');
    }

    private function displayCourseStats($enrollmentService, $course)
    {
        $stats = $enrollmentService->getCourseEnrollmentStats($course);

        $this->info('📊 Current Course Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Capacity', $stats['capacity_limit']],
                ['Currently Enrolled', $stats['current_enrolled']],
                ['Available Spots', $stats['available_spots']],
                ['Enrollment %', $stats['enrollment_percentage'] . '%'],
            ]
        );

        $this->info('📋 Status Breakdown:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Pending', $stats['status_breakdown']['pending']],
                ['Approved', $stats['status_breakdown']['approved']],
                ['Completed', $stats['status_breakdown']['completed']],
                ['Rejected', $stats['status_breakdown']['rejected']],
                ['Cancelled', $stats['status_breakdown']['cancelled']],
            ]
        );
        $this->newLine();
    }

    private function testStatusChanges($enrollmentService, $course)
    {
        $this->info('🔄 Testing status changes and waiting list management...');

        // Get an approved enrollment to test cancellation
        $approvedEnrollment = Enrollment::where('course_id', $course->id)
            ->where('status', 'approved')
            ->first();

        if ($approvedEnrollment) {
            $this->info("📤 Cancelling enrollment for student: {$approvedEnrollment->student->name}");

            $result = $enrollmentService->cancelEnrollment($approvedEnrollment, 'Testing cancellation');

            if ($result['success']) {
                $this->info("✅ {$result['message']}");

                // Check if waiting list was processed
                $this->info('🔍 Checking if waiting students were promoted...');
                $this->displayCourseStats($enrollmentService, $course);
            } else {
                $this->error("❌ {$result['message']}");
            }
        }
    }
}
