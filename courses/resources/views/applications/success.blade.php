@extends('layouts.app')

@section('title', 'تم تقديم الطلب بنجاح')

@push('styles')
<style>
    .success-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 3rem;
        text-align: center;
        margin: 2rem 0;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
    }
    
    .success-icon {
        font-size: 5rem;
        color: #28a745;
        animation: bounce 2s infinite;
        margin-bottom: 1rem;
    }
    
    @keyframes bounce {
        0%, 20%, 60%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-20px);
        }
        80% {
            transform: translateY(-10px);
        }
    }
    
    .student-id-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        padding: 2rem;
        margin: 2rem 0;
    }
    
    .student-code {
        font-size: 2rem;
        font-weight: bold;
        font-family: 'Courier New', monospace;
        background: rgba(255, 255, 255, 0.2);
        padding: 1rem;
        border-radius: 10px;
        letter-spacing: 2px;
        margin: 1rem 0;
    }
    
    .info-card {
        background: white;
        color: #333;
        border-radius: 15px;
        padding: 1.5rem;
        margin: 1rem 0;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1.5rem;
        border-radius: 25px;
        font-weight: bold;
        margin: 0.5rem 0;
    }
    
    .status-registered {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-waiting {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .action-buttons {
        margin-top: 2rem;
    }
    
    .btn-custom {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid white;
        color: white;
        padding: 0.8rem 2rem;
        border-radius: 25px;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        margin: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .btn-custom:hover {
        background: white;
        color: #667eea;
        transform: translateY(-2px);
    }
    
    .contact-info {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    @if(!session()->has('application_code'))
        <div class="alert alert-warning text-center">
            <h4>لا يمكن الوصول إلى هذه الصفحة مباشرة</h4>
            <p>يرجى تقديم طلب جديد للوصول إلى صفحة التأكيد</p>
            <a href="{{ route('apply') }}" class="btn btn-primary">تقديم طلب جديد</a>
        </div>
    @else
        <div class="success-container">
            <div class="success-icon">
                <i class="bx bx-check-circle"></i>
            </div>
            
            <h1 class="display-4 fw-bold mb-3">تم تقديم طلبك بنجاح!</h1>
            <p class="lead mb-4">شكراً لك على تقديم طلب التسجيل في الدورات التدريبية</p>
            
            <div class="student-id-card">
                <h3 class="mb-3">
                    <i class="bx bx-id-card me-2"></i>
                    رمز الطالب الخاص بك
                </h3>
                <div class="student-code">{{ session('application_code') }}</div>
                <p class="mb-0">
                    <i class="bx bx-info-circle me-1"></i>
                    احتفظ بهذا الرمز للمتابعة واستعلام عن حالة طلبك
                </p>
            </div>

            @if(session('status_message'))
                <div class="info-card">
                    <h5 class="fw-bold mb-3">
                        <i class="bx bx-bell me-2 text-primary"></i>
                        حالة طلبك
                    </h5>
                    
                    @if(strpos(session('status_message'), 'تم تسجيلك بنجاح') !== false)
                        <div class="status-badge status-registered">
                            <i class="bx bx-check me-1"></i>
                            مقبول ومسجل
                        </div>
                        <p class="mt-2 mb-0">{{ session('status_message') }}</p>
                    @else
                        <div class="status-badge status-waiting">
                            <i class="bx bx-time me-1"></i>
                            في قائمة الانتظار
                        </div>
                        <p class="mt-2 mb-0">{{ session('status_message') }}</p>
                        <div class="alert alert-info mt-3 mb-0">
                            <small>
                                <i class="bx bx-info-circle me-1"></i>
                                سيتم تسجيلك تلقائياً عند توفر أماكن، وسيتم إشعارك بذلك
                            </small>
                        </div>
                    @endif
                </div>
            @endif

            <div class="action-buttons">
                <a href="{{ route('application.status', session('application_code')) }}" class="btn-custom">
                    <i class="bx bx-search me-2"></i>
                    تتبع حالة الطلب
                </a>
                <a href="{{ route('apply') }}" class="btn-custom">
                    <i class="bx bx-plus me-2"></i>
                    تقديم طلب جديد
                </a>
            </div>

            <div class="contact-info">
                <h6 class="fw-bold mb-2">
                    <i class="bx bx-support me-2"></i>
                    للاستفسارات والدعم
                </h6>
                <p class="small mb-1">
                    <i class="bx bx-envelope me-1"></i>
                    البريد الإلكتروني: support@courses.com
                </p>
                <p class="small mb-0">
                    <i class="bx bx-phone me-1"></i>
                    الهاتف: 920000000
                </p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="fw-bold text-primary mb-3">
                        <i class="bx bx-question-circle me-2"></i>
                        ماذا بعد؟
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bx bx-check text-success me-2"></i>
                            ستتم مراجعة طلبك من قبل الإدارة
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-check text-success me-2"></i>
                            سيتم إشعارك بحالة الطلب عبر البريد الإلكتروني
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-check text-success me-2"></i>
                            يمكنك تتبع حالة طلبك باستخدام رمز الطالب
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="fw-bold text-primary mb-3">
                        <i class="bx bx-time-five me-2"></i>
                        الأوقات المتوقعة
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bx bx-timer text-warning me-2"></i>
                            مراجعة الطلب: 1-2 يوم عمل
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-timer text-warning me-2"></i>
                            الرد على الطلب: 2-3 أيام عمل
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-timer text-warning me-2"></i>
                            بداية الدورات: حسب الجدول المحدد
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy student code to clipboard functionality
    const studentCode = document.querySelector('.student-code');
    if (studentCode) {
        studentCode.addEventListener('click', function() {
            navigator.clipboard.writeText(this.textContent.trim()).then(function() {
                // Show copied notification
                const originalText = studentCode.innerHTML;
                studentCode.innerHTML = '<i class="bx bx-check"></i> تم النسخ!';
                setTimeout(() => {
                    studentCode.innerHTML = originalText;
                }, 2000);
            });
        });
        
        // Add cursor pointer style
        studentCode.style.cursor = 'pointer';
        studentCode.title = 'انقر للنسخ';
    }
});
</script>
@endpush
@endsection
