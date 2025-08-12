@extends('layouts.app')

@section('title', 'تحديث الملف الشخصي')

@push('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 15px;
    }

    .profile-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .profile-card .card-header {
        background: #f8f9fa;
        border: none;
        padding: 1.5rem;
        font-weight: 600;
        color: #495057;
    }

    .profile-card .card-body {
        padding: 2rem;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .form-control:focus,
    .form-select:focus {
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

    .avatar-placeholder {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h2 fw-bold mb-2">
                        <i class="bx bx-user-circle me-2"></i>
                        تحديث الملف الشخصي
                    </h1>
                    <p class="mb-0 opacity-90">تحديث بياناتك الشخصية وكلمة المرور</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="avatar-placeholder">
                        {{ mb_substr(auth()->user()->name, 0, 2) }}
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

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="profile-card">
                    <div class="card-header">
                        <h5 class="mb-0">المعلومات الشخصية</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.profile.update') }}">
                            @csrf
                            @method('PUT')

                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">الاسم الكامل</label>
                                <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', $admin->name) }}"
                                    required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', $admin->email) }}"
                                    required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <h6 class="mb-3">تغيير كلمة المرور (اختياري)</h6>
                            <p class="text-muted small mb-3">اتركه فارغاً إذا كنت لا تريد تغيير كلمة المرور</p>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور الجديدة</label>
                                <input type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password">
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
                                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                                <input type="password"
                                    class="form-control"
                                    id="password_confirmation"
                                    name="password_confirmation">
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i>
                                    حفظ التغييرات
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

        // Password validation visual feedback
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');

        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const requirements = document.querySelector('.password-requirements');

                if (password.length > 0) {
                    requirements.style.display = 'block';
                } else {
                    requirements.style.display = 'none';
                }
            });
        }

        // Confirm password matching
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', function() {
                const password = passwordInput.value;
                const confirmPassword = this.value;

                if (confirmPassword.length > 0) {
                    if (password === confirmPassword) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                    }
                } else {
                    this.classList.remove('is-valid', 'is-invalid');
                }
            });
        }
    });
</script>
@endpush