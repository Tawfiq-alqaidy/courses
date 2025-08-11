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
        'status' => 'string'
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
        $registeredCount = \App\Models\Application::where('status', 'registered')
            ->whereJsonContains('selected_courses', (string)$this->id)
            ->count();
        return $registeredCount < $this->capacity_limit;
    }

    public function getAvailableSpotsCount()
    {
        $registeredCount = \App\Models\Application::where('status', 'registered')
            ->whereJsonContains('selected_courses', (string)$this->id)
            ->count();
        return max(0, $this->capacity_limit - $registeredCount);
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
        if ($this->capacity_limit <= 0) {
            return 0;
        }

        $enrolled = $this->enrollments()->count();
        return round(($enrolled / $this->capacity_limit) * 100, 1);
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
            ->whereRaw('(SELECT COUNT(*) FROM enrollments WHERE enrollments.course_id = courses.id) < capacity_limit');
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
}