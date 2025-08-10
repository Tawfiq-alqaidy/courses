<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'capacity_limit',
        'start_time',
        'end_time'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    /**
     * Get the category this course belongs to
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get applications for this course
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'selected_courses');
    }

    /**
     * Check if course has available spots
     */
    public function hasAvailableSpots()
    {
        // Count registered applications for this course
        $registeredCount = Application::where('status', 'registered')
            ->whereJsonContains('selected_courses', $this->id)
            ->count();
            
        return $registeredCount < $this->capacity_limit;
    }

    /**
     * Get available spots count
     */
    public function getAvailableSpotsAttribute()
    {
        $registeredCount = Application::where('status', 'registered')
            ->whereJsonContains('selected_courses', $this->id)
            ->count();
            
        return $this->capacity_limit - $registeredCount;
    }

    /**
     * Get registered students count
     */
    public function getRegisteredStudentsCountAttribute()
    {
        return Application::where('status', 'registered')
            ->whereJsonContains('selected_courses', (string)$this->id)
            ->count();
    }

    /**
     * Get waiting students count
     */
    public function getWaitingStudentsCountAttribute()
    {
        return Application::where('status', 'waiting')
            ->whereJsonContains('selected_courses', (string)$this->id)
            ->count();
    }

    /**
     * Get total applications count (registered + waiting)
     */
    public function getTotalApplicationsCountAttribute()
    {
        return Application::whereIn('status', ['registered', 'waiting'])
            ->whereJsonContains('selected_courses', (string)$this->id)
            ->count();
    }

    /**
     * Check if course is full (no available spots)
     */
    public function isFullAttribute()
    {
        return !$this->hasAvailableSpots();
    }

    /**
     * Get course status based on capacity
     */
    public function getStatusAttribute()
    {
        if ($this->registered_students_count >= $this->capacity_limit) {
            return 'full';
        } elseif ($this->registered_students_count > ($this->capacity_limit * 0.8)) {
            return 'almost_full';
        } else {
            return 'available';
        }
    }

    /**
     * Get capacity percentage
     */
    public function getCapacityPercentageAttribute()
    {
        if ($this->capacity_limit == 0) return 0;
        return round(($this->registered_students_count / $this->capacity_limit) * 100, 1);
    }
}
