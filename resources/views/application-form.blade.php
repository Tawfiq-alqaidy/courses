@extends('layouts.app')

@section('title', 'منظومة التسجيل في الموسم الجامعي')

@push('styles')
    <style>
        .hero-section {
            background: linear-gradient(135deg, #112eb0ff 0%, #1c0bb3ff 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
            border-radius: 20px;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.1;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .course-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
            height: 100%;
        }

        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            border-color: #667eea;
        }

        .course-card.selected {
            border-color: #667eea;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            transform: translateY(-4px);
        }

        .category-filter {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .category-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .category-filter.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transform: translateY(-2px);
        }

        .form-section {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.1);
            border: 1px solid #e9ecef;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
        }

        .form-control.is-valid,
        .form-select.is-valid {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 1.2rem 3rem;
            border-radius: 25px;
            font-size: 1.2rem;
            font-weight: bold;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .course-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
        }

        .capacity-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .capacity-bar {
            flex: 1;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }

        .capacity-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .capacity-available {
            background: linear-gradient(90deg, #28a745, #20c997);
        }

        .capacity-almost-full {
            background: linear-gradient(90deg, #ffc107, #fd7e14);
        }

        .capacity-full {
            background: linear-gradient(90deg, #dc3545, #e83e8c);
        }

        .error-list {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .success-alert {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .floating-label {
            position: relative;
        }

        .floating-label .form-control:focus+.form-label,
        .floating-label .form-control:not(:placeholder-shown)+.form-label {
            transform: translateY(-1.5rem) scale(0.85);
            color: #667eea;
        }

        .requirements-card {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 0;
            }

            .form-section {
                padding: 1.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container mt-4">
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="hero-content">
                <h1 class="display-4 fw-bold mb-3">اتحاد طلبة جامعة طرابلس</h1>
                <p class="lead mb-4">انضم إلينا وطور مهاراتك مع أفضل الدورات التدريبية</p>
                <div class="d-inline-flex align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-check-circle me-2"></i>
                        <span>سهل وسريع</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bx bx-shield-check me-2"></i>
                        <span>آمن ومحمي</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bx bx-support me-2"></i>
                        <span>دعم 24/7</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="success-alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle display-6 text-success me-3"></i>
                    <div>
                        <h5 class="text-success fw-bold mb-1">تم بنجاح!</h5>
                        <p class="mb-0">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="error-list">
                <div class="d-flex align-items-start">
                    <i class="bx bx-error-circle display-6 text-danger me-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="text-danger fw-bold mb-3">يرجى تصحيح الأخطاء التالية:</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li class="mb-1">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Requirements Info -->
        <div class="requirements-card">
            <h5 class="fw-bold text-primary mb-3">
                <i class="bx bx-info-circle me-2"></i>
                متطلبات التقديم
            </h5>
            <div class="row">
                <div class="col-md-4 mb-2">
                    <i class="bx bx-check text-success me-2"></i>
                    بيانات شخصية صحيحة
                </div>
                <div class="col-md-4 mb-2">
                    <i class="bx bx-check text-success me-2"></i>
                    اختيار دورة واحدة على الأقل
                </div>
                <div class="col-md-4 mb-2">
                    <i class="bx bx-check text-success me-2"></i>
                    بريد إلكتروني فعال
                </div>
            </div>
        </div>

        <form action="{{ route('applications.store') }}" method="POST" id="applicationForm" novalidate>
            @csrf

            <!-- Personal Information Section -->
            <div class="form-section">
                <h3 class="h4 fw-bold mb-4 text-dark">
                    <i class="bx bx-user-circle text-primary me-2"></i>
                    البيانات الشخصية
                </h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="student_name" class="form-label fw-semibold required">
                            <i class="bx bx-user me-1"></i>الاسم الكامل
                        </label>
                        <input type="text"
                            class="form-control form-control-lg @error('student_name') is-invalid @enderror"
                            id="student_name" name="student_name" value="{{ old('student_name') }}"
                            placeholder="أدخل اسمك الكامل (الاسم الأول والأخير)" required>
                        @error('student_name')
                            <div class="invalid-feedback">
                                <i class="bx bx-error-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="valid-feedback">
                            <i class="bx bx-check-circle me-1"></i>اسم صالح
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_email" class="form-label fw-semibold required">
                            <i class="bx bx-envelope me-1"></i>البريد الإلكتروني
                        </label>
                        <input type="email"
                            class="form-control form-control-lg @error('student_email') is-invalid @enderror"
                            id="student_email" name="student_email" value="{{ old('student_email') }}"
                            placeholder="example@email.com" required>
                        @error('student_email')
                            <div class="invalid-feedback">
                                <i class="bx bx-error-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="valid-feedback">
                            <i class="bx bx-check-circle me-1"></i>بريد إلكتروني صالح
                        </div>
                        <div class="form-text">
                            <i class="bx bx-info-circle me-1"></i>
                            سيتم إرسال تأكيد التسجيل على هذا البريد
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_phone" class="form-label fw-semibold required">
                            <i class="bx bx-phone me-1"></i>رقم الهاتف
                        </label>
                        <input type="tel"
                            class="form-control form-control-lg @error('student_phone') is-invalid @enderror"
                            id="student_phone" name="student_phone" value="{{ old('student_phone') }}"
                            placeholder="05xxxxxxxx" pattern="[0-9]{10}" required>
                        @error('student_phone')
                            <div class="invalid-feedback">
                                <i class="bx bx-error-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="valid-feedback">
                            <i class="bx bx-check-circle me-1"></i>رقم هاتف صالح
                        </div>
                        <div class="form-text">
                            <i class="bx bx-info-circle me-1"></i>
                            يجب ان يحتوي رقم الهاتف على 10 ارقام
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label fw-semibold required">
                            <i class="bx bx-category me-1"></i>التخصص
                        </label>
                        <select class="form-select form-select-lg @error('category_id') is-invalid @enderror"
                            id="category_id" name="category_id" required>
                            <option value="">اختر التخصص المناسب لك</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">
                                <i class="bx bx-error-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="valid-feedback">
                            <i class="bx bx-check-circle me-1"></i>تخصص محدد
                        </div>
                        <div class="form-text">
                            <i class="bx bx-info-circle me-1"></i>
                            اختر التخصص الذي يناسب اهتماماتك ومسارك المهني
                        </div>
                    </div>
                </div>
            </div>
            <!-- Course Selection Section -->
            <div class="form-section">
                <h3 class="h4 fw-bold mb-4 text-dark">
                    <i class="bx bx-book-open text-primary me-2"></i>
                    اختيار الدورات
                    <span class="badge bg-info ms-2" id="selectedCount">0 دورة محددة</span>
                </h3>

                <div class="alert alert-info mb-4">
                    <i class="bx bx-info-circle me-2"></i>
                    <strong>ملاحظة هامة:</strong> يجب اختيار دورة واحدة على الأقل للتقديم. يمكنك اختيار عدة دورات من نفس
                    التخصص.
                </div>

                <!-- Category Filter -->
                <div class="mb-4">
                    <label class="form-label fw-semibold mb-3">
                        <i class="bx bx-filter me-1"></i>تصفية حسب التخصص
                    </label>
                    <div class="d-flex flex-wrap gap-2">
                        <div class="category-filter active badge bg-light text-dark p-2 px-3" data-category="all">
                            <i class="bx bx-grid-alt me-1"></i>
                            جميع الدورات
                        </div>
                        @foreach ($categories as $category)
                            <div class="category-filter badge bg-light text-dark p-2 px-3"
                                data-category="{{ $category->id }}">
                                <i class="bx bx-book me-1"></i>
                                {{ $category->name }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Selected Courses Error -->
                @error('selected_courses')
                    <div class="alert alert-danger mb-4">
                        <i class="bx bx-error-circle me-2"></i>{{ $message }}
                    </div>
                @enderror

                <!-- Courses Grid -->
                <div class="row" id="coursesGrid">
                    @forelse($courses as $course)
                        <div class="col-lg-6 mb-4 course-item" data-category="{{ $course->category_id }}">
                            <div class="card course-card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input course-checkbox" type="checkbox"
                                            value="{{ $course->id }}" name="selected_courses[]"
                                            id="course_{{ $course->id }}" data-category="{{ $course->category_id }}"
                                            {{ in_array($course->id, old('selected_courses', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold h5 mb-0" for="course_{{ $course->id }}">
                                            {{ $course->title }}
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <span class="badge bg-primary mb-2">{{ $course->category->name }}</span>
                                        @php
                                            $registered = $course->registered_students_count ?? 0;
                                            $capacity = $course->capacity_limit;
                                            $percentage = $capacity > 0 ? ($registered / $capacity) * 100 : 0;
                                        @endphp
                                        @if ($percentage >= 100)
                                            <span class="badge bg-danger ms-1">مكتملة</span>
                                        @elseif($percentage >= 80)
                                            <span class="badge bg-warning text-dark ms-1">شبه مكتملة</span>
                                        @else
                                            <span class="badge bg-success ms-1">متاحة</span>
                                        @endif
                                    </div>

                                    <p class="text-muted mb-3">{{ Str::limit($course->description, 120) }}</p>

                                    <div class="course-info">
                                        <div class="row text-sm mb-2">
                                            <div class="col-6">
                                                <i class="bx bx-group text-primary me-1"></i>
                                                <strong>السعة:</strong> {{ $registered }}/{{ $capacity }}
                                            </div>
                                            <div class="col-6">
                                                <i class="bx bx-time text-primary me-1"></i>
                                                <strong>المدة:</strong> {{ $course->duration ?? 'غير محددة' }}
                                            </div>
                                        </div>

                                        @if ($course->start_time)
                                            <div class="mb-2">
                                                <i class="bx bx-calendar text-primary me-1"></i>
                                                <strong>التوقيت:</strong>
                                                {{ $course->start_time->format('Y/m/d H:i') }}
                                            </div>
                                        @endif

                                        <!-- Capacity Indicator -->
                                        <div class="capacity-indicator">
                                            <span class="small text-muted">الإشغال:</span>
                                            <div class="capacity-bar">
                                                @php
                                                    $barClass =
                                                        $percentage >= 100
                                                            ? 'capacity-full'
                                                            : ($percentage >= 80
                                                                ? 'capacity-almost-full'
                                                                : 'capacity-available');
                                                @endphp
                                                <div class="capacity-fill {{ $barClass }}"
                                                    style="width: {{ min($percentage, 100) }}%"></div>
                                            </div>
                                            <span class="small">{{ round($percentage) }}%</span>
                                        </div>

                                        @if ($percentage >= 100)
                                            <div class="alert alert-warning small mt-2 mb-0">
                                                <i class="bx bx-info-circle me-1"></i>
                                                سيتم وضعك في قائمة الانتظار
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="bx bx-book-open display-1 text-muted"></i>
                                <h4 class="mt-3 text-muted">لا توجد دورات متاحة حاليًا</h4>
                                <p class="text-muted">يرجى المراجعة لاحقًا أو تواصل مع الإدارة</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Submit Section -->
            <div class="text-center">
                <div class="mb-4">
                    <div class="alert alert-light border" id="submissionInfo" style="display: none;">
                        <h6 class="fw-bold mb-2">
                            <i class="bx bx-info-circle text-primary me-2"></i>
                            معلومات مهمة قبل التقديم
                        </h6>
                        <ul class="text-start small mb-0">
                            <li>تأكد من صحة جميع البيانات المدخلة</li>
                            <li>ستحصل على رمز طالب فريد للمتابعة</li>
                            <li>سيتم إشعارك بحالة الطلب عبر البريد الإلكتروني</li>
                            <li>يمكنك تتبع حالة طلبك في أي وقت</li>
                        </ul>
                    </div>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn" disabled>
                    <i class="bx bx-send me-2"></i>
                    <span id="submitText">تقديم الطلب</span>
                </button>

                <div class="mt-3">
                    <p class="text-muted small mb-1" id="submitHint">
                        <i class="bx bx-info-circle me-1"></i>
                        يرجى اختيار دورة واحدة على الأقل قبل التقديم
                    </p>
                    <div class="d-flex justify-content-center align-items-center gap-3 small text-muted">
                        <div>
                            <i class="bx bx-shield-check me-1"></i>
                            آمن ومشفر
                        </div>
                        <div>
                            <i class="bx bx-time me-1"></i>
                            يستغرق أقل من دقيقتين
                        </div>
                        <div>
                            <i class="bx bx-support me-1"></i>
                            دعم فني متاح
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryFilters = document.querySelectorAll('.category-filter');
            const courseItems = document.querySelectorAll('.course-item');
            const courseCheckboxes = document.querySelectorAll('.course-checkbox');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitHint = document.getElementById('submitHint');
            const selectedCount = document.getElementById('selectedCount');
            const submissionInfo = document.getElementById('submissionInfo');
            const categorySelect = document.getElementById('category_id');

            // Real-time form validation
            const inputs = document.querySelectorAll('input[required], select[required]');
            inputs.forEach(input => {
                input.addEventListener('input', validateField);
                input.addEventListener('blur', validateField);
            });

            function validateField(e) {
                const field = e.target;
                const value = field.value.trim();

                if (field.type === 'email') {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (value && emailPattern.test(value)) {
                        field.classList.add('is-valid');
                        field.classList.remove('is-invalid');
                    } else if (value) {
                        field.classList.add('is-invalid');
                        field.classList.remove('is-valid');
                    }
                } else if (field.type === 'tel') {
                    const phonePattern = /^\d{10}$/;
                    if (value && phonePattern.test(value)) {
                        field.classList.add('is-valid');
                        field.classList.remove('is-invalid');
                    } else if (value) {
                        field.classList.add('is-invalid');
                        field.classList.remove('is-valid');
                    }
                } else if (field.required && value) {
                    field.classList.add('is-valid');
                    field.classList.remove('is-invalid');
                }
            }

            // Category filter functionality
            categoryFilters.forEach(filter => {
                filter.addEventListener('click', function() {
                    // Remove active class from all filters
                    categoryFilters.forEach(f => f.classList.remove('active'));

                    // Add active class to clicked filter
                    this.classList.add('active');

                    const category = this.dataset.category;

                    courseItems.forEach(item => {
                        if (category === 'all' || item.dataset.category === category) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });

            // Course selection functionality
            function updateSubmitButton() {
                const checkedBoxes = document.querySelectorAll('.course-checkbox:checked');
                const isValid = checkedBoxes.length > 0;

                submitBtn.disabled = !isValid;
                selectedCount.textContent = `${checkedBoxes.length} دورة محددة`;

                if (isValid) {
                    submitBtn.classList.add('btn-primary');
                    submitBtn.classList.remove('btn-secondary');
                    submitHint.innerHTML =
                        `<i class="bx bx-check-circle me-1 text-success"></i>تم اختيار ${checkedBoxes.length} دورة - يمكنك الآن تقديم الطلب`;
                    submissionInfo.style.display = 'block';
                } else {
                    submitBtn.classList.add('btn-secondary');
                    submitBtn.classList.remove('btn-primary');
                    submitHint.innerHTML =
                        `<i class="bx bx-info-circle me-1"></i>يرجى اختيار دورة واحدة على الأقل قبل التقديم`;
                    submissionInfo.style.display = 'none';
                }

                // Update course card selection styling
                courseCheckboxes.forEach(checkbox => {
                    const card = checkbox.closest('.course-card');
                    if (checkbox.checked) {
                        card.classList.add('selected');
                    } else {
                        card.classList.remove('selected');
                    }
                });
            }

            // Category-based course filtering
            function filterCoursesByCategory(selectedCategoryId) {
                courseCheckboxes.forEach(checkbox => {
                    const courseCategory = checkbox.dataset.category;
                    const courseItem = checkbox.closest('.course-item');

                    if (!selectedCategoryId || courseCategory === selectedCategoryId) {
                        checkbox.disabled = false;
                        courseItem.style.opacity = '1';
                    } else {
                        // Uncheck and disable courses from other categories
                        if (checkbox.checked) {
                            checkbox.checked = false;
                        }
                        checkbox.disabled = true;
                        courseItem.style.opacity = '0.5';
                    }
                });
                updateSubmitButton();
            }

            courseCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSubmitButton);
            });

            // Auto-filter courses when category is selected
            categorySelect.addEventListener('change', function() {
                const selectedCategory = this.value;
                filterCoursesByCategory(selectedCategory);

                if (selectedCategory) {
                    const targetFilter = document.querySelector(`[data-category="${selectedCategory}"]`);
                    if (targetFilter) {
                        targetFilter.click();
                    }
                } else {
                    // Reset to show all courses
                    document.querySelector('[data-category="all"]').click();
                    courseCheckboxes.forEach(checkbox => {
                        checkbox.disabled = false;
                        checkbox.closest('.course-item').style.opacity = '1';
                    });
                }
            });

            // Form submission handling
            document.getElementById('applicationForm').addEventListener('submit', function(e) {
                const checkedBoxes = document.querySelectorAll('.course-checkbox:checked');

                if (checkedBoxes.length === 0) {
                    e.preventDefault();
                    alert('يرجى اختيار دورة واحدة على الأقل');
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                submitText.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>جاري الإرسال...';

                // Prevent double submission
                setTimeout(() => {
                    if (!submitBtn.disabled) return;
                    submitBtn.style.opacity = '0.7';
                }, 100);
            });

            // Initial button state
            updateSubmitButton();

            // Auto-scroll to errors if present
            @if ($errors->any())
                setTimeout(() => {
                    const errorElement = document.querySelector('.error-list');
                    if (errorElement) {
                        errorElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }, 500);
            @endif

            // Add required field indicators
            const requiredLabels = document.querySelectorAll('.required');
            requiredLabels.forEach(label => {
                if (!label.querySelector('.text-danger')) {
                    label.insertAdjacentHTML('beforeend', '<span class="text-danger ms-1">*</span>');
                }
            });
        });
    </script>

    <style>
        .required::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
    </style>
@endpush
