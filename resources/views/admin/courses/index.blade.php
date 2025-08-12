@extends('layouts.app')

@section('title', 'إدارة الدورات')

@push('styles')
<style>
    .courses-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 15px;
    }

    .courses-table {
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

    .course-title {
        font-weight: 600;
        color: #495057;
    }

    .course-description {
        color: #6c757d;
        font-size: 0.9rem;
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .capacity-badge {
        background: #e9ecef;
        color: #495057;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .capacity-full {
        background: #f8d7da;
        color: #721c24;
    }

    .capacity-low {
        background: #fff3cd;
        color: #856404;
    }

    .time-info {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .action-buttons .btn {
        margin: 0 0.25rem;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
    }

    .filter-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .stats-row {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .stat-item {
        text-align: center;
        color: white;
    }

    .stat-item .number {
        font-size: 1.5rem;
        font-weight: bold;
        display: block;
    }

    .stat-item .label {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .course-description {
            max-width: 150px;
        }

        .action-buttons .btn {
            margin-bottom: 0.25rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="courses-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h2 fw-bold mb-2">
                        <i class="bx bx-book-open me-2"></i>
                        إدارة الدورات
                    </h1>
                    <p class="mb-0 opacity-90">إدارة جميع الدورات التدريبية والتحكم في السعة والمواعيد</p>
                </div>
                <div class="col-md-6">
                    <div class="stats-row">
                        <div class="row">
                            <div class="col-4">
                                <div class="stat-item">
                                    <span class="number">{{ $courses->total() }}</span>
                                    <span class="label">إجمالي</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <span class="number">{{ $courses->where('start_time', '>', now())->count() }}</span>
                                    <span class="label">قادمة</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <span class="number">{{ $categories->count() }}</span>
                                    <span class="label">تخصص</span>
                                </div>
                            </div>
                        </div>
                    </div>
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

        <!-- Add Course Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">قائمة الدورات</h4>
            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-2"></i>إضافة دورة جديدة
            </a>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.courses.index') }}" class="row align-items-end g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">البحث</label>
                    <input type="text"
                        name="search"
                        class="form-control"
                        placeholder="عنوان الدورة..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">التخصص</label>
                    <select name="category" class="form-select">
                        <option value="">جميع التخصصات</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">جميع الحالات</option>
                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>قادمة</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>جارية</option>
                        <option value="ended" {{ request('status') == 'ended' ? 'selected' : '' }}>منتهية</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">ترتيب حسب</label>
                    <select name="sort" class="form-select">
                        <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>تاريخ الإنشاء</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>العنوان</option>
                        <option value="start_time" {{ request('sort') == 'start_time' ? 'selected' : '' }}>تاريخ البداية</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search me-1"></i>بحث
                        </button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-refresh me-1"></i>إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Courses Table -->
        <div class="courses-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>الدورة</th>
                            <th>التخصص</th>
                            <th>السعة</th>
                            <th>المسجلين</th>
                            <th>التوقيت</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                        @php
                        $enrolledCount = $course->getCurrentEnrolledCount();
                        $capacityPercentage = (float) $course->capacityPercentage;
                        @endphp
                        <tr>
                            <td>
                                <div>
                                    <div class="course-title">{{ $course->title }}</div>
                                    <div class="course-description">{{ Str::limit($course->description, 80) }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $course->category->name }}</span>
                            </td>
                            <td>
                                <span class="capacity-badge 
                                    @if($capacityPercentage >= 100) capacity-full
                                    @elseif($capacityPercentage >= 80) capacity-low
                                    @endif">
                                    {{ $course->capacity_limit }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">{{ $enrolledCount }}</span>
                                    <div class="progress" style="width: 60px; height: 6px;">
                                        <div class="progress-bar 
                                            @if($capacityPercentage >= 100) bg-danger
                                            @elseif($capacityPercentage >= 80) bg-warning
                                            @else bg-success
                                            @endif"
                                            style="width: {{ min((float)$capacityPercentage, 100) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="time-info">
                                    @if($course->start_time)
                                    <div><i class="bx bx-time me-1"></i>{{ \Carbon\Carbon::parse($course->start_time)->format('Y/m/d') }}</div>
                                    <div><small class="text-muted">{{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }}</small></div>
                                    @else
                                    <span class="text-muted">غير محدد</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if(!$course->start_time)
                                <span class="badge bg-secondary">غير محدد</span>
                                @elseif($course->start_time > now())
                                <span class="badge bg-info">قادمة</span>
                                @elseif($course->end_time && $course->end_time < now())
                                    <span class="badge bg-dark">منتهية</span>
                                    @else
                                    <span class="badge bg-success">جارية</span>
                                    @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.courses.show', $course) }}"
                                        class="btn btn-outline-primary btn-sm"
                                        title="عرض التفاصيل">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <a href="{{ route('admin.courses.edit', $course) }}"
                                        class="btn btn-outline-warning btn-sm"
                                        title="تعديل">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.courses.destroy', $course) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذه الدورة؟ سيتم حذف جميع الطلبات المرتبطة بها.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-outline-danger btn-sm"
                                            title="حذف">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bx bx-book-open display-1 text-muted"></i>
                                <h5 class="mt-3 text-muted">لا توجد دورات</h5>
                                <p class="text-muted">قم بإضافة الدورة الأولى</p>
                                <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                                    <i class="bx bx-plus me-2"></i>إضافة دورة جديدة
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($courses->hasPages())
            <!-- <div class="p-4 border-top">
                {{ $courses->appends(request()->query())->links() }}
            </div> -->
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });

        // Animate progress bars
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.transition = 'width 1s ease';
                bar.style.width = width;
            }, 100);
        });
    });
</script>
@endpush