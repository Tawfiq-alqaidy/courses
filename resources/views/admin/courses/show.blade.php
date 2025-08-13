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
        .stats-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
        }
        .stat-card.registered {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .stat-card.unregistered {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .stat-card.waiting {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .applications-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 1rem;
        }
        .table td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
        }
        .table tbody tr {
            border-bottom: 1px solid #f1f3f4;
            transition: all 0.2s ease;
        }
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-registered {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-unregistered {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .status-waiting {
            background: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
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

        <!-- Applications Statistics -->
        <div class="course-card">
            <h3 class="section-title">إحصائيات طلبات التسجيل</h3>
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['total_applications'] }}</div>
                    <div class="stat-label">إجمالي الطلبات</div>
                </div>
                <div class="stat-card registered">
                    <div class="stat-number">{{ $stats['registered'] }}</div>
                    <div class="stat-label">مسجل</div>
                </div>
                <div class="stat-card unregistered">
                    <div class="stat-number">{{ $stats['unregistered'] }}</div>
                    <div class="stat-label">غير مسجل</div>
                </div>
                <div class="stat-card waiting">
                    <div class="stat-number">{{ $stats['waiting'] }}</div>
                    <div class="stat-label">قائمة الانتظار</div>
                </div>
            </div>
        </div>

        <!-- Applications List -->
        @if($applications->count() > 0)
        <div class="applications-table">
            <div class="d-flex justify-content-between align-items-center p-3 bg-light">
                <h5 class="mb-0">الطلاب المتقدمون لهذه الدورة</h5>
                <span class="badge bg-primary">{{ $applications->total() }} طالب</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>رقم الطالب</th>
                            <th>اسم الطالب</th>
                            <th>البريد الإلكتروني</th>
                            <th>الهاتف</th>
                            <th>التخصص</th>
                            <th>الحالة</th>
                            <th>تاريخ التقديم</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ $application->unique_student_code }}</strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                        {{ strtoupper(substr($application->student_name, 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold">{{ $application->student_name }}</span>
                                </div>
                            </td>
                            <td>{{ $application->student_email }}</td>
                            <td>{{ $application->student_phone }}</td>
                            <td>
                                <span class="badge bg-info">{{ $application->category?->name ?? 'جميع التخصصات' }}</span>
                            </td>
                            <td>
                                <span class="status-badge 
                                    @if($application->status === 'registered') status-registered
                                    @elseif($application->status === 'unregistered') status-unregistered
                                    @else status-waiting @endif">
                                    @if($application->status === 'registered') مسجل
                                    @elseif($application->status === 'unregistered') غير مسجل
                                    @else قائمة الانتظار @endif
                                </span>
                            </td>
                            <td>{{ $application->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.applications.show', $application) }}" 
                                   class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($applications->hasPages())
            <div class="p-3">
                {{ $applications->links() }}
            </div>
            @endif
        </div>
        @else
        <div class="course-card text-center">
            <h5 class="text-muted">لم يتقدم أي طالب لهذه الدورة بعد</h5>
            <p class="text-muted">عندما يتقدم الطلاب لهذه الدورة، ستظهر معلوماتهم هنا.</p>
        </div>
        @endif
    </div>
@endsection
