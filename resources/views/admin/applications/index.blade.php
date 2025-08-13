@extends('layouts.app')

@section('title', 'إدارة طلبات التسجيل')

@push('styles')
<style>
    .applications-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 15px;
    }

    .filter-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
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

    .action-buttons .btn {
        margin: 0 0.25rem;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
    }

    .bulk-actions {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        display: none;
    }

    .bulk-actions.show {
        display: block;
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .courses-list {
        max-width: 200px;
    }

    .course-tag {
        display: inline-block;
        background: #e9ecef;
        color: #495057;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        margin: 0.125rem;
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

    /* Enhanced Pagination Styling */
    .pagination-wrapper {
        background: #f8f9fa;
        padding: 1.5rem;
        border-top: 1px solid #e9ecef;
        border-radius: 0 0 15px 15px;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    .pagination {
        margin: 0;
        justify-content: center;
    }

    .page-link {
        border: 1px solid #dee2e6;
        color: #495057;
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .page-link:hover {
        background: #667eea;
        border-color: #667eea;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
    }

    .page-item.disabled .page-link {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #6c757d;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .student-info {
            flex-direction: column;
            gap: 0.5rem;
            text-align: center;
        }

        .action-buttons .btn {
            margin-bottom: 0.25rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .courses-list {
            max-width: 150px;
        }

        .pagination-wrapper {
            padding: 1rem;
        }

        .pagination-info {
            text-align: center;
            margin-bottom: 0.75rem;
        }

        .pagination {
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        .page-link {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="applications-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h2 fw-bold mb-2">
                        إدارة طلبات التسجيل
                    </h1>
                    <p class="mb-0 opacity-90">مراجعة وإدارة جميع طلبات التسجيل في الدورات</p>
                </div>
                <div class="col-md-6">
                    <div class="stats-row">
                        <div class="row">
                            <div class="col-4">
                                <div class="stat-item">
                                    <span class="number">{{ $stats['total'] }}</span>
                                    <span class="label">إجمالي</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <span class="number">{{ $stats['unregistered'] }}</span>
                                    <span class="label">في الانتظار</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <span class="number">{{ $stats['registered'] }}</span>
                                    <span class="label">مقبول</span>
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

        <!-- Filters -->
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.applications.index') }}" class="row align-items-end g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">البحث</label>
                    <input type="text"
                        name="search"
                        class="form-control"
                        placeholder="اسم الطالب، البريد، أو رقم الطالب..."
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
                        <option value="unregistered" {{ request('status') == 'unregistered' ? 'selected' : '' }}>في الانتظار</option>
                        <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>مقبول</option>
                        <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>قائمة انتظار</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">ترتيب حسب</label>
                    <select name="sort" class="form-select">
                        <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>تاريخ التقديم</option>
                        <option value="student_name" {{ request('sort') == 'student_name' ? 'selected' : '' }}>اسم الطالب</option>
                        <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>الحالة</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search me-1"></i>بحث
                        </button>
                        <!-- <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-refresh me-1"></i>إعادة تعيين
                        </a> -->
                        <button type="button" class="btn btn-success" onclick="exportApplications()">
                            <i class="bx bx-export me-1"></i>Export to Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulkActions">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="fw-semibold">
                        <span id="selectedCount">0</span> طلب محدد
                    </span>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-success btn-sm" onclick="bulkAction('registered')">
                        <i class="bx bx-check me-1"></i>قبول المحدد
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="bulkAction('waiting')">
                        <i class="bx bx-time me-1"></i>قائمة انتظار
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="bulkDelete()">
                        <i class="bx bx-trash me-1"></i>حذف المحدد
                    </button>
                </div>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="applications-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>الطالب</th>
                            <th>التخصص</th>
                            <th>الدورات المختارة</th>
                            <th>رقم الطلب</th>
                            <th>الحالة</th>
                            <th>تاريخ التقديم</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input application-checkbox"
                                    value="{{ $application->id }}">
                            </td>
                            <td>
                                <div class="student-info">
                                    <div class="student-avatar">
                                        {{ mb_substr($application->student_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $application->student_name }}</div>
                                        <div class="text-muted small">
                                            <i class="bx bx-envelope me-1"></i>{{ $application->student_email }}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="bx bx-phone me-1"></i>{{ $application->student_phone }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $application->category?->name ?? 'جميع التخصصات' }}</span>
                            </td>
                            <td>
                                <div class="courses-list">
                                    @if(isset($application->loadedCourses) && $application->loadedCourses->count() > 0)
                                    @foreach($application->loadedCourses as $course)
                                    <span class="course-tag">{{ $course->title }}</span>
                                    @endforeach
                                    @else
                                    <span class="text-muted">لا توجد دورات محددة</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <code class="small">{{ $application->unique_student_code }}</code>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $application->status }}">
                                    {{ $application->status_label }}
                                </span>
                                @if($application->status === 'waiting')
                                <div class="small text-muted mt-1">
                                    <i class="bx bx-list-ol"></i>
                                    موضع {{ $application->getWaitingListPosition() }} في قائمة الانتظار
                                </div>
                                @endif
                            </td>
                            <td>
                                <div class="small">
                                    {{ $application->created_at->format('Y/m/d') }}
                                    <div class="text-muted">{{ $application->created_at->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.applications.show', $application) }}"
                                        class="btn btn-outline-primary btn-sm"
                                        title="عرض التفاصيل">
                                        <i class="bx bx-show"></i>
                                    </a>

                                    <a href="{{ route('admin.applications.edit', $application) }}"
                                        class="btn btn-outline-info btn-sm"
                                        title="تعديل الطلب">
                                        <i class="bx bx-edit"></i>
                                    </a>

                                    @if($application->status === 'unregistered')
                                    <button class="btn btn-outline-success btn-sm"
                                        onclick="updateStatus({{ $application->id }}, 'register')"
                                        title="قبول الطلب">
                                        <i class="bx bx-check"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm"
                                        onclick="updateStatus({{ $application->id }}, 'waiting')"
                                        title="قائمة انتظار">
                                        <i class="bx bx-time"></i>
                                    </button>
                                    @elseif($application->status === 'waiting')
                                    <button class="btn btn-outline-info btn-sm"
                                        onclick="promoteFromWaiting({{ $application->id }})"
                                        title="ترقية من قائمة الانتظار">
                                        <i class="bx bx-up-arrow-alt"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm"
                                        onclick="updateStatus({{ $application->id }}, 'unregister')"
                                        title="إعادة للمراجعة">
                                        <i class="bx bx-undo"></i>
                                    </button>
                                    @elseif($application->status === 'registered')
                                    <button class="btn btn-outline-secondary btn-sm"
                                        onclick="updateStatus({{ $application->id }}, 'unregister')"
                                        title="إلغاء التسجيل (سيرقى طلاب الانتظار تلقائياً)">
                                        <i class="bx bx-undo"></i>
                                    </button>
                                    @endif

                                    <button class="btn btn-outline-danger btn-sm"
                                        onclick="deleteApplication({{ $application->id }})"
                                        title="حذف الطلب">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bx bx-file-blank display-1 text-muted"></i>
                                <h5 class="mt-3 text-muted">لا توجد طلبات</h5>
                                <p class="text-muted">لم يتم العثور على طلبات تطابق معايير البحث</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($applications->hasPages())
            <div class="pagination-wrapper">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="pagination-info">
                            <span>عرض {{ $applications->firstItem() ?? 0 }} إلى {{ $applications->lastItem() ?? 0 }} من أصل {{ $applications->total() }} نتيجة</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end justify-content-center">
                            {{ $applications->appends(request()->query())->links('custom.pagination') }}
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="pagination-wrapper">
                <div class="pagination-info text-center">
                    <span>عرض {{ $applications->count() }} من أصل {{ $applications->count() }} نتيجة</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Confirmation Modals -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الإجراء</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage">هل أنت متأكد من هذا الإجراء؟</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="confirmAction">تأكيد</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const applicationCheckboxes = document.querySelectorAll('.application-checkbox');
        const bulkActionsDiv = document.getElementById('bulkActions');
        const selectedCountSpan = document.getElementById('selectedCount');

        // Select All functionality
        selectAllCheckbox?.addEventListener('change', function() {
            applicationCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });

        // Individual checkbox functionality
        applicationCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActions);
        });

        function updateBulkActions() {
            const selectedCheckboxes = document.querySelectorAll('.application-checkbox:checked');
            const count = selectedCheckboxes.length;

            selectedCountSpan.textContent = count;

            if (count > 0) {
                bulkActionsDiv.classList.add('show');
            } else {
                bulkActionsDiv.classList.remove('show');
            }

            // Update select all checkbox state
            if (selectAllCheckbox) {
                selectAllCheckbox.indeterminate = count > 0 && count < applicationCheckboxes.length;
                selectAllCheckbox.checked = count === applicationCheckboxes.length;
            }
        }
    });

    function updateStatus(applicationId, status) {
        const statusLabels = {
            'register': 'قبول',
            'waiting': 'نقل إلى قائمة الانتظار',
            'unregister': 'إعادة للمراجعة'
        };

        showConfirmModal(
            `هل تريد ${statusLabels[status]} هذا الطلب؟`,
            () => {
                fetch(`/admin/applications/${applicationId}/${status}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        location.reload();
                    })
                    .catch(error => {
                        location.reload();
                        console.error('Error:', error);
                    });
            }
        );
    }

    function promoteFromWaiting(applicationId) {
        console.log(applicationId)
        showConfirmModal(
            'هل تريد ترقية هذا الطالب من قائمة الانتظار؟',
            () => {
                fetch(`/admin/applications/${applicationId}/promote`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        location.reload();
                    })
                    .catch(error => {
                        location.reload();
                        console.error('Error:', error);
                    });
            }
        );
    }

    function deleteApplication(applicationId) {
        showConfirmModal(
            'هل أنت متأكد من حذف هذا الطلب؟ لا يمكن التراجع عن هذا الإجراء.',
            () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('admin.applications.index') }}/${applicationId}`;
                form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
                document.body.appendChild(form);
                form.submit();
            },
            'btn-danger'
        );
    }

    function bulkAction(status) {
        const selectedIds = Array.from(document.querySelectorAll('.application-checkbox:checked'))
            .map(checkbox => checkbox.value);

        if (selectedIds.length === 0) return;

        const statusLabels = {
            'registered': 'قبول',
            'waiting': 'نقل إلى قائمة الانتظار'
        };

        showConfirmModal(
            `هل تريد ${statusLabels[status]} الطلبات المحددة (${selectedIds.length} طلب)؟`,
            () => {
                fetch('{{ route("admin.applications.bulk-update") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            ids: selectedIds,
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        location.reload();
                    })
                    .catch(error => {
                        location.reload();
                        console.error('Error:', error);
                    });
            }
        );
    }

    function bulkDelete() {
        const selectedIds = Array.from(document.querySelectorAll('.application-checkbox:checked'))
            .map(checkbox => checkbox.value);

        if (selectedIds.length === 0) return;

        showConfirmModal(
            `هل أنت متأكد من حذف الطلبات المحددة (${selectedIds.length} طلب)؟ لا يمكن التراجع عن هذا الإجراء.`,
            () => {
                fetch('{{ route("admin.applications.bulk-delete") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            ids: selectedIds
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        location.reload();
                    })
                    .catch(error => {
                        location.reload();
                        console.error('Error:', error);
                    });
            },
            'btn-danger'
        );
    }

    function exportApplications() {
        const params = new URLSearchParams(window.location.search);
        const url = '{{ route("admin.applications.export") }}?' + params.toString();

        fetch(url)
            .then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok.');
                }
                const disposition = res.headers.get('Content-Disposition');
                let filename = 'applications.xlsx'; // Default filename
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    const matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, '');
                    }
                }
                return res.blob().then(blob => ({
                    blob,
                    filename
                }));
            })
            .then(({
                blob,
                filename
            }) => {
                const a = document.createElement('a');
                const objectUrl = window.URL.createObjectURL(blob);
                a.href = objectUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(objectUrl);
            })
            .catch(error => {
                console.error('There has been a problem with your fetch operation:', error);
                // Fallback to original method if fetch fails
                window.location.href = url;
            });
    }

    function showConfirmModal(message, onConfirm, buttonClass = 'btn-primary') {
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        const messageElement = document.getElementById('confirmMessage');
        const confirmButton = document.getElementById('confirmAction');

        messageElement.textContent = message;
        confirmButton.className = `btn ${buttonClass}`;

        confirmButton.onclick = () => {
            modal.hide();
            onConfirm();
        };

        modal.show();
    }
</script>
@endpush