@extends('layouts.app')

@section('title', 'حالة الطلب - ' . $application->unique_student_code)

@push('styles')
<style>
    .status-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
    }

    .student-info-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .course-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
        border-left: 4px solid #667eea;
    }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .status-registered {
        background: #d4edda;
        color: #155724;
    }

    .status-waiting {
        background: #fff3cd;
        color: #856404;
    }

    .status-unregistered {
        background: #f8d7da;
        color: #721c24;
    }

    .timeline {
        position: relative;
        padding: 1rem 0;
    }

    .timeline-item {
        position: relative;
        padding-left: 3rem;
        margin-bottom: 1.5rem;
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: 0.8rem;
        top: 0.5rem;
        width: 0.8rem;
        height: 0.8rem;
        background: #667eea;
        border-radius: 50%;
    }

    .timeline-item:after {
        content: '';
        position: absolute;
        left: 1.1rem;
        top: 1.3rem;
        width: 2px;
        height: calc(100% - 0.5rem);
        background: #e9ecef;
    }

    .timeline-item:last-child:after {
        display: none;
    }

    .capacity-progress {
        background: #e9ecef;
        border-radius: 10px;
        height: 8px;
        overflow: hidden;
    }

    .capacity-bar {
        height: 100%;
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .capacity-available {
        background: linear-gradient(90deg, #28a745, #20c997);
    }

    .capacity-almost-full {
        background: linear-gradient(90deg, #ffc107, #fd7e14);
    }

    .capacity-full {
        background: linear-gradient(90deg, #dc3545, #e83e8c);
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <div class="status-header">
        <h1 class="display-5 fw-bold mb-3">
            <i class="bx bx-search-alt me-2"></i>
            حالة طلب التسجيل
        </h1>
        <p class="lead mb-0">رمز الطالب: <strong>{{ $application->unique_student_code }}</strong></p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Student Information -->
            <div class="student-info-card">
                <h4 class="fw-bold text-primary mb-3">
                    <i class="bx bx-user me-2"></i>
                    معلومات الطالب
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small text-muted fw-bold">الاسم الكامل</label>
                        <p class="mb-0 fw-semibold">{{ $application->student_name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small text-muted fw-bold">البريد الإلكتروني</label>
                        <p class="mb-0">{{ $application->student_email }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small text-muted fw-bold">رقم الهاتف</label>
                        <p class="mb-0">{{ $application->student_phone }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small text-muted fw-bold">التخصص</label>
                        <p class="mb-0">
                            <span class="badge bg-primary">{{ $application->category?->name ?? 'جميع التخصصات' }}</span>
                        </p>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-bold">حالة الطلب</label>
                        <div>
                            <span class="status-badge status-{{ $application->status }}">
                                <i class="bx bx-{{ $application->status === 'registered' ? 'check' : ($application->status === 'waiting' ? 'time' : 'x') }} me-1"></i>
                                {{ $application->status_label }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <label class="form-label small text-muted fw-bold">تاريخ التقديم</label>
                        <p class="mb-0">{{ $application->created_at->format('Y/m/d H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Selected Courses -->
            <div class="student-info-card">
                <h4 class="fw-bold text-primary mb-3">
                    <i class="bx bx-book-open me-2"></i>
                    الدورات المختارة ({{ count($application->selected_courses ?? []) }})
                </h4>

                @forelse($application->getSelectedCoursesDetails() as $course)
                <div class="course-card">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h5 class="fw-bold mb-2">{{ $course->title }}</h5>
                            <p class="text-muted mb-2">{{ $course->description }}</p>
                            <div class="d-flex flex-wrap gap-3 small">
                                <div>
                                    <i class="bx bx-calendar text-primary me-1"></i>
                                    <strong>البداية:</strong>
                                    {{ $course->start_time ? $course->start_time->format('Y/m/d') : 'غير محدد' }}
                                </div>
                                <div>
                                    <i class="bx bx-time text-primary me-1"></i>
                                    <strong>المدة:</strong>
                                    {{ $course->duration ?? 'غير محددة' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="mb-2">
                                <span class="small text-muted">السعة:</span>
                                <strong>{{ $course->registered_students_count ?? 0 }}/{{ $course->capacity_limit }}</strong>
                            </div>
                            <div class="capacity-progress mb-2">
                                @php
                                $percentage = $course->capacity_limit > 0 ? (($course->registered_students_count ?? 0) / $course->capacity_limit) * 100 : 0;
                                $barClass = $percentage >= 100 ? 'capacity-full' : ($percentage >= 80 ? 'capacity-almost-full' : 'capacity-available');
                                @endphp
                                <div class="capacity-bar {{ $barClass }}" style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                            <div class="small text-center">
                                @if($percentage >= 100)
                                <span class="text-danger fw-bold">مكتملة</span>
                                @elseif($percentage >= 80)
                                <span class="text-warning fw-bold">شبه مكتملة</span>
                                @else
                                <span class="text-success fw-bold">متاحة</span>
                                @endif
                            </div>

                            @if($application->status === 'waiting')
                            <div class="mt-2">
                                <small class="text-muted d-block">موضع في قائمة الانتظار:</small>
                                <strong class="text-warning">#{{ $application->getWaitingListPosition($course->id) }}</strong>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="alert alert-warning">
                    <i class="bx bx-info-circle me-2"></i>
                    لم يتم العثور على دورات مختارة
                </div>
                @endforelse
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="student-info-card">
                <h5 class="fw-bold text-primary mb-3">
                    <i class="bx bx-time-five me-2"></i>
                    تسلسل الطلب
                </h5>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="fw-semibold">تم تقديم الطلب</div>
                        <div class="small text-muted">{{ $application->created_at->format('Y/m/d H:i') }}</div>
                    </div>

                    @if($application->status === 'registered')
                    <div class="timeline-item">
                        <div class="fw-semibold text-success">تم قبول الطلب</div>
                        <div class="small text-muted">تم تسجيلك بنجاح في الدورات</div>
                    </div>
                    @elseif($application->status === 'waiting')
                    <div class="timeline-item">
                        <div class="fw-semibold text-warning">في قائمة الانتظار</div>
                        <div class="small text-muted">سيتم تسجيلك عند توفر أماكن</div>
                    </div>
                    @else
                    <div class="timeline-item">
                        <div class="fw-semibold text-muted">في انتظار المراجعة</div>
                        <div class="small text-muted">سيتم مراجعة طلبك قريباً</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="student-info-card">
                <h5 class="fw-bold text-primary mb-3">
                    <i class="bx bx-cog me-2"></i>
                    إجراءات سريعة
                </h5>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="bx bx-printer me-2"></i>
                        طباعة التفاصيل
                    </button>
                    <a href="{{ route('apply') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-plus me-2"></i>
                        تقديم طلب جديد
                    </a>
                    <button class="btn btn-outline-info" onclick="shareStatus()">
                        <i class="bx bx-share me-2"></i>
                        مشاركة الرابط
                    </button>
                </div>
            </div>

            <!-- Contact Support -->
            <!-- <div class="student-info-card">
                <h6 class="fw-bold text-primary mb-2">
                    <i class="bx bx-support me-2"></i>
                    تحتاج مساعدة؟
                </h6>
                <p class="small text-muted mb-2">تواصل مع فريق الدعم للحصول على المساعدة</p>
                <div class="small">
                    <div class="mb-1">
                        <i class="bx bx-envelope text-primary me-1"></i>
                        support@courses.com
                    </div>
                    <div>
                        <i class="bx bx-phone text-primary me-1"></i>
                        920000000
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</div>

@push('scripts')
<script>
    function shareStatus() {
        if (navigator.share) {
            navigator.share({
                title: 'حالة طلب التسجيل',
                text: 'حالة طلب التسجيل - رمز الطالب: {{ $application->unique_student_code }}',
                url: window.location.href
            });
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(window.location.href).then(function() {
                alert('تم نسخ رابط الصفحة');
            });
        }
    }

    // Auto-refresh page every 5 minutes if status is waiting
    var applicationStatus = '{{ $application->status }}';
    if (applicationStatus === 'waiting') {
        setTimeout(() => {
            location.reload();
        }, 5 * 60 * 1000); // 5 minutes
    }
</script>
@endpush
@endsection