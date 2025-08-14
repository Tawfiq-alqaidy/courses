<?php

namespace App\Models;

use App\Providers\FileDbConnection;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'start_time',
        'end_time',
        'capacity_limit',
        'price',
        'duration',
        'status',
        'image',
        'instructor_id'
    ];

    public $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'price' => 'decimal:2',
        'status' => 'string',
        'capacity_limit' => 'integer',
    ];

    // العلاقات
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    // الوظائف المساعدة
    public function hasAvailableSpots()
    {
        $enrolledCount = $this->enrollments()
            ->whereIn('status', ['approved', 'completed'])
            ->count();
        return $enrolledCount < $this->capacity_limit;
    }

    public function getAvailableSpotsCount()
    {
        $enrolledCount = $this->enrollments()
            ->whereIn('status', ['approved', 'completed'])
            ->count();
        return max(0, $this->capacity_limit - $enrolledCount);
    }

    public function getCurrentEnrolledCount()
    {
        // Count applications for this course that are registered
        $count = 0;
        $applications = Application::where('status', 'registered')->get();

        foreach ($applications as $application) {
            if (in_array($this->id, $application->selected_courses ?? [])) {
                $count++;
            }
        }

        return $count;
    }

    public function isFull()
    {
        return !$this->hasAvailableSpots();
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isUpcoming()
    {
        return $this->start_time && $this->start_time->isFuture();
    }

    public function isOngoing()
    {
        $now = now();
        return $this->start_time && $this->end_time &&
            $this->start_time->lte($now) && $this->end_time->gte($now);
    }

    public function isCompleted()
    {
        return $this->end_time && $this->end_time->isPast();
    }

    public function getDurationInHours()
    {
        if ($this->duration) {
            return $this->duration;
        }

        if ($this->start_time && $this->end_time) {
            return $this->start_time->diffInHours($this->end_time);
        }

        return 0;
    }

    public function getFormattedPrice()
    {
        return '$' . number_format($this->price, 2);
    }

    public function getEnrollmentPercentage()
    {
        // Always use the accessor for consistency
        return $this->capacityPercentage;
    }

    // Scopes للاستعلامات
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }

    public function scopeOngoing($query)
    {
        $now = now();
        return $query->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
            ->whereRaw('(SELECT COUNT(*) FROM enrollments WHERE enrollments.course_id = courses.id AND enrollments.status IN ("approved", "completed")) < capacity_limit');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'active' => 'success',
            'inactive' => 'secondary',
            'draft' => 'warning',
            'archived' => 'danger'
        ];

        $color = $statuses[$this->status] ?? 'secondary';
        return "<span class='badge bg-{$color}'>{$this->status}</span>";
    }

    public function getIsEnrollmentOpenAttribute()
    {
        return $this->isActive() && $this->hasAvailableSpots() && $this->isUpcoming();
    }

    public function getCapacityPercentageAttribute()
    {
        if ((int)$this->capacity_limit <= 0) {
            return 0.0;
        }
        $enrolled = (int)$this->getCurrentEnrolledCount();
        return round(($enrolled / (float)$this->capacity_limit) * 100, 1);
    }

    public function getRegisteredStudentsCountAttribute()
    {
        return $this->getCurrentEnrolledCount();
    }

    /**
     * Check if this course conflicts with another course
     */
    public function conflictsWith(Course $otherCourse)
    {
        // Skip if either course doesn't have start/end times
        if (!$this->start_time || !$this->end_time || !$otherCourse->start_time || !$otherCourse->end_time) {
            return false;
        }

        // Check if same day first
        if ($this->start_time->isSameDay($otherCourse->start_time)) {
            // Two time periods overlap if: start1 < end2 AND start2 < end1
            return $this->start_time->lt($otherCourse->end_time) && $otherCourse->start_time->lt($this->end_time);
        }

        // Check for multi-day course conflicts
        return $this->start_time->lt($otherCourse->end_time) && $otherCourse->start_time->lt($this->end_time);
    }

    /**
     * Get formatted time range for display
     */
    public function getTimeRangeAttribute()
    {
        if (!$this->start_time) {
            return 'غير محدد';
        }

        $timeRange = $this->start_time->format('Y-m-d H:i');

        if ($this->end_time) {
            if ($this->start_time->isSameDay($this->end_time)) {
                // Same day - show only end time
                $timeRange .= ' - ' . $this->end_time->format('H:i');
            } else {
                // Different days - show full end datetime
                $timeRange .= ' - ' . $this->end_time->format('Y-m-d H:i');
            }
        }

        return $timeRange;
    }

    /**
     * Check for conflicts in a collection of courses
     */
    public static function findConflicts($courses)
    {
        $conflicts = [];
        $coursesArray = $courses->toArray();

        for ($i = 0; $i < count($coursesArray); $i++) {
            for ($j = $i + 1; $j < count($coursesArray); $j++) {
                $course1 = Course::find($coursesArray[$i]['id']);
                $course2 = Course::find($coursesArray[$j]['id']);

                if ($course1 && $course2 && $course1->conflictsWith($course2)) {
                    $conflicts[] = [
                        'course1' => $course1,
                        'course2' => $course2,
                        'message' => "دورة '{$course1->title}' ({$course1->time_range}) تتعارض مع دورة '{$course2->title}' ({$course2->time_range})"
                    ];
                }
            }
        }

        return $conflicts;
    }
}
