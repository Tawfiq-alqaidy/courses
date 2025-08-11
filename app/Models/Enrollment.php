<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'status',
        'enrollment_date',
        'amount_paid',
        'notes',
        'progress_percentage',
        'grade'
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'amount_paid' => 'decimal:2',
        'progress_percentage' => 'decimal:2',
        'grade' => 'decimal:2'
    ];

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // When enrollment status changes, check if we need to promote waiting students
        static::updated(function ($enrollment) {
            if ($enrollment->wasChanged('status')) {
                $enrollment->handleStatusChange();
            }
        });

        // When enrollment is deleted, promote waiting students
        static::deleted(function ($enrollment) {
            $enrollment->promoteWaitingStudents();
        });
    }

    /**
     * Get the student who enrolled
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the course being enrolled in
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if enrollment is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if enrollment is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if enrollment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Approve enrollment
     */
    public function approve()
    {
        $this->update(['status' => 'approved']);
    }

    /**
     * Reject enrollment
     */
    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Complete enrollment
     */
    public function complete($grade = null)
    {
        $this->update([
            'status' => 'completed',
            'progress_percentage' => 100,
            'grade' => $grade
        ]);
    }

    /**
     * Handle status changes and manage waiting list
     */
    public function handleStatusChange()
    {
        $originalStatus = $this->getOriginal('status');
        $newStatus = $this->status;

        // If status changed from approved/completed to something else, 
        // or from something else to approved/completed
        if ($this->isStatusChangeAffectingCapacity($originalStatus, $newStatus)) {
            $this->promoteWaitingStudents();
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
     * Promote waiting students when spots become available
     */
    public function promoteWaitingStudents()
    {
        $course = $this->course;

        if (!$course || !$course->hasAvailableSpots()) {
            return;
        }

        // Find first pending enrollment (waiting student)
        $waitingEnrollment = Enrollment::where('course_id', $course->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($waitingEnrollment) {
            // Auto-approve the waiting student if there's space
            $waitingEnrollment->approve();

            // You could also send a notification here
            // $waitingEnrollment->student->notify(new EnrollmentApproved($waitingEnrollment));
        }
    }

    /**
     * Get enrollment statistics for a course
     */
    public static function getEnrollmentStats($courseId)
    {
        return [
            'total' => static::where('course_id', $courseId)->count(),
            'pending' => static::where('course_id', $courseId)->where('status', 'pending')->count(),
            'approved' => static::where('course_id', $courseId)->where('status', 'approved')->count(),
            'rejected' => static::where('course_id', $courseId)->where('status', 'rejected')->count(),
            'completed' => static::where('course_id', $courseId)->where('status', 'completed')->count(),
            'cancelled' => static::where('course_id', $courseId)->where('status', 'cancelled')->count(),
        ];
    }
}
