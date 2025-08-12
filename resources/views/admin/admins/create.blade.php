@extends('layouts.app')

@section('title', 'إضافة مدير جديد')

@push('styles')
<style>
    .admin-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 15px;
    }

    .admin-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .admin-card .card-header {
        background: #f8f9fa;
        border: none;
        padding: 1.5rem;
        font-weight: 600;
        color: #495057;
    }

    .admin-card .card-body {
        padding: 2rem;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .password-requirements {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: #6c757d;
    }

    .password-requirements ul {
        margin: 0;
        padding-left: 1.5rem;
    }

    .security-notice {
        background: #e7f3ff;
        border: 1px solid #b3d9ff;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 2rem;
    }

    .security-notice .icon {
        color: #0066cc;
        font-size: 1.2rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h2 fw-bold mb-2">
                        <i class="bx bx-user-plus me-2"></i>
                        إضافة مدير جديد
                    </h1>
                    <p class="mb-0 opacity-90">إنشاء حساب مدير جديد للوصول إلى لوحة التحكم</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light">
                        <i class="bx bx-arrow-back me-1"></i>
                        العودة للوحة التحكم
                    </a>
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

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Security Notice -->
        <div class="security-notice">
            <div class="d-flex align-items-start">
                <i class="bx bx-shield-alt-2 icon me-2 mt-1"></i>
                <div>
                    <h6 class="mb-1">ملاحظة أمنية</h6>
                    <p class="mb-0 small">سيتمكن المدير الجديد من الوصول الكامل إلى لوحة التحكم. تأكد من منح هذه الصلاحيات للأشخاص المناسبين فقط.</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="admin-card">
                    <div class="card-header">
                        <h5 class="mb-0">بيانات المدير الجديد</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.admins.store') }}">
                            @csrf

                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    placeholder="أدخل الاسم الكامل للمدير">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="admin@example.com">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">سيستخدم هذا البريد للدخول إلى النظام</div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    required
                                    placeholder="أدخل كلمة مرور قوية">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="password-requirements">
                                    <strong>متطلبات كلمة المرور:</strong>
                                    <ul>
                                        <li>8 أحرف على الأقل</li>
                                        <li>حرف كبير واحد على الأقل (A-Z)</li>
                                        <li>حرف صغير واحد على الأقل (a-z)</li>
                                        <li>رقم واحد على الأقل (0-9)</li>
                                        <li>رمز خاص واحد على الأقل (@$!%*?&)</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password"
                                    class="form-control"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    required
                                    placeholder="أعد إدخال كلمة المرور">
                                <div class="invalid-feedback" id="password-match-error" style="display: none;">
                                    كلمة المرور غير متطابقة
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary" id="submit-btn">
                                    <i class="bx bx-user-plus me-1"></i>
                                    إنشاء حساب المدير
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                    <i class="bx bx-x me-1"></i>
                                    إلغاء
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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

        // Password validation
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const submitBtn = document.getElementById('submit-btn');
        const passwordMatchError = document.getElementById('password-match-error');

        // Real-time password validation
        function validatePasswords() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.add('is-valid');
                    passwordMatchError.style.display = 'none';
                    return true;
                } else {
                    confirmPasswordInput.classList.remove('is-valid');
                    confirmPasswordInput.classList.add('is-invalid');
                    passwordMatchError.style.display = 'block';
                    return false;
                }
            } else {
                confirmPasswordInput.classList.remove('is-valid', 'is-invalid');
                passwordMatchError.style.display = 'none';
                return true;
            }
        }

        // Password strength validation
        function validatePasswordStrength() {
            const password = passwordInput.value;
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /\d/.test(password),
                special: /[@$!%*?&]/.test(password)
            };

            const allValid = Object.values(requirements).every(Boolean);

            if (password.length > 0) {
                if (allValid) {
                    passwordInput.classList.remove('is-invalid');
                    passwordInput.classList.add('is-valid');
                } else {
                    passwordInput.classList.remove('is-valid');
                    passwordInput.classList.add('is-invalid');
                }
            } else {
                passwordInput.classList.remove('is-valid', 'is-invalid');
            }

            return allValid;
        }

        // Event listeners
        passwordInput.addEventListener('input', function() {
            validatePasswordStrength();
            validatePasswords();
        });

        confirmPasswordInput.addEventListener('input', function() {
            validatePasswords();
        });

        // Form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const isPasswordValid = validatePasswordStrength();
            const isPasswordsMatch = validatePasswords();

            if (!isPasswordValid || !isPasswordsMatch) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endpush