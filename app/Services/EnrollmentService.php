<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnrollmentService
{
    /**
     * Enroll a student in a course
     */
    public function enrollStudent(User $student, Course $course, array $data = [])
    {
        try {
            DB::beginTransaction();

            // Check if already enrolled
            $existingEnrollment = Enrollment::where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existingEnrollment) {
                throw new \Exception('Student is already enrolled in this course');
            }

            // Check if course is available for enrollment
            if (!$course->isActive()) {
                throw new \Exception('Course is not active');
            }

            // Determine enrollment status
            $status = $this->determineEnrollmentStatus($course);

            // Create enrollment
            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'status' => $status,
                'enrollment_date' => now(),
                'amount_paid' => $data['amount_paid'] ?? $course->price,
                'notes' => $data['notes'] ?? null,
            ]);

            DB::commit();

            return [
                'success' => true,
                'enrollment' => $enrollment,
                'message' => $this->getEnrollmentMessage($status),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Enrollment failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Determine enrollment status based on course availability
     */
    private function determineEnrollmentStatus(Course $course)
    {
        if ($course->hasAvailableSpots()) {
            return 'approved';
        }

        return 'pending'; // Will be in waiting list
    }

    /**
     * Get appropriate message based on enrollment status
     */
    private function getEnrollmentMessage($status)
    {
        $messages = [
            'approved' => 'تم قبولك في الدورة بنجاح!',
            'pending' => 'تم تسجيلك في قائمة الانتظار. سيتم إشعارك عند توفر مكان.',
        ];

        return $messages[$status] ?? 'تم التسجيل بنجاح';
    }

    /**
     * Update enrollment status
     */
    public function updateEnrollmentStatus(Enrollment $enrollment, $newStatus, array $data = [])
    {
        try {
            DB::beginTransaction();

            $oldStatus = $enrollment->status;

            $enrollment->update(array_merge(['status' => $newStatus], $data));

            // If status changed from non-capacity affecting to capacity affecting or vice versa
            if ($this->isStatusChangeAffectingCapacity($oldStatus, $newStatus)) {
                $this->processWaitingList($enrollment->course);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'تم تحديث حالة التسجيل بنجاح',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Status update failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process waiting list when spots become available
     */
    public function processWaitingList(Course $course)
    {
        while ($course->hasAvailableSpots()) {
            $waitingEnrollment = Enrollment::where('course_id', $course->id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->first();

            if (!$waitingEnrollment) {
                break; // No more waiting students
            }

            $waitingEnrollment->update(['status' => 'approved']);

            // Here you could send notifications
            Log::info("Student {$waitingEnrollment->student_id} promoted from waiting list for course {$course->id}");
        }
    }

    /**
     * Check if status change affects course capacity
     */
    private function isStatusChangeAffectingCapacity($oldStatus, $newStatus)
    {
        $capacityStatuses = ['approved', 'completed'];

        $oldAffectsCapacity = in_array($oldStatus, $capacityStatuses);
        $newAffectsCapacity = in_array($newStatus, $capacityStatuses);

        return $oldAffectsCapacity !== $newAffectsCapacity;
    }

    /**
     * Get enrollment statistics for a course
     */
    public function getCourseEnrollmentStats(Course $course)
    {
        return [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'capacity_limit' => $course->capacity_limit,
            'current_enrolled' => $course->getCurrentEnrolledCount(),
            'available_spots' => $course->getAvailableSpotsCount(),
            'enrollment_percentage' => $course->getEnrollmentPercentage(),
            'status_breakdown' => Enrollment::getEnrollmentStats($course->id),
        ];
    }

    /**
     * Cancel enrollment
     */
    public function cancelEnrollment(Enrollment $enrollment, $reason = null)
    {
        try {
            DB::beginTransaction();

            $oldStatus = $enrollment->status;

            $enrollment->update([
                'status' => 'cancelled',
                'notes' => $reason ? "Cancelled: {$reason}" : 'Cancelled by student',
            ]);

            // If this was an approved/completed enrollment, process waiting list
            if (in_array($oldStatus, ['approved', 'completed'])) {
                $this->processWaitingList($enrollment->course);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'تم إلغاء التسجيل بنجاح',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancellation failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
