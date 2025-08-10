<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StudentApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'education_level',
        'field_of_study',
        'current_occupation',
        'experience_years',
        'motivation',
        'expectations',
        'previous_experience',
        'available_times',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
        'amount_to_pay',
        'payment_completed',
        'payment_method',
        'payment_reference'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'available_times' => 'array',
        'reviewed_at' => 'datetime',
        'amount_to_pay' => 'decimal:2',
        'payment_completed' => 'boolean'
    ];

    /**
     * Get the course this application belongs to
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the admin who reviewed this application
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get age from date of birth
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Check if application is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if application is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if application is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Approve the application
     */
    public function approve($adminId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $adminId,
            'admin_notes' => $notes
        ]);
    }

    /**
     * Reject the application
     */
    public function reject($adminId, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $adminId,
            'admin_notes' => $notes
        ]);
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusColorAttribute()
    {
        switch($this->status) {
            case 'pending':
                return 'warning';
            case 'approved':
                return 'success';
            case 'rejected':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Get status text in Arabic
     */
    public function getStatusTextAttribute()
    {
        switch($this->status) {
            case 'pending':
                return 'في الانتظار';
            case 'approved':
                return 'مقبول';
            case 'rejected':
                return 'مرفوض';
            default:
                return 'غير محدد';
        }
    }

    /**
     * Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved applications
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
