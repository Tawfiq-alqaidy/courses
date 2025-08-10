@extends('layouts.app')

@section('title', 'تفاصيل الدورة')

@push('styles')
    <style>
        .course-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 1rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        .course-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .section-title {
            color: #495057;
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
        }
        .info-row {
            margin-bottom: 1rem;
        }
        .info-label {
            font-weight: 700;
            color: #333;
            width: 160px;
            display: inline-block;
        }
        .info-value {
            color: #555;
            display: inline-block;
        }
        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.5);
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="course-header">
            <h1 class="mb-0">{{ $course->title }}</h1>
            <small>التخصص: {{ $course->category->name ?? 'غير محدد' }}</small>
        </div>

        <div class="course-card">
            <h3 class="section-title">الوصف</h3>
            <p>{{ $course->description }}</p>

            <h3 class="section-title">معلومات السعة</h3>
            <div class="info-row">
                <span class="info-label">الحد الأقصى للمتدربين:</span>
                <span class="info-value">{{ $course->capacity_limit }}</span>
            </div>

            <h3 class="section-title">المواعيد</h3>
            <div class="info-row">
                <span class="info-label">تاريخ ووقت البداية:</span>
                <span class="info-value">
                {{ $course->start_time ? $course->start_time->format('Y-m-d H:i') : 'غير محدد' }}
            </span>
            </div>
            <div class="info-row">
                <span class="info-label">تاريخ ووقت النهاية:</span>
                <span class="info-value">
                {{ $course->end_time ? $course->end_time->format('Y-m-d H:i') : 'غير محدد' }}
            </span>
            </div>

            <a href="{{ route('admin.courses.index') }}" class="btn-back mt-4 d-inline-block">
                <i class="bx bx-arrow-back me-2"></i> العودة إلى القائمة
            </a>
        </div>
    </div>
@endsection
