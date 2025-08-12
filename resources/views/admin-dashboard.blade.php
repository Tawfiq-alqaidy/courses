@extends('layouts.app')

@section('title', 'لوحة تحكم الإدارة')

@push('styles')
<style>
    /* CSS Variables for consistent spacing */
    :root {
        --spacing-xs: 0.5rem;
        /* 8px */
        --spacing-sm: 1rem;
        /* 16px */
        --spacing-md: 1.5rem;
        /* 24px */
        --spacing-lg: 2rem;
        /* 32px */
        --spacing-xl: 3rem;
        /* 48px */
        --border-radius: 15px;
        --border-radius-sm: 10px;
        --shadow-light: 0 5px 20px rgba(0, 0, 0, 0.1);
        --shadow-hover: 0 15px 35px rgba(0, 0, 0, 0.15);
        --transition: all 0.3s ease;
    }

    /* Reset and base styles */
    .container-fluid {
        padding-left: var(--spacing-md);
        padding-right: var(--spacing-md);
    }

    .container {
        padding-left: var(--spacing-sm);
        padding-right: var(--spacing-sm);
    }

    /* Dashboard Header */
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: var(--spacing-lg) 0;
        margin-bottom: var(--spacing-lg);
        border-radius: var(--border-radius);
    }

    /* Statistics Cards */
    .stat-card {
        background: white;
        border-radius: var(--border-radius);
        padding: var(--spacing-md);
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        border: none;
        height: 100%;
        margin-bottom: var(--spacing-sm);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: var(--border-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-bottom: var(--spacing-sm);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: var(--spacing-xs);
        color: #2c3e50;
        line-height: 1.2;
    }

    .stat-label {
        color: #6c757d;
        font-weight: 500;
        margin-bottom: var(--spacing-xs);
        font-size: 1rem;
    }

    .stat-change {
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: var(--spacing-xs);
    }

    /* Chart Cards */
    .chart-card {
        background: white;
        border-radius: var(--border-radius);
        padding: var(--spacing-md);
        box-shadow: var(--shadow-light);
        margin-bottom: var(--spacing-lg);
    }

    .chart-card h4 {
        margin-bottom: var(--spacing-md);
    }

    /* Recent Applications */
    .recent-applications {
        background: white;
        border-radius: var(--border-radius);
        padding: var(--spacing-md);
        box-shadow: var(--shadow-light);
        margin-bottom: var(--spacing-sm);
    }

    .recent-applications h4 {
        margin-bottom: var(--spacing-md);
    }

    .application-item {
        padding: var(--spacing-sm);
        border-radius: var(--border-radius-sm);
        margin-bottom: var(--spacing-xs);
        transition: all 0.2s ease;
    }

    .application-item:hover {
        background: #f8f9fa;
    }

    .application-item:last-child {
        margin-bottom: 0;
    }

    .application-item .row {
        align-items: center;
    }

    .application-item h6 {
        margin-bottom: var(--spacing-xs);
    }

    .application-item small {
        margin: 0;
    }

    /* Status Badge */
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
    }

    /* Quick Actions */
    .quick-actions {
        background: white;
        border-radius: var(--border-radius);
        padding: var(--spacing-md);
        box-shadow: var(--shadow-light);
        margin-bottom: var(--spacing-sm);
    }

    .quick-actions h4 {
        margin-bottom: var(--spacing-md);
    }

    .action-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.75rem var(--spacing-md);
        border-radius: var(--border-radius-sm);
        font-weight: 600;
        transition: var(--transition);
        margin-bottom: var(--spacing-xs);
        width: 100%;
        text-align: left;
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .action-btn:hover {
        transform: translateX(-5px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        color: white;
        text-decoration: none;
    }

    .action-btn:last-child {
        margin-bottom: 0;
    }

    /* Welcome Section */
    .welcome-section {
        background: rgba(255, 255, 255, 0.1);
        border-radius: var(--border-radius);
        padding: var(--spacing-md);
        margin-bottom: var(--spacing-sm);
        backdrop-filter: blur(10px);
    }

    .welcome-section h1 {
        margin-bottom: var(--spacing-xs);
    }

    .welcome-section p {
        margin-bottom: 0;
    }

    /* Grid Spacing */
    .row.g-4 {
        margin-bottom: var(--spacing-md);
    }

    .row:not(.g-4) {
        margin-bottom: var(--spacing-sm);
    }

    /* Section Spacing */
    .statistics-section {
        margin-bottom: var(--spacing-lg);
    }

    .content-section {
        margin-bottom: var(--spacing-lg);
    }

    .content-section:last-child {
        margin-bottom: 0;
    }

    /* System Info */
    .quick-actions .bg-light {
        margin-top: var(--spacing-md);
        padding: var(--spacing-sm);
        border-radius: var(--border-radius-sm);
    }

    .quick-actions .bg-light h6 {
        margin-bottom: var(--spacing-sm);
    }

    .quick-actions .bg-light .row>div {
        margin-bottom: var(--spacing-xs);
    }

    /* Empty State */
    .text-center.py-5 {
        padding: var(--spacing-xl) var(--spacing-sm) !important;
    }

    .text-center.py-5 i {
        margin-bottom: var(--spacing-sm);
    }

    .text-center.py-5 h5 {
        margin-top: var(--spacing-sm);
        margin-bottom: var(--spacing-xs);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        :root {
            --spacing-xs: 0.375rem;
            /* 6px */
            --spacing-sm: 0.75rem;
            /* 12px */
            --spacing-md: 1rem;
            /* 16px */
            --spacing-lg: 1.5rem;
            /* 24px */
            --spacing-xl: 2rem;
            /* 32px */
        }

        .container-fluid {
            padding-left: var(--spacing-sm);
            padding-right: var(--spacing-sm);
        }

        .stat-number {
            font-size: 2rem;
        }

        .dashboard-header {
            padding: var(--spacing-md) 0;
            margin-bottom: var(--spacing-md);
        }

        .stat-card,
        .chart-card,
        .recent-applications,
        .quick-actions {
            margin-bottom: var(--spacing-sm);
        }

        .action-btn {
            margin-bottom: var(--spacing-xs);
        }

        /* Reduce padding on mobile */
        .stat-card,
        .chart-card,
        .recent-applications,
        .quick-actions {
            padding: var(--spacing-sm);
        }

        .application-item {
            padding: var(--spacing-xs);
        }

        /* Stack elements more efficiently */
        .application-item .row>div {
            margin-bottom: var(--spacing-xs);
        }

        .application-item .row>div:last-child {
            margin-bottom: 0;
        }
    }

    @media (max-width: 576px) {
        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }

        .stat-number {
            font-size: 1.75rem;
        }

        .welcome-section h1 {
            font-size: 1.5rem;
        }

        .action-btn {
            font-size: 0.9rem;
            padding: var(--spacing-xs) var(--spacing-sm);
        }
    }

    /* Print Styles */
    @media print {

        .dashboard-header,
        .quick-actions,
        .action-btn {
            display: none !important;
        }

        .stat-card,
        .chart-card,
        .recent-applications {
            box-shadow: none !important;
            border: 1px solid #ddd;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="welcome-section">
                        <h1 class="h2 fw-bold mb-2">
                            <i class="bx bx-tachometer me-2"></i>
                            مرحبًا {{ Auth::user()->name }}
                        </h1>
                        <p class="mb-0 opacity-90">
                            لوحة تحكم إدارة الدورات التدريبية
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light">
                            <i class="bx bx-log-out me-2"></i>تسجيل الخروج
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <section class="statistics-section">
            <div class="row g-4">
                <!-- Total Applications -->
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);">
                            <i class="bx bx-file-blank"></i>
                        </div>
                        <div class="stat-number">{{ $stats['total_applications'] ?? 0 }}</div>
                        <div class="stat-label">إجمالي الطلبات</div>
                        <div class="stat-change text-success">
                            <i class="bx bx-up-arrow-alt me-1"></i>
                            +{{ $stats['new_applications_today'] ?? 0 }} اليوم
                        </div>
                    </div>
                </div>

                <!-- Pending Applications -->
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #FF9800 0%, #f57c00 100%);">
                            <i class="bx bx-time-five"></i>
                        </div>
                        <div class="stat-number">{{ $stats['pending_applications'] ?? 0 }}</div>
                        <div class="stat-label">طلبات في الانتظار</div>
                        <div class="stat-change text-warning">
                            <i class="bx bx-time me-1"></i>
                            تحتاج مراجعة
                        </div>
                    </div>
                </div>

                <!-- Total Courses -->
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #2196F3 0%, #1976d2 100%);">
                            <i class="bx bx-book-open"></i>
                        </div>
                        <div class="stat-number">{{ $stats['total_courses'] ?? 0 }}</div>
                        <div class="stat-label">إجمالي الدورات</div>
                        <div class="stat-change text-info">
                            <i class="bx bx-category me-1"></i>
                            {{ $stats['total_categories'] ?? 0 }} تخصص
                        </div>
                    </div>
                </div>

                <!-- Approved Applications -->
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #9C27B0 0%, #7b1fa2 100%);">
                            <i class="bx bx-check-circle"></i>
                        </div>
                        <div class="stat-number">{{ $stats['approved_applications'] ?? 0 }}</div>
                        <div class="stat-label">طلبات مقبولة</div>
                        <div class="stat-change text-success">
                            <i class="bx bx-trending-up me-1"></i>
                            {{ $stats['approval_rate'] ?? 0 }}% معدل القبول
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="content-section">
            <div class="row">
                <!-- Recent Applications -->
                <div class="col-lg-8">
                    <div class="recent-applications">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold mb-0">
                                <i class="bx bx-list-ul text-primary me-2"></i>
                                آخر الطلبات
                            </h4>
                            <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-primary btn-sm">
                                عرض الكل
                                <i class="bx bx-chevron-left ms-1"></i>
                            </a>
                        </div>

                        @if($recentApplications && $recentApplications->count() > 0)
                        @foreach($recentApplications as $application)
                        <div class="application-item border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-1">{{ $application->student_name }}</h6>
                                    <small class="text-muted">
                                        <i class="bx bx-envelope me-1"></i>{{ $application->student_email }}
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-primary">{{ $application->category->name }}</span>
                                </div>
                                <div class="col-md-2">
                                    <span class="status-badge 
                                            @if($application->status === 'unregistered') bg-warning text-dark
                                            @elseif($application->status === 'registered') bg-success
                                            @else bg-secondary @endif">
                                        {{ $application->status_label }}
                                    </span>
                                </div>
                                <div class="col-md-1 text-end">
                                    <a href="{{ route('admin.applications.show', $application) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="text-center py-5">
                            <i class="bx bx-file-blank display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">لا توجد طلبات حديثة</h5>
                            <p class="text-muted">ستظهر الطلبات الجديدة هنا</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-lg-4">
                    <div class="quick-actions">
                        <h4 class="fw-bold mb-4">
                            <i class="bx bx-bolt text-primary me-2"></i>
                            إجراءات سريعة
                        </h4>

                        <a href="{{ route('admin.applications.index') }}" class="action-btn">
                            <i class="bx bx-list-ul me-2"></i>
                            إدارة الطلبات
                            @if(($stats['pending_applications'] ?? 0) > 0)
                            <span class="badge bg-warning text-dark ms-2">{{ $stats['pending_applications'] }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.courses.index') }}" class="action-btn">
                            <i class="bx bx-book-open me-2"></i>
                            إدارة الدورات
                        </a>

                        <a href="{{ route('admin.categories.index') }}" class="action-btn">
                            <i class="bx bx-category me-2"></i>
                            إدارة التخصصات
                        </a>

                        <a href="{{ route('admin.courses.create') }}" class="action-btn">
                            <i class="bx bx-plus me-2"></i>
                            إضافة دورة جديدة
                        </a>

                        <!-- System Info -->
                        <!-- <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="fw-bold mb-3">معلومات النظام</h6>
                            <div class="row text-sm">
                                <div class="col-6 mb-2">
                                    <strong>PHP:</strong> {{ PHP_VERSION }}
                                </div>
                                <div class="col-6 mb-2">
                                    <strong>Laravel:</strong> {{ app()->version() }}
                                </div>
                                <div class="col-12 mb-2">
                                    <strong>آخر تسجيل دخول:</strong><br>
                                    <small class="text-muted">{{ Auth::user()->last_login_at ?? 'لم يسجل من قبل' }}</small>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-refresh page every 5 minutes to get latest data
        setTimeout(() => {
            location.reload();
        }, 300000); // 5 minutes

        // Add real-time updates for stats (optional)
        function updateStats() {
            fetch('{{ route("admin.dashboard") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (response.ok) {
                    // Stats updated, you could update specific elements here
                    console.log('Stats refreshed');
                }
            }).catch(error => {
                console.log('Stats refresh failed:', error);
            });
        }

        // Refresh stats every minute
        setInterval(updateStats, 60000);
    });
</script>
@endpush