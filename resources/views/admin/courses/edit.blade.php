@extends('layouts.app')

@section('title', 'تعديل دورة')

@push('styles')
    <style>
        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 15px;
        }
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .required {
            color: #dc3545;
        }
        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .capacity-info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 0.5rem;
        }
        .datetime-group {
            display: flex;
            gap: 1rem;
            align-items: end;
        }
        .datetime-group .form-group {
            flex: 1;
        }
        @media (max-width: 768px) {
            .datetime-group {
                flex-direction: column;
                gap: 0.5rem;
            }
            .form-card {
                padding: 1.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="form-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h2 fw-bold mb-2">
                            <i class="bx bx-edit me-2"></i>
                            تعديل دورة
                        </h1>
                        <p class="mb-0 opacity-90">تعديل بيانات الدورة التدريبية</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-light">
                            <i class="bx bx-arrow-back me-2"></i>العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5><i class="bx bx-error me-2"></i>يرجى تصحيح الأخطاء التالية:</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" id="courseForm">
                        @csrf
                        @method('PUT')
                        <div class="form-card">
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="bx bx-info-circle text-primary"></i>
                                    المعلومات الأساسية
                                </h4>
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="title" class="form-label">
                                            عنوان الدورة <span class="required">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('title') is-invalid @enderror"
                                               id="title"
                                               name="title"
                                               value="{{ old('title', $course->title) }}"
                                               placeholder="أدخل عنوان الدورة"
                                               required>
                                        @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="category_id" class="form-label">
                                            التخصص <span class="required">*</span>
                                        </label>
                                        <select class="form-select @error('category_id') is-invalid @enderror"
                                                id="category_id"
                                                name="category_id"
                                                required>
                                            <option value="">اختر التخصص</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">
                                        وصف الدورة <span class="required">*</span>
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              name="description"
                                              rows="4"
                                              placeholder="أدخل وصفاً مفصلاً للدورة ومحتواها وأهدافها"
                                              required>{{ old('description', $course->description) }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="bx bx-group text-primary"></i>
                                    إعدادات السعة
                                </h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="capacity_limit" class="form-label">
                                            الحد الأقصى للمتدربين <span class="required">*</span>
                                        </label>
                                        <input type="number"
                                               class="form-control @error('capacity_limit') is-invalid @enderror"
                                               id="capacity_limit"
                                               name="capacity_limit"
                                               value="{{ old('capacity_limit', $course->capacity_limit) }}"
                                               min="1"
                                               max="200"
                                               placeholder="مثل: 20"
                                               required>
                                        @error('capacity_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="capacity-info">
                                    <h6><i class="bx bx-info-circle me-2"></i>معلومات مهمة حول السعة:</h6>
                                    <ul class="mb-0">
                                        <li>يمكن للطلاب التسجيل حتى الوصول للحد الأقصى</li>
                                        <li>الطلاب الإضافيين سيتم وضعهم في قائمة الانتظار</li>
                                        <li>يمكن تعديل السعة لاحقاً حسب الحاجة</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="bx bx-calendar text-primary"></i>
                                    المواعيد والجدولة
                                </h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="start_time" class="form-label">
                                            تاريخ ووقت البداية
                                        </label>
                                        <input type="datetime-local"
                                               class="form-control @error('start_time') is-invalid @enderror"
                                               id="start_time"
                                               name="start_time"
                                               value="{{ old('start_time', optional($course->start_time)->format('Y-m-d\TH:i')) }}">
                                        @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="end_time" class="form-label">
                                            تاريخ ووقت النهاية
                                        </label>
                                        <input type="datetime-local"
                                               class="form-control @error('end_time') is-invalid @enderror"
                                               id="end_time"
                                               name="end_time"
                                               value="{{ old('end_time', optional($course->end_time)->format('Y-m-d\TH:i')) }}">
                                        @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="bx bx-bulb me-2"></i>
                                    <strong>نصائح للمواعيد:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>اترك الحقول فارغة إذا كانت المواعيد غير محددة بعد</li>
                                        <li>يمكن تحديد المواعيد لاحقاً من خلال التعديل</li>
                                        <li>تأكد من أن وقت النهاية بعد وقت البداية</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="submit" class="btn-save me-3">
                                        <i class="bx bx-save me-2"></i>
                                        حفظ التعديلات
                                    </button>
                                    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-x me-2"></i>إلغاء
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const capacityInput = document.getElementById('capacity_limit');
            const form = document.getElementById('courseForm');

            startTimeInput.addEventListener('change', function() {
                if (this.value) {
                    endTimeInput.min = this.value;
                    if (endTimeInput.value && endTimeInput.value <= this.value) {
                        endTimeInput.value = '';
                    }
                }
            });

            endTimeInput.addEventListener('change', function() {
                if (startTimeInput.value && this.value && this.value <= startTimeInput.value) {
                    alert('وقت النهاية يجب أن يكون بعد وقت البداية');
                    this.value = '';
                }
            });

            capacityInput.addEventListener('input', function() {
                const value = parseInt(this.value);
                if (value && (value < 1 || value > 200)) {
                    this.setCustomValidity('السعة يجب أن تكون بين 1 و 200');
                } else {
                    this.setCustomValidity('');
                }
            });

            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('.btn-save');
                submitButton.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>جاري الحفظ...';
                submitButton.disabled = true;
            });

            const alerts = document.querySelectorAll('.alert-danger');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 8000);
            });

            const titleInput = document.getElementById('title');
            const titleLabel = document.querySelector('label[for="title"]');
            titleInput.addEventListener('input', function() {
                const length = this.value.length;
                const maxLength = 255;
                if (length > maxLength - 20) {
                    titleLabel.innerHTML = `عنوان الدورة <span class="required">*</span> <small class="text-muted">(${length}/${maxLength})</small>`;
                } else {
                    titleLabel.innerHTML = 'عنوان الدورة <span class="required">*</span>';
                }
            });

            const descriptionInput = document.getElementById('description');
            const descriptionLabel = document.querySelector('label[for="description"]');
            descriptionInput.addEventListener('input', function() {
                const length = this.value.length;
                if (length > 500) {
                    descriptionLabel.innerHTML = `وصف الدورة <span class="required">*</span> <small class="text-muted">(${length} حرف)</small>`;
                } else {
                    descriptionLabel.innerHTML = 'وصف الدورة <span class="required">*</span>';
                }
            });
        });
    </script>
@endpush
