@extends('layouts.app')

@section('title', 'تم تقديم الطلب بنجاح')

@push('styles')
<style>
    .success-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        color: white;
        padding: 3rem 2rem;
        text-align: center;
        box-shadow: 0 20px 60px rgba(102,126,234,0.3);
        margin: 2rem 0;
    }
    .success-icon {
        font-size: 4rem;
        animation: bounce 2s infinite;
        color: #4CAF50;
        background: white;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-20px);
        }
        60% {
            transform: translateY(-10px);
        }
    }
    .student-code {
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        padding: 1rem;
        margin: 1.5rem 0;
        border: 2px dashed rgba(255,255,255,0.3);
    }
    .code-text {
        font-size: 2rem;
        font-weight: bold;
        font-family: 'Courier New', monospace;
        letter-spacing: 3px;
    }
    .info-card {
        background: rgba(255,255,255,0.1);
        border-radius: 15px;
        padding: 1.5rem;
        margin: 1rem 0;
        backdrop-filter: blur(10px);
    }
    .btn-home {
        background: white;
        color: #667eea;
        border: none;
        padding: 1rem 2rem;
        border-radius: 10px;
        font-weight: bold;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        margin-top: 2rem;
    }
    .btn-home:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(255,255,255,0.3);
        color: #764ba2;
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="success-container">
                <div class="success-icon">
                    <i class="bx bx-check"></i>
                </div>
                
                <h1 class="display-4 fw-bold mb-3">تم تقديم طلبك بنجاح!</h1>
                <p class="lead mb-4">شكرًا لك {{ $application->student_name }} على التقديم في دوراتنا التدريبية</p>
                
                <!-- Student Code -->
                <div class="student-code">
                    <h4 class="mb-2">رقم الطلب الخاص بك</h4>
                    <div class="code-text">{{ $application->unique_student_code }}</div>
                    <small class="d-block mt-2">احتفظ بهذا الرقم للمراجعة أو الاستفسار</small>
                </div>

                <!-- Application Details -->
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="bx bx-info-circle me-2"></i>
                        تفاصيل الطلب
                    </h5>
                    <div class="row text-start">
                        <div class="col-md-6 mb-2">
                            <strong>الاسم:</strong> {{ $application->student_name }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>البريد الإلكتروني:</strong> {{ $application->student_email }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>الهاتف:</strong> {{ $application->student_phone }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>التخصص:</strong> {{ $application->category->name }}
                        </div>
                        <div class="col-12 mb-2">
                            <strong>حالة الطلب:</strong> 
                            <span class="badge bg-warning text-dark">{{ $application->status_label }}</span>
                        </div>
                    </div>
                </div>

                <!-- Selected Courses -->
                @if($selectedCourses->count() > 0)
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="bx bx-book-open me-2"></i>
                        الدورات المختارة ({{ $selectedCourses->count() }} دورة)
                    </h5>
                    <div class="row">
                        @foreach($selectedCourses as $course)
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-check-circle text-success me-2"></i>
                                <div>
                                    <div class="fw-bold">{{ $course->title }}</div>
                                    <small class="text-light">{{ $course->category->name }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Next Steps -->
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="bx bx-list-check me-2"></i>
                        الخطوات التالية
                    </h5>
                    <div class="text-start">
                        <div class="mb-2">
                            <i class="bx bx-check me-2 text-success"></i>
                            سيتم مراجعة طلبك من قبل الإدارة
                        </div>
                        <div class="mb-2">
                            <i class="bx bx-envelope me-2 text-info"></i>
                            سيتم إشعارك بالقبول عبر البريد الإلكتروني
                        </div>
                        <div class="mb-2">
                            <i class="bx bx-phone me-2 text-warning"></i>
                            يمكنك الاتصال بنا للاستفسار باستخدام رقم الطلب
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="bx bx-support me-2"></i>
                        للاستفسار والدعم
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <i class="bx bx-phone me-2"></i>
                            <strong>الهاتف:</strong> 123-456-7890
                        </div>
                        <div class="col-md-6">
                            <i class="bx bx-envelope me-2"></i>
                            <strong>البريد:</strong> info@courses.com
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4">
                    <a href="{{ route('application.form') }}" class="btn btn-home me-3">
                        <i class="bx bx-plus me-2"></i>
                        تقديم طلب جديد
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-outline-light">
                        <i class="bx bx-home me-2"></i>
                        العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Copy Code Modal -->
<div class="modal fade" id="copyModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <i class="bx bx-check-circle text-success display-4"></i>
                <h5 class="mt-3">تم النسخ بنجاح!</h5>
                <p class="text-muted">تم نسخ رقم الطلب إلى الحافظة</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click functionality to copy student code
    const codeElement = document.querySelector('.code-text');
    if (codeElement) {
        codeElement.style.cursor = 'pointer';
        codeElement.title = 'انقر لنسخ رقم الطلب';
        
        codeElement.addEventListener('click', function() {
            navigator.clipboard.writeText(this.textContent).then(function() {
                // Show copy confirmation modal
                const modal = new bootstrap.Modal(document.getElementById('copyModal'));
                modal.show();
                setTimeout(() => modal.hide(), 2000);
            }).catch(function() {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = codeElement.textContent;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                // Show copy confirmation
                const modal = new bootstrap.Modal(document.getElementById('copyModal'));
                modal.show();
                setTimeout(() => modal.hide(), 2000);
            });
        });
    }
    
    // Auto-hide alert after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endpush
