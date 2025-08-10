<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Application extends Model
    {
        use HasFactory;

        protected $fillable = [
            'student_name',
            'student_email',
            'student_phone',
            'category_id',
            'selected_courses',
            'unique_student_code',
            'status'
        ];

        protected $casts = [
            'selected_courses' => 'array'
        ];

        // Relationship with Category
        public function category()
        {
            return $this->belongsTo(Category::class);
        }

        // Accessor for status label
        public function getStatusLabelAttribute()
        {
            switch($this->status) {
                case 'unregistered':
                    return 'في الانتظار';
                case 'registered':
                    return 'مقبول';
                case 'waiting':
                    return 'قائمة الانتظار';
                default:
                    return 'غير محدد';
            }
        }

        // Get selected courses details
        public function getSelectedCoursesDetails()
        {
            if (empty($this->selected_courses) || !is_array($this->selected_courses)) {
                return collect();
            }

            return Course::whereIn('id', $this->selected_courses)->get();
        }

        // Scope for filtering by status
        public function scopeByStatus($query, $status)
        {
            return $query->where('status', $status);
        }

        // Scope for searching
        public function scopeSearch($query, $term)
        {
            return $query->where(function($q) use ($term) {
                $q->where('student_name', 'like', "%{$term}%")
                    ->orWhere('student_email', 'like', "%{$term}%")
                    ->orWhere('student_phone', 'like', "%{$term}%")
                    ->orWhere('unique_student_code', 'like', "%{$term}%");
            });
        }

        // Status scopes
        public function scopeRegistered($query)
        {
            return $query->where('status', 'registered');
        }

        public function scopeWaiting($query)
        {
            return $query->where('status', 'waiting');
        }

        public function scopeUnregistered($query)
        {
            return $query->where('status', 'unregistered');
        }

        /**
         * Check if all selected courses have available spots
         */
        public function canBeRegistered()
        {
            if (empty($this->selected_courses)) {
                return false;
            }

            // Check if all selected courses have capacity
            foreach ($this->selected_courses as $courseId) {
                $course = Course::findOrFail($courseId);

                    return true;

            }

            return true;
        }

        /**
         * Register the application with automatic waiting list logic
         */
        public function register()
        {
            if ($this->canBeRegistered()) {
                $this->update(['status' => 'registered']);
                return true;
            } else {
                // If cannot be registered due to capacity, put on waiting list
                $this->update(['status' => 'waiting']);
                return false;
            }
        }

        /**
         * Put application on waiting list
         */
        public function putOnWaitingList()
        {
            $this->update(['status' => 'waiting']);
        }

        /**
         * Mark as unregistered and promote waiting students
         */
        public function markAsUnregistered()
        {

            $this->update(['status' => 'unregistered']);

            return true;
        }

        /**
         * Promote waiting students when spots become available
         */
        private function promoteWaitingStudents()
        {
            if (empty($this->selected_courses) || !is_array($this->selected_courses)) {
                return;
            }

            foreach ($this->selected_courses as $courseId) {
                $course = Course::find($courseId);
                if (!$course) continue;

                // Check if course now has available spots
                if (!$course->hasAvailableSpots()) continue;

                // Find first waiting student for this course (ordered by application date)
                $waitingApplication = Application::where('status', 'waiting')
                    ->whereJsonContains('selected_courses', (string)$courseId)
                    ->orderBy('created_at', 'asc')
                    ->first();

                if ($waitingApplication) {
                    // Try to register the waiting student
                    $waitingApplication->attemptRegistrationFromWaiting();
                }
            }
        }

        /**
         * Attempt to register from waiting list
         */
        public function attemptRegistrationFromWaiting()
        {
            if ($this->status !== 'waiting') {
                return false;
            }

            if ($this->canBeRegistered()) {
                $this->update(['status' => 'registered']);
                return true;
            }

            return false;
        }

        /**
         * Generate unique student code
         */
        public static function generateUniqueCode()
        {
            do {
                $code = 'STU-' . strtoupper(substr(uniqid(), -8));
            } while (self::where('unique_student_code', $code)->exists());

            return $code;
        }

        /**
         * Boot method to auto-generate unique code and handle waiting list logic
         */
        protected static function boot()
        {
            parent::boot();

            static::creating(function ($application) {
                if (empty($application->unique_student_code)) {
                    $application->unique_student_code = self::generateUniqueCode();
                }

                // Auto-assign status based on course capacity when creating new application
                if (empty($application->status)) {
                    $application->status = $application->determineInitialStatus();
                }
            });
        }

        /**
         * Determine initial status based on course capacity
         */
        public function determineInitialStatus()
        {
            if (empty($this->selected_courses) || !is_array($this->selected_courses)) {
                return 'unregistered';
            }

            // Check if all selected courses have available spots
            $allCoursesAvailable = true;
            foreach ($this->selected_courses as $courseId) {
                $course = Course::find($courseId);
                if (!$course || !$course->hasAvailableSpots()) {
                    $allCoursesAvailable = false;
                    break;
                }
            }

            // If all courses have spots available, register immediately
            // If any course is full, add to waiting list
            return $allCoursesAvailable ? 'registered' : 'waiting';
        }

        /**
         * Get the selected courses
         */
        public function courses()
        {
            return Course::whereIn('id', $this->selected_courses ?? [])->get();
        }

        /**
         * Check if application is unregistered
         */
        public function isUnregistered()
        {
            return $this->status === 'unregistered';
        }

        /**
         * Check if application is registered
         */
        public function isRegistered()
        {
            return $this->status === 'registered';
        }

        /**
         * Check if application is waiting
         */
        public function isWaiting()
        {
            return $this->status === 'waiting';
        }

        /**
         * Get status badge color for UI
         */
        public function getStatusColorAttribute()
        {
            switch($this->status) {
                case 'registered':
                    return 'success';
                case 'waiting':
                    return 'warning';
                case 'unregistered':
                    return 'secondary';
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
                case 'registered':
                    return 'مسجل';
                case 'waiting':
                    return 'في قائمة الانتظار';
                case 'unregistered':
                    return 'غير مسجل';
                default:
                    return 'غير محدد';
            }
        }

        /**
         * Get courses count
         */
        public function getCoursesCountAttribute()
        {
            return count($this->selected_courses ?? []);
        }

        /**
         * Get waiting list position for this application and course
         */
        public function getWaitingListPosition($courseId = null)
        {
            if ($this->status !== 'waiting') {
                return null;
            }

            if ($courseId) {
                // Position for specific course
                return Application::where('status', 'waiting')
                        ->whereJsonContains('selected_courses', (string)$courseId)
                        ->where('created_at', '<', $this->created_at)
                        ->count() + 1;
            } else {
                // Overall position (for first course in selected courses)
                if (empty($this->selected_courses)) {
                    return null;
                }

                $firstCourse = $this->selected_courses[0];
                return $this->getWaitingListPosition($firstCourse);
            }
        }

        /**
         * Check if application can be moved from waiting to registered
         */
        public function canPromoteFromWaiting()
        {
            return $this->status === 'waiting' && $this->canBeRegistered();
        }
    }
