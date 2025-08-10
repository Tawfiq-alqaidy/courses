@extends('layouts.app')

@section('title', 'لوحة تحكم الإدارة')

@push('styles')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 15px;
    }
    
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: none;
        height: 100%;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-bottom: 1rem;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }
    
    .stat-label {
        color: #6c757d;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .stat-change {
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .chart-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    .recent-applications {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .application-item {
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .application-item:hover {
        background: #f8f9fa;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .quick-actions {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .action-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-bottom: 0.5rem;
        width: 100%;
        text-align: left;
    }
    
    .action-btn:hover {
        transform: translateX(-5px);
        box-shadow: 0 5px 15px rgba(102,126,234,0.3);
        color: white;
    }
    
    .welcome-section {
        background: rgba(255,255,255,0.1);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        backdrop-filter: blur(10px);
    }
    
    @media (max-width: 768px) {
        .stat-number {
            font-size: 2rem;
        }
        
        .dashboard-header {
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
        }
        
        .stat-card, .chart-card, .recent-applications, .quick-actions {
            margin-bottom: 1rem;
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
                            لوحة تحكم إدارة الدورات التدريبية - {{ now()->format('Y/m/d') }}
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
        <div class="row g-4 mb-4">
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
                    <div class="mt-4 p-3 bg-light rounded">
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Applications Status Chart -->
        @if(isset($chartData))
        <div class="row mt-4">
            <div class="col-12">
                <div class="chart-card">
                    <h4 class="fw-bold mb-4">
                        <i class="bx bx-bar-chart text-primary me-2"></i>
                        إحصائيات الطلبات
                    </h4>
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="statusChart" width="400" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <canvas id="categoryChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh page every 5 minutes to get latest data
    setTimeout(() => {
        location.reload();
    }, 300000); // 5 minutes

    @if(isset($chartData))
    // Status Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['في الانتظار', 'مقبول', 'في قائمة الانتظار'],
                datasets: [{
                    data: [
                        {{ $chartData['unregistered'] ?? 0 }},
                        {{ $chartData['registered'] ?? 0 }},
                        {{ $chartData['waiting'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#FF9800',
                        '#4CAF50',
                        '#9C27B0'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'حالة الطلبات'
                    }
                }
            }
        });
    }

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['category_names'] ?? []) !!},
                datasets: [{
                    label: 'عدد الطلبات',
                    data: {!! json_encode($chartData['category_counts'] ?? []) !!},
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'الطلبات حسب التخصص'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    @endif

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
