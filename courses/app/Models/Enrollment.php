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
}
