@extends('layouts.app')

@section('title', 'تعديل طلب التسجيل')

@push('styles')
<style>
    .edit-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 15px;
    }

    .edit-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .course-selection {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .course-checkbox {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        border: 2px solid #e9ecef;
        transition: all 0.2s ease;
    }

    .course-checkbox:hover {
        border-color: #667eea;
        box-shadow: 0 2px 10px rgba(102, 126, 234, 0.1);
    }

    .course-checkbox input[type="checkbox"]:checked + .course-info {
        background: rgba(102, 126, 234, 0.1);
    }

    .course-info {
        transition: background 0.2s ease;
        padding: 0.5rem;
        border-radius: 6px;
    }

    .course-title {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.25rem;
    }

    .course-details {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        color: white;
        font-weight: 600;
    }

    .btn-cancel {
        background: #6c757d;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        margin-left: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="edit-header text-center">
        <h1 class="display-6 fw-bold mb-2">
            <i class="bx bx-edit-alt me-2"></i>تعديل طلب التسجيل
        </h1>
        <p class="lead mb-0">تعديل بيانات ودورات الطالب: {{ $application->student_name }}</p>
    </div>

    <form method="POST" action="{{ route('admin.applications.update', $application) }}">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-8">
                <!-- Student Information -->
                <div class="edit-card">
                    <h5 class="mb-4">
                        <i class="bx bx-user me-2"></i>بيانات الطالب
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="student_name" class="form-label">اسم الطالب</label>
                            <input type="text" 
                                   class="form-control @error('student_name') is-invalid @enderror"
                                   id="student_name" 
                                   name="student_name" 
                                   value="{{ old('student_name', $application->student_name) }}" 
                                   required>
                            @error('student_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label for="student_phone" class="form-label">رقم الهاتف</label>
                            <input type="text" 
                                   class="form-control @error('student_phone') is-invalid @enderror"
                                   id="student_phone" 
                                   name="student_phone" 
                                   value="{{ old('student_phone', $application->student_phone) }}" 
                                   required>
                            @error('student_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" value="{{ $application->student_email }}" readonly>
                            <small class="text-muted">لا يمكن تعديل البريد الإلكتروني</small>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="form-label">رقم الطالب</label>
                            <input type="text" class="form-control" value="{{ $application->unique_student_code }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Course Selection -->
                <div class="edit-card">
                    <h5 class="mb-4">
                        <i class="bx bx-book me-2"></i>الدورات المختارة من قبل الطالب
                    </h5>

                    <div class="form-group">
                        <label class="form-label">التخصص الحالي</label>
                        <input type="text" class="form-control" 
                               value="{{ $application->category ? $application->category->name : 'جميع التخصصات' }}" 
                               readonly>
                        <input type="hidden" name="category_id" value="{{ $application->category_id }}">
                        <small class="text-muted">لا يمكن تغيير التخصص في وضع التعديل</small>
                    </div>

                    <div class="course-selection">
                        <label class="form-label">الدورات المختارة</label>
                        <p class="text-muted mb-3">يمكنك إلغاء تحديد الدورات التي لا يرغب الطالب في التسجيل بها</p>
                        
                        <div id="selected-courses-container">
                            @php
                                $selectedCourseIds = $application->selected_courses ?? [];
                                $selectedCourses = \App\Models\Course::whereIn('id', $selectedCourseIds)->get();
                            @endphp

                            @if($selectedCourses && $selectedCourses->count() > 0)
                                @foreach($selectedCourses as $course)
                                    <div class="course-checkbox">
                                        <label class="d-flex align-items-start">
                                            <input type="checkbox" 
                                                   name="selected_courses[]" 
                                                   value="{{ $course->id }}"
                                                   class="me-3 mt-1"
                                                   {{ in_array($course->id, old('selected_courses', $selectedCourseIds)) ? 'checked' : '' }}>
                                            <div class="course-info flex-grow-1">
                                                <div class="course-title">{{ $course->title }}</div>
                                                <div class="course-details">
                                                    @if($course->category)
                                                        <span class="badge bg-primary me-2">{{ $course->category->name }}</span>
                                                    @endif
                                                    @if($course->start_time && $course->end_time)
                                                        <i class="bx bx-time me-1"></i>
                                                        {{ \Carbon\Carbon::parse($course->start_time)->format('Y/m/d H:i') }} - 
                                                        {{ \Carbon\Carbon::parse($course->end_time)->format('Y/m/d H:i') }}
                                                    @endif
                                                    @if($course->price)
                                                        <span class="ms-3">
                                                            <i class="bx bx-money me-1"></i>{{ $course->price }} ريال
                                                        </span>
                                                    @endif
                                                    <div class="mt-1">
                                                        <small class="text-muted">{{ $course->description }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-warning">
                                    <i class="bx bx-info-circle me-2"></i>
                                    لا توجد دورات مختارة لهذا الطالب
                                </div>
                            @endif
                        </div>
                        
                        @error('selected_courses')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror

                        <div class="mt-3 p-3 bg-light rounded">
                            <h6 class="mb-2">
                                <i class="bx bx-info-circle me-1"></i>ملاحظة مهمة:
                            </h6>
                            <ul class="small mb-0 text-muted">
                                <li>يمكنك إلغاء تحديد أي دورة لإزالتها من طلب الطالب</li>
                                <li>يجب أن يبقى هناك دورة واحدة على الأقل مختارة</li>
                                <li>إلغاء تحديد دورة لن يؤثر على الطلاب الآخرين</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Status -->
                <div class="edit-card">
                    <h5 class="mb-4">
                        <i class="bx bx-info-circle me-2"></i>حالة الطلب
                    </h5>

                    <div class="form-group">
                        <label for="status" class="form-label">الحالة</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" 
                                name="status" 
                                required>
                            <option value="unregistered" {{ old('status', $application->status) === 'unregistered' ? 'selected' : '' }}>
                                غير مسجل
                            </option>
                            <option value="registered" {{ old('status', $application->status) === 'registered' ? 'selected' : '' }}>
                                مسجل
                            </option>
                            <option value="waiting" {{ old('status', $application->status) === 'waiting' ? 'selected' : '' }}>
                                قائمة انتظار
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <p class="small text-muted">
                            <i class="bx bx-info-circle me-1"></i>
                            تاريخ الإنشاء: {{ $application->created_at->format('Y/m/d H:i') }}
                        </p>
                        <p class="small text-muted">
                            <i class="bx bx-edit me-1"></i>
                            آخر تحديث: {{ $application->updated_at->format('Y/m/d H:i') }}
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="edit-card">
                    <h5 class="mb-4">
                        <i class="bx bx-cog me-2"></i>الإجراءات
                    </h5>

                    <button type="submit" class="btn btn-save w-100 mb-3">
                        <i class="bx bx-save me-2"></i>حفظ التغييرات
                    </button>

                    <a href="{{ route('admin.applications.index') }}" class="btn btn-cancel w-100">
                        <i class="bx bx-x me-2"></i>إلغاء
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Course selection validation
        const form = document.querySelector('form');
        const checkboxes = document.querySelectorAll('input[name="selected_courses[]"]');
        
        form.addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('input[name="selected_courses[]"]:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('يجب اختيار دورة واحدة على الأقل');
                return false;
            }
        });
        
        // Visual feedback for course selection
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const courseBox = this.closest('.course-checkbox');
                if (this.checked) {
                    courseBox.style.opacity = '1';
                    courseBox.style.backgroundColor = 'rgba(102, 126, 234, 0.1)';
                } else {
                    courseBox.style.opacity = '0.7';
                    courseBox.style.backgroundColor = '';
                }
            });
        });
    });
</script>
@endpush
