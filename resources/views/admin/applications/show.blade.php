@extends('layouts.app')

@section('title', 'تفاصيل طلب التسجيل')

@push('styles')
<style>
    .application-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 15px;
    }

    .detail-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }

    .student-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        font-weight: bold;
        margin-right: 1rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .status-unregistered {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .status-registered {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-waiting {
        background: #e2e3e5;
        color: #383d41;
        border: 1px solid #d6d8db;
    }

    .course-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        border-left: 4px solid #667eea;
    }

    .info-item {
        margin-bottom: 1rem;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    .info-value {
        color: #212529;
        font-size: 1rem;
    }

    .action-buttons .btn {
        margin: 0.25rem;
        min-width: 120px;
    }

    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
        background: white;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 20px;
        width: 12px;
        height: 12px;
        background: #667eea;
        border: 3px solid white;
        border-radius: 50%;
        box-shadow: 0 0 0 2px #667eea;
    }

    @media (max-width: 768px) {
        .student-info {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .action-buttons .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="application-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h2 fw-bold mb-2">
                        <i class="bx bx-file-blank me-2"></i>
                        تفاصيل طلب التسجيل
                    </h1>
                    <p class="mb-0 opacity-90">رقم الطلب: {{ $application->unique_student_code }}</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-light">
                        <i class="bx bx-arrow-back me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row">
            <!-- Student Information -->
            <div class="col-lg-8">
                <div class="detail-card">
                    <div class="d-flex align-items-start student-info mb-4">
                        <div class="student-avatar">
                            {{ mb_substr($application->student_name, 0, 2) }}
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mb-2">{{ $application->student_name }}</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="bx bx-envelope me-1"></i>البريد الإلكتروني
                                        </div>
                                        <div class="info-value">{{ $application->student_email }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="bx bx-phone me-1"></i>رقم الهاتف
                                        </div>
                                        <div class="info-value">{{ $application->student_phone }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="bx bx-category me-1"></i>التخصص
                                        </div>
                                        <div class="info-value">
                                            <span class="badge bg-primary">{{ $application->category?->name ?? 'جميع التخصصات' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="bx bx-time me-1"></i>تاريخ التقديم
                                        </div>
                                        <div class="info-value">{{ $application->created_at->format('Y/m/d H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Courses -->
                    <h5 class="mb-3">
                        <i class="bx bx-book-open text-primary me-2"></i>
                        الدورات المختارة ({{ count($selectedCourses) }} دورة)
                    </h5>

                    @if($selectedCourses->count() > 0)
                    <form id="courseReviewForm" method="POST" action="{{ route('admin.applications.update-courses', $application) }}">
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            @foreach($selectedCourses as $course)
                            <div class="col-md-6 mb-3">
                                <div class="course-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-2">{{ $course->title }}</h6>
                                        <div class="form-check">
                                            <input class="form-check-input course-checkbox" type="checkbox" 
                                                   name="selected_courses[]" value="{{ $course->id }}" 
                                                   id="course_{{ $course->id }}" checked>
                                            <label class="form-check-label text-success" for="course_{{ $course->id }}">
                                                <small>موافق عليها</small>
                                            </label>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-2">{{ $course->description }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bx bx-group me-1"></i>
                                            السعة: {{ $course->capacity_limit }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="bx bx-calendar me-1"></i>
                                            {{ $course->start_time ? \Carbon\Carbon::parse($course->start_time)->format('Y/m/d') : 'غير محدد' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="action-buttons mt-4 text-center">
                            <button type="button" class="btn btn-secondary" onclick="selectAllCourses()">
                                <i class="bx bx-check-square me-1"></i>تحديد الكل
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="deselectAllCourses()">
                                <i class="bx bx-square me-1"></i>إلغاء تحديد الكل
                            </button>
                            <button type="submit" name="action" value="update_courses" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>تحديث الدورات المختارة
                            </button>
                            <button type="submit" name="action" value="approve" class="btn btn-success">
                                <i class="bx bx-check me-1"></i>موافقة على الطلب
                            </button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger">
                                <i class="bx bx-x me-1"></i>رفض الطلب
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="text-center py-4">
                        <i class="bx bx-book-open display-4 text-muted"></i>
                        <p class="text-muted mt-2">لم يتم تحديد دورات لهذا الطلب</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Status and Actions -->
            <!-- <div class="col-lg-4"> -->
            <!-- Current Status -->
            <!-- <div class="detail-card"> -->

            <!-- </div> -->

            <!-- Application Timeline -->
            <div class="detail-card">
                <h5 class="mb-3">
                    <i class="bx bx-time-five text-primary me-2"></i>
                    سجل الأحداث
                </h5>

                <div class="timeline">
                    <div class="timeline-item">
                        <h6 class="mb-1">تم تقديم الطلب</h6>
                        <small class="text-muted">{{ $application->created_at->format('Y/m/d H:i') }}</small>
                    </div>

                    @if($application->updated_at != $application->created_at)
                    <div class="timeline-item">
                        <h6 class="mb-1">آخر تحديث</h6>
                        <small class="text-muted">{{ $application->updated_at->format('Y/m/d H:i') }}</small>
                    </div>
                    @endif

                    @if($application->status === 'registered')
                    <div class="timeline-item">
                        <h6 class="mb-1">تم قبول الطلب</h6>
                        <small class="text-success">مقبول للتسجيل</small>
                    </div>
                    @elseif($application->status === 'waiting')
                    <div class="timeline-item">
                        <h6 class="mb-1">في قائمة الانتظار</h6>
                        <small class="text-warning">بانتظار توفر مقعد</small>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <!-- <div class="detail-card">
                    <h5 class="mb-3">
                        <i class="bx bx-stats text-primary me-2"></i>
                        إحصائيات سريعة
                    </h5>
                    
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number h4 mb-1">{{ count($selectedCourses) }}</div>
                                <small class="text-muted">الدورات</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number h4 mb-1">{{ $application->created_at->diffInDays() }}</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number h4 mb-1">1</div>
                                <small class="text-muted">طالب</small>
                            </div>
                        </div>
                    </div>
                </div> -->
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    function selectAllCourses() {
        document.querySelectorAll('.course-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function deselectAllCourses() {
        document.querySelectorAll('.course-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });

        // Confirmation for status changes
        const forms = document.querySelectorAll('form[action*="register"], form[action*="waiting"], form[action*="unregister"]');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = form.querySelector('button[type="submit"]');
                const action = button.textContent.trim();

                if (!confirm(`هل أنت متأكد من ${action}؟`)) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush