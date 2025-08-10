<?php

// Simple data storage using JSON files instead of database
class SimpleDataStorage 
{
    private $dataPath;
    
    public function __construct($dataPath = 'storage/app/data/')
    {
        $this->dataPath = $dataPath;
        if (!is_dir($this->dataPath)) {
            mkdir($this->dataPath, 0755, true);
        }
    }
    
    private function getFilePath($table)
    {
        return $this->dataPath . $table . '.json';
    }
    
    private function loadData($table)
    {
        $file = $this->getFilePath($table);
        if (file_exists($file)) {
            $content = file_get_contents($file);
            return json_decode($content, true) ?: [];
        }
        return [];
    }
    
    private function saveData($table, $data)
    {
        $file = $this->getFilePath($table);
        return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    public function getAll($table)
    {
        return $this->loadData($table);
    }
    
    public function find($table, $id)
    {
        $data = $this->loadData($table);
        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }
        return null;
    }
    
    public function create($table, $item)
    {
        $data = $this->loadData($table);
        
        // Auto-increment ID
        $maxId = 0;
        foreach ($data as $existing) {
            if ($existing['id'] > $maxId) {
                $maxId = $existing['id'];
            }
        }
        
        $item['id'] = $maxId + 1;
        $item['created_at'] = date('Y-m-d H:i:s');
        $item['updated_at'] = date('Y-m-d H:i:s');
        
        $data[] = $item;
        $this->saveData($table, $data);
        
        return $item;
    }
    
    public function update($table, $id, $updates)
    {
        $data = $this->loadData($table);
        
        foreach ($data as $key => $item) {
            if ($item['id'] == $id) {
                $data[$key] = array_merge($item, $updates);
                $data[$key]['updated_at'] = date('Y-m-d H:i:s');
                $this->saveData($table, $data);
                return $data[$key];
            }
        }
        
        return null;
    }
    
    public function delete($table, $id)
    {
        $data = $this->loadData($table);
        
        foreach ($data as $key => $item) {
            if ($item['id'] == $id) {
                unset($data[$key]);
                $this->saveData($table, array_values($data));
                return true;
            }
        }
        
        return false;
    }
    
    public function where($table, $field, $value)
    {
        $data = $this->loadData($table);
        $results = [];
        
        foreach ($data as $item) {
            if (isset($item[$field]) && $item[$field] == $value) {
                $results[] = $item;
            }
        }
        
        return $results;
    }
    
    public function initSampleData()
    {
        // Create sample categories
        $categories = [
            ['id' => 1, 'name' => 'البرمجة وتطوير الويب', 'description' => 'تعلم البرمجة وتطوير التطبيقات', 'created_at' => '2025-08-01 00:00:00', 'updated_at' => '2025-08-01 00:00:00'],
            ['id' => 2, 'name' => 'التصميم الجرافيكي', 'description' => 'تصميم واجهات المستخدم والهوية البصرية', 'created_at' => '2025-08-01 00:00:00', 'updated_at' => '2025-08-01 00:00:00'],
            ['id' => 3, 'name' => 'التسويق الرقمي', 'description' => 'التسويق عبر الإنترنت ووسائل التواصل', 'created_at' => '2025-08-01 00:00:00', 'updated_at' => '2025-08-01 00:00:00'],
            ['id' => 4, 'name' => 'إدارة المشاريع', 'description' => 'أسس وممارسات إدارة المشاريع', 'created_at' => '2025-08-01 00:00:00', 'updated_at' => '2025-08-01 00:00:00']
        ];
        
        // Create sample courses
        $courses = [
            ['id' => 1, 'name' => 'تطوير المواقع بـ Laravel', 'category_id' => 1, 'description' => 'تعلم تطوير تطبيقات الويب الحديثة باستخدام إطار العمل Laravel', 'capacity' => 20, 'start_date' => '2025-08-15', 'start_time' => '09:00:00', 'created_at' => '2025-08-01 00:00:00', 'updated_at' => '2025-08-01 00:00:00'],
            ['id' => 2, 'name' => 'تصميم UI/UX المتقدم', 'category_id' => 2, 'description' => 'دورة شاملة في تصميم واجهات المستخدم وتجربة المستخدم', 'capacity' => 25, 'start_date' => '2025-08-20', 'start_time' => '14:00:00', 'created_at' => '2025-08-01 00:00:00', 'updated_at' => '2025-08-01 00:00:00'],
            ['id' => 3, 'name' => 'التسويق عبر وسائل التواصل', 'category_id' => 3, 'description' => 'استراتيجيات التسويق الحديثة عبر منصات التواصل الاجتماعي', 'capacity' => 30, 'start_date' => '2025-08-25', 'start_time' => '10:00:00', 'created_at' => '2025-08-01 00:00:00', 'updated_at' => '2025-08-01 00:00:00'],
            ['id' => 4, 'name' => 'إدارة المشاريع الرقمية', 'category_id' => 4, 'description' => 'أسس وممارسات إدارة المشاريع الرقمية والتقنية', 'capacity' => 15, 'start_date' => '2025-09-01', 'start_time' => '16:00:00', 'created_at' => '2025-08-01 00:00:00', 'updated_at' => '2025-08-01 00:00:00']
        ];
        
        // Create sample applications
        $applications = [
            ['id' => 1, 'student_code' => 'STU-2025001', 'student_name' => 'أحمد محمد علي', 'student_email' => 'ahmed@example.com', 'student_phone' => '0551234567', 'category_id' => 1, 'course_id' => 1, 'status' => 'registered', 'waiting_position' => null, 'created_at' => '2025-08-10 10:00:00', 'updated_at' => '2025-08-10 10:00:00'],
            ['id' => 2, 'student_code' => 'STU-2025002', 'student_name' => 'فاطمة أحمد سالم', 'student_email' => 'fatima@example.com', 'student_phone' => '0559876543', 'category_id' => 2, 'course_id' => 2, 'status' => 'waiting', 'waiting_position' => 1, 'created_at' => '2025-08-10 11:00:00', 'updated_at' => '2025-08-10 11:00:00'],
            ['id' => 3, 'student_code' => 'STU-2025003', 'student_name' => 'محمد عبدالله حسن', 'student_email' => 'mohammed@example.com', 'student_phone' => '0554567890', 'category_id' => 3, 'course_id' => 3, 'status' => 'registered', 'waiting_position' => null, 'created_at' => '2025-08-09 14:30:00', 'updated_at' => '2025-08-09 14:30:00']
        ];
        
        // Save the data
        $this->saveData('categories', $categories);
        $this->saveData('courses', $courses);
        $this->saveData('applications', $applications);
        
        return true;
    }
}

// Initialize data storage
$storage = new SimpleDataStorage();

// Check if we need to initialize sample data
if (empty($storage->getAll('categories'))) {
    $storage->initSampleData();
}

// Course Application Logic
class CourseApplicationLogic 
{
    private $storage;
    
    public function __construct($storage)
    {
        $this->storage = $storage;
    }
    
    public function determineApplicationStatus($courseId)
    {
        $course = $this->storage->find('courses', $courseId);
        if (!$course) return 'invalid';
        
        $registeredCount = count($this->storage->where('applications', 'course_id', $courseId));
        $registeredApproved = 0;
        
        $applications = $this->storage->where('applications', 'course_id', $courseId);
        foreach ($applications as $app) {
            if ($app['status'] === 'registered') {
                $registeredApproved++;
            }
        }
        
        if ($registeredApproved < $course['capacity']) {
            return 'registered';
        } else {
            return 'waiting';
        }
    }
    
    public function submitApplication($studentData, $selectedCourses)
    {
        $results = [];
        
        foreach ($selectedCourses as $courseId) {
            $course = $this->storage->find('courses', $courseId);
            if (!$course) continue;
            
            $status = $this->determineApplicationStatus($courseId);
            $studentCode = 'STU-' . date('Y') . sprintf('%05d', rand(10000, 99999));
            
            $waitingPosition = null;
            if ($status === 'waiting') {
                $waitingList = $this->storage->where('applications', 'course_id', $courseId);
                $waitingCount = 0;
                foreach ($waitingList as $app) {
                    if ($app['status'] === 'waiting') {
                        $waitingCount++;
                    }
                }
                $waitingPosition = $waitingCount + 1;
            }
            
            $application = [
                'student_code' => $studentCode,
                'student_name' => $studentData['name'],
                'student_email' => $studentData['email'],
                'student_phone' => $studentData['phone'],
                'category_id' => $studentData['category'],
                'course_id' => $courseId,
                'status' => $status,
                'waiting_position' => $waitingPosition
            ];
            
            $savedApp = $this->storage->create('applications', $application);
            
            $results[] = [
                'application' => $savedApp,
                'course' => $course,
                'message' => $status === 'registered' 
                    ? 'تم تسجيلك بنجاح في الدورة' 
                    : "تم وضعك في قائمة الانتظار - ترتيبك: {$waitingPosition}"
            ];
        }
        
        return $results;
    }
    
    public function promoteFromWaitingList($courseId)
    {
        $waitingList = $this->storage->where('applications', 'course_id', $courseId);
        
        // Sort by created_at to get first in line
        usort($waitingList, function($a, $b) {
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });
        
        foreach ($waitingList as $app) {
            if ($app['status'] === 'waiting') {
                $this->storage->update('applications', $app['id'], [
                    'status' => 'registered',
                    'waiting_position' => null
                ]);
                return $app;
            }
        }
        
        return null;
    }
    
    public function getStatistics()
    {
        $applications = $this->storage->getAll('applications');
        $courses = $this->storage->getAll('courses');
        
        $stats = [
            'total_applications' => count($applications),
            'registered' => 0,
            'waiting' => 0,
            'rejected' => 0,
            'active_courses' => count($courses)
        ];
        
        foreach ($applications as $app) {
            if (isset($stats[$app['status']])) {
                $stats[$app['status']]++;
            }
        }
        
        return $stats;
    }
}

// Initialize the application logic
$appLogic = new CourseApplicationLogic($storage);

// Handle API requests
$action = $_GET['action'] ?? 'dashboard';

header('Content-Type: application/json; charset=utf-8');

switch ($action) {
    case 'init':
        $storage->initSampleData();
        echo json_encode(['success' => true, 'message' => 'تم تهيئة البيانات بنجاح'], JSON_UNESCAPED_UNICODE);
        break;
        
    case 'dashboard':
        $stats = $appLogic->getStatistics();
        $recentApplications = array_slice($storage->getAll('applications'), -5);
        $courses = $storage->getAll('courses');
        
        echo json_encode([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'recent_applications' => $recentApplications,
                'courses' => $courses
            ]
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    case 'categories':
        echo json_encode([
            'success' => true,
            'data' => $storage->getAll('categories')
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    case 'courses':
        $courses = $storage->getAll('courses');
        $categories = $storage->getAll('categories');
        
        // Add category names and enrollment counts
        foreach ($courses as &$course) {
            foreach ($categories as $category) {
                if ($category['id'] == $course['category_id']) {
                    $course['category_name'] = $category['name'];
                    break;
                }
            }
            
            $enrollments = $storage->where('applications', 'course_id', $course['id']);
            $registered = 0;
            foreach ($enrollments as $enrollment) {
                if ($enrollment['status'] === 'registered') {
                    $registered++;
                }
            }
            $course['current_enrolled'] = $registered;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $courses
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    case 'applications':
        $applications = $storage->getAll('applications');
        $courses = $storage->getAll('courses');
        
        // Add course names
        foreach ($applications as &$app) {
            foreach ($courses as $course) {
                if ($course['id'] == $app['course_id']) {
                    $app['course_name'] = $course['name'];
                    break;
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'data' => $applications
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    case 'apply':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $results = $appLogic->submitApplication($input['student'], $input['courses']);
            
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
        $promoted = $appLogic->promoteFromWaitingList($courseId);
        
        echo json_encode([
            'success' => true,
            'data' => $promoted,
            'message' => $promoted ? 'تم ترقية الطالب بنجاح' : 'لا يوجد طلاب في قائمة الانتظار'
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    case 'update_status':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $updated = $storage->update('applications', $input['id'], ['status' => $input['status']]);
            
            if ($updated && $input['status'] === 'rejected') {
                // Auto-promote from waiting list
                $appLogic->promoteFromWaitingList($updated['course_id']);
            }
            
            echo json_encode([
                'success' => true,
                'data' => $updated,
                'message' => 'تم تحديث الحالة بنجاح'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Unknown action'
        ], JSON_UNESCAPED_UNICODE);
}
?>
