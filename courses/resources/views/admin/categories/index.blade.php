@extends('layouts.app')

@section('title', 'إدارة التخصصات')

@push('styles')
<style>
    .categories-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 15px;
    }
    
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .category-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: none;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    
    .category-header {
        display: flex;
        justify-content-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    
    .category-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .category-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .category-stats {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        margin: 1rem 0;
    }
    
    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .stat-row:last-child {
        margin-bottom: 0;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .stat-value {
        font-weight: 600;
        color: #495057;
    }
    
    .category-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .category-actions .btn {
        flex: 1;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    
    .add-category-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 2px dashed #dee2e6;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-height: 200px;
        transition: all 0.3s ease;
    }
    
    .add-category-card:hover {
        border-color: #667eea;
        background: linear-gradient(135deg, #f0f4ff 0%, #e6f0ff 100%);
    }
    
    .add-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(102, 126, 234, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    
    .stats-row {
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .header-stat-item {
        text-align: center;
        color: white;
    }
    
    .header-stat-item .number {
        font-size: 1.5rem;
        font-weight: bold;
        display: block;
    }
    
    .header-stat-item .label {
        font-size: 0.875rem;
        opacity: 0.9;
    }
    
    .quick-actions {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    @media (max-width: 768px) {
        .categories-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .category-card {
            padding: 1rem;
        }
        
        .category-actions {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="categories-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h2 fw-bold mb-2">
                        <i class="bx bx-category me-2"></i>
                        إدارة التخصصات
                    </h1>
                    <p class="mb-0 opacity-90">إدارة جميع تخصصات الدورات التدريبية</p>
                </div>
                <div class="col-md-6">
                    <div class="stats-row">
                        <div class="row">
                            <div class="col-4">
                                <div class="header-stat-item">
                                    <span class="number">{{ $categories->count() }}</span>
                                    <span class="label">تخصص</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="header-stat-item">
                                    <span class="number">{{ $totalCourses }}</span>
                                    <span class="label">دورة</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="header-stat-item">
                                    <span class="number">{{ $totalApplications }}</span>
                                    <span class="label">طلب</span>
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

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-0">
                        <i class="bx bx-zap text-primary me-2"></i>
                        إجراءات سريعة
                    </h5>
                </div>
                <div class="col-md-4 text-md-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="bx bx-plus me-2"></i>إضافة تخصص جديد
                    </button>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="categories-grid">
            @foreach($categories as $category)
            <div class="category-card">
                <div class="category-header">
                    <div class="flex-grow-1">
                        <div class="category-icon">
                            <i class="bx bx-book-open"></i>
                        </div>
                        <h3 class="category-title">{{ $category->name }}</h3>
                    </div>
                </div>

                <div class="category-stats">
                    <div class="stat-row">
                        <span class="stat-label">
                            <i class="bx bx-book me-1"></i>عدد الدورات
                        </span>
                        <span class="stat-value">{{ $category->courses->count() }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">
                            <i class="bx bx-user me-1"></i>عدد الطلبات
                        </span>
                        <span class="stat-value">{{ $categoryStats[$category->id]['applications'] ?? 0 }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">
                            <i class="bx bx-check-circle me-1"></i>المقبولين
                        </span>
                        <span class="stat-value text-success">{{ $categoryStats[$category->id]['registered'] ?? 0 }}</span>
                    </div>
                </div>

                <div class="category-actions">
                    <a href="{{ route('admin.courses.index', ['category' => $category->id]) }}" 
                       class="btn btn-outline-primary">
                        <i class="bx bx-list-ul me-1"></i>عرض الدورات
                    </a>
                    <button type="button" 
                            class="btn btn-outline-warning edit-category-btn"
                            data-bs-toggle="modal" 
                            data-bs-target="#editCategoryModal"
                            data-id="{{ $category->id }}"
                            data-name="{{ $category->name }}">
                        <i class="bx bx-edit me-1"></i>تعديل
                    </button>
                    @if($category->courses->count() == 0)
                        <form action="{{ route('admin.categories.destroy', $category) }}" 
                              method="POST" 
                              class="d-inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا التخصص؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bx bx-trash me-1"></i>حذف
                            </button>
                        </form>
                    @else
                        <button type="button" 
                                class="btn btn-outline-danger" 
                                disabled 
                                title="لا يمكن حذف تخصص يحتوي على دورات">
                            <i class="bx bx-trash me-1"></i>حذف
                        </button>
                    @endif
                </div>
            </div>
            @endforeach

            <!-- Add Category Card -->
            <div class="category-card add-category-card" 
                 data-bs-toggle="modal" 
                 data-bs-target="#addCategoryModal"
                 style="cursor: pointer;">
                <div class="add-icon">
                    <i class="bx bx-plus"></i>
                </div>
                <h4 class="text-muted">إضافة تخصص جديد</h4>
                <p class="text-muted mb-0">انقر لإضافة تخصص جديد</p>
            </div>
        </div>

        @if($categories->count() == 0)
            <div class="text-center py-5">
                <i class="bx bx-category display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">لا توجد تخصصات</h4>
                <p class="text-muted">ابدأ بإضافة أول تخصص للدورات</p>
                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bx bx-plus me-2"></i>إضافة التخصص الأول
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-plus-circle text-primary me-2"></i>
                    إضافة تخصص جديد
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_category_name" class="form-label">
                            اسم التخصص <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="add_category_name" 
                               name="name" 
                               placeholder="مثل: تطوير الويب، التصميم الجرافيكي..."
                               required>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>نصائح:</strong>
                        <ul class="mb-0 mt-2">
                            <li>استخدم أسماء واضحة ومفهومة</li>
                            <li>تجنب التكرار مع التخصصات الموجودة</li>
                            <li>يمكن إضافة الدورات للتخصص بعد إنشائه</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-2"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-2"></i>حفظ التخصص
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-edit text-warning me-2"></i>
                    تعديل التخصص
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_category_name" class="form-label">
                            اسم التخصص <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_category_name" 
                               name="name" 
                               required>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bx bx-warning me-2"></i>
                        <strong>تنبيه:</strong> تعديل اسم التخصص سيؤثر على جميع الدورات المرتبطة به.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-2"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bx bx-save me-2"></i>حفظ التعديل
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit category modal handling
    const editButtons = document.querySelectorAll('.edit-category-btn');
    const editForm = document.getElementById('editCategoryForm');
    const editNameInput = document.getElementById('edit_category_name');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.dataset.id;
            const categoryName = this.dataset.name;
            
            editForm.action = `/admin/categories/${categoryId}`;
            editNameInput.value = categoryName;
        });
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Form submission handling
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>جاري الحفظ...';
                submitButton.disabled = true;
            }
        });
    });

    // Add category card click handler
    const addCategoryCard = document.querySelector('.add-category-card');
    if (addCategoryCard) {
        addCategoryCard.addEventListener('click', function() {
            document.getElementById('add_category_name').focus();
        });
    }

    // Auto-focus on modal open
    const addModal = document.getElementById('addCategoryModal');
    const editModal = document.getElementById('editCategoryModal');
    
    addModal.addEventListener('shown.bs.modal', function() {
        document.getElementById('add_category_name').focus();
    });
    
    editModal.addEventListener('shown.bs.modal', function() {
        document.getElementById('edit_category_name').focus();
        document.getElementById('edit_category_name').select();
    });
});
</script>
@endpush
