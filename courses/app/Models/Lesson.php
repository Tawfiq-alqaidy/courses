<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'title_ar',
        'content',
        'content_ar',
        'order',
        'duration_minutes',
        'type',
        'video_url',
        'attachments',
        'is_free'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_free' => 'boolean'
    ];

    /**
     * Get the course this lesson belongs to
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get localized title
     */
    public function getDisplayTitleAttribute()
    {
        return app()->getLocale() === 'ar' && $this->title_ar ? $this->title_ar : $this->title;
    }

    /**
     * Get localized content
     */
    public function getDisplayContentAttribute()
    {
        return app()->getLocale() === 'ar' && $this->content_ar ? $this->content_ar : $this->content;
    }

    /**
     * Check if lesson is video
     */
    public function isVideo()
    {
        return $this->type === 'video';
    }

    /**
     * Check if lesson is assignment
     */
    public function isAssignment()
    {
        return $this->type === 'assignment';
    }

    /**
     * Check if lesson is quiz
     */
    public function isQuiz()
    {
        return $this->type === 'quiz';
    }
}
