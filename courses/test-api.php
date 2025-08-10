<?php
/**
 * Simple Test Application - Course Management System Demo
 * This demonstrates the core functionality without Laravel dependencies
 */

// Simulate database data
$categories = [
    ['id' => 1, 'name' => 'البرمجة وتطوير الويب'],
    ['id' => 2, 'name' => 'التصميم الجرافيكي'],
    ['id' => 3, 'name' => 'التسويق الرقمي'],
    ['id' => 4, 'name' => 'إدارة المشاريع'],
];

$courses = [
    [
        'id' => 1,
        'name' => 'تطوير المواقع بـ Laravel',
        'category_id' => 1,
        'category_name' => 'البرمجة وتطوير الويب',
        'capacity' => 20,
        'current_enrolled' => 15,
        'start_date' => '2025-08-15',
        'start_time' => '09:00',
        'description' => 'تعلم تطوير تطبيقات الويب الحديثة باستخدام إطار العمل Laravel'
    ],
    [
        'id' => 2,
        'name' => 'تصميم UI/UX المتقدم',
        'category_id' => 2,
        'category_name' => 'التصميم الجرافيكي',
        'capacity' => 25,
        'current_enrolled' => 25,
        'start_date' => '2025-08-20',
        'start_time' => '14:00',
        'description' => 'دورة شاملة في تصميم واجهات المستخدم وتجربة المستخدم'
    ],
    [
        'id' => 3,
        'name' => 'التسويق عبر وسائل التواصل',
        'category_id' => 3,
        'category_name' => 'التسويق الرقمي',
        'capacity' => 30,
        'current_enrolled' => 28,
        'start_date' => '2025-08-25',
        'start_time' => '10:00',
        'description' => 'استراتيجيات التسويق الحديثة عبر منصات التواصل الاجتماعي'
    ],
    [
        'id' => 4,
        'name' => 'إدارة المشاريع الرقمية',
        'category_id' => 4,
        'category_name' => 'إدارة المشاريع',
        'capacity' => 15,
        'current_enrolled' => 8,
        'start_date' => '2025-09-01',
        'start_time' => '16:00',
        'description' => 'أسس وممارسات إدارة المشاريع الرقمية والتقنية'
    ]
];

$applications = [
    [
        'id' => 1,
        'student_code' => 'STU-2025001',
        'student_name' => 'أحمد محمد علي',
        'student_email' => 'ahmed@example.com',
        'student_phone' => '0551234567',
        'course_id' => 1,
        'course_name' => 'تطوير المواقع بـ Laravel',
        'status' => 'registered',
        'waiting_position' => null,
        'created_at' => '2025-08-10 10:00:00'
    ],
    [
        'id' => 2,
        'student_code' => 'STU-2025002',
        'student_name' => 'فاطمة أحمد سالم',
        'student_email' => 'fatima@example.com',
        'student_phone' => '0559876543',
        'course_id' => 2,
        'course_name' => 'تصميم UI/UX المتقدم',
        'status' => 'waiting',
        'waiting_position' => 1,
        'created_at' => '2025-08-10 11:00:00'
    ],
    [
        'id' => 3,
        'student_code' => 'STU-2025003',
        'student_name' => 'محمد عبدالله حسن',
        'student_email' => 'mohammed@example.com',
        'student_phone' => '0554567890',
        'course_id' => 3,
        'course_name' => 'التسويق عبر وسائل التواصل',
        'status' => 'registered',
        'waiting_position' => null,
        'created_at' => '2025-08-09 14:30:00'
    ]
];

/**
 * Core Application Logic - Demonstrates Waiting List Functionality
 */
class CourseApplicationSystem 
{
    private $courses;
    private $applications;
    
    public function __construct($courses, $applications) 
    {
        $this->courses = $courses;
        $this->applications = $applications;
    }
    
    /**
     * Determine if student should be registered or put on waiting list
     */
    public function determineApplicationStatus($courseId) 
    {
        $course = $this->getCourse($courseId);
        if (!$course) return 'invalid';
        
        $registeredCount = $this->getRegisteredCount($courseId);
        
        if ($registeredCount < $course['capacity']) {
            return 'registered';
        } else {
            return 'waiting';
        }
    }
    
    /**
     * Get course by ID
     */
    public function getCourse($courseId) 
    {
        foreach ($this->courses as $course) {
            if ($course['id'] == $courseId) {
                return $course;
            }
        }
        return null;
    }
    
    /**
     * Count registered students for a course
     */
    public function getRegisteredCount($courseId) 
    {
        $count = 0;
        foreach ($this->applications as $app) {
            if ($app['course_id'] == $courseId && $app['status'] == 'registered') {
                $count++;
            }
        }
        return $count;
    }
    
    /**
     * Get waiting list for a course
     */
    public function getWaitingList($courseId) 
    {
        $waiting = [];
        foreach ($this->applications as $app) {
            if ($app['course_id'] == $courseId && $app['status'] == 'waiting') {
                $waiting[] = $app;
            }
        }
        // Sort by application date
        usort($waiting, function($a, $b) {
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });
        return $waiting;
    }
    
    /**
     * Promote first student from waiting list to registered
     */
    public function promoteFromWaitingList($courseId) 
    {
        $waitingList = $this->getWaitingList($courseId);
        if (!empty($waitingList)) {
            // In real system, this would update database
            $firstStudent = $waitingList[0];
            echo "تم ترقية الطالب: " . $firstStudent['student_name'] . " من قائمة الانتظار إلى مسجل\n";
            return $firstStudent;
        }
        return null;
    }
    
    /**
     * Generate statistics for admin dashboard
     */
    public function getStatistics() 
    {
        $stats = [
            'total_applications' => count($this->applications),
            'registered' => 0,
            'waiting' => 0,
            'rejected' => 0,
            'active_courses' => count($this->courses)
        ];
        
        foreach ($this->applications as $app) {
            $stats[$app['status']]++;
        }
        
        return $stats;
    }
    
    /**
     * Simulate new application submission
     */
    public function submitApplication($studentData, $courseIds) 
    {
        $results = [];
        foreach ($courseIds as $courseId) {
            $status = $this->determineApplicationStatus($courseId);
            $course = $this->getCourse($courseId);
            
            $studentCode = 'STU-' . date('Y') . sprintf('%03d', rand(100, 999));
            
            $results[] = [
                'student_code' => $studentCode,
                'course_name' => $course['name'],
                'status' => $status,
                'message' => $status == 'registered' 
                    ? 'تم تسجيلك بنجاح في الدورة' 
                    : 'تم وضعك في قائمة الانتظار - سيتم إشعارك عند توفر مكان'
            ];
        }
        return $results;
    }
}

// Initialize the system
$system = new CourseApplicationSystem($courses, $applications);

// Handle requests
$action = $_GET['action'] ?? 'dashboard';

header('Content-Type: application/json; charset=utf-8');

switch ($action) {
    case 'dashboard':
        $stats = $system->getStatistics();
        echo json_encode([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'recent_applications' => array_slice($applications, -5),
                'courses' => $courses
            ]
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    case 'courses':
        echo json_encode([
            'success' => true,
            'data' => $courses
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    case 'categories':
        echo json_encode([
            'success' => true,
            'data' => $categories
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    case 'applications':
        echo json_encode([
            'success' => true,
            'data' => $applications
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    case 'apply':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $results = $system->submitApplication($input['student'], $input['courses']);
            echo json_encode([
                'success' => true,
                'data' => $results
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
        
    case 'promote':
        $courseId = $_GET['course_id'] ?? 0;
        $promoted = $system->promoteFromWaitingList($courseId);
        echo json_encode([
            'success' => true,
            'data' => $promoted,
            'message' => $promoted ? 'تم ترقية الطالب بنجاح' : 'لا يوجد طلاب في قائمة الانتظار'
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    case 'test':
        // Test the waiting list logic
        echo json_encode([
            'success' => true,
            'data' => [
                'message' => 'Course Application System is working!',
                'php_version' => phpversion(),
                'timestamp' => date('Y-m-d H:i:s'),
                'test_results' => [
                    'course_1_status' => $system->determineApplicationStatus(1),
                    'course_2_status' => $system->determineApplicationStatus(2),
                    'waiting_list_course_2' => $system->getWaitingList(2),
                    'statistics' => $system->getStatistics()
                ]
            ]
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Unknown action'
        ], JSON_UNESCAPED_UNICODE);
}
?>
