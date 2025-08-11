@extends('layouts.app')

@section('title', 'تسجيل دخول الإدارة')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 0;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        max-width: 450px;
        width: 100%;
        margin: 0 1rem;
    }

    .login-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 2rem 2rem;
        text-align: center;
        position: relative;
    }

    .login-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .login-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
    }

    .login-form {
        padding: 2.5rem;
    }

    .form-floating {
        margin-bottom: 1.5rem;
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.8rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        background-color: white;
    }

    .form-label {
        color: #6c757d;
        font-weight: 600;
        padding: 0 0.5rem;
    }

    .btn-login {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        width: 100%;
        color: white;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .alert {
        border-radius: 10px;
        border: none;
        margin-bottom: 1.5rem;
    }

    .remember-me {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 1rem 0;
    }

    .form-check-input {
        border-radius: 5px;
        border: 2px solid #667eea;
    }

    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    .footer-text {
        text-align: center;
        margin-top: 2rem;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .demo-credentials {
        background: rgba(102, 126, 234, 0.1);
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1.5rem;
        border: 1px dashed #667eea;
    }

    .demo-credentials h6 {
        color: #667eea;
        margin-bottom: 0.5rem;
    }

    .demo-credentials small {
        color: #6c757d;
    }

    @media (max-width: 576px) {
        .login-header {
            padding: 2rem 1.5rem 1.5rem;
        }

        .login-form {
            padding: 2rem 1.5rem;
        }

        .login-icon {
            font-size: 3rem;
        }
    }
</style>
@endpush

@section('content')
<div class="login-container">
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <div class="login-icon">
                <i class="bx bxs-shield-check"></i>
            </div>
            <h2 class="fw-bold mb-2">لوحة تحكم الإدارة</h2>
            <p class="mb-0 opacity-90">تسجيل دخول المدراء والموظفين</p>
        </div>

        <!-- Login Form -->
        <div class="login-form">
            @if ($errors->any())
            <div class="alert alert-danger">
                <i class="bx bx-error-circle me-2"></i>
                <strong>خطأ في تسجيل الدخول!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('status'))
            <div class="alert alert-success">
                <i class="bx bx-check-circle me-2"></i>
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                <!-- Email Field -->
                <div class="form-floating">
                    <input type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="admin@example.com"
                        required
                        autofocus>
                    <label for="email">
                        <i class="bx bx-envelope me-2"></i>البريد الإلكتروني
                    </label>
                    @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="form-floating">
                    <input type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="كلمة المرور"
                        required>
                    <label for="password">
                        <i class="bx bx-lock-alt me-2"></i>كلمة المرور
                    </label>
                    @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <!-- <div class="remember-me">
                    <input type="checkbox"
                           class="form-check-input"
                           id="remember"
                           name="remember"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        تذكرني
                    </label>
                </div> -->

                <!-- Submit Button -->
                <button type="submit" class="btn btn-login">
                    <i class="bx bx-log-in-circle me-2"></i>
                    تسجيل الدخول
                </button>
            </form>

            <!-- Demo Credentials -->
            <!-- <div class="demo-credentials">
                <h6><i class="bx bx-info-circle me-2"></i>بيانات تجريبية</h6>
                <div class="row">
                    <div class="col-6">
                        <small><strong>البريد:</strong><br>admin@courses.com</small>
                    </div>
                    <div class="col-6">
                        <small><strong>كلمة المرور:</strong><br>password</small>
                    </div>
                </div>
                <hr class="my-2">
                <div class="row">
                    <div class="col-6">
                        <small><strong>البريد:</strong><br>admin@example.com</small>
                    </div>
                    <div class="col-6">
                        <small><strong>كلمة المرور:</strong><br>admin123</small>
                    </div>
                </div>
            </div> -->

            <!-- Footer -->
            <div class="footer-text">
                <small>
                    <i class="bx bx-shield me-1"></i>
                    منطقة محمية للمدراء المعتمدين فقط
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto focus on email field
        const emailField = document.getElementById('email');
        if (emailField && !emailField.value) {
            emailField.focus();
        }

        // Add loading state to submit button
        const form = document.querySelector('form');
        const submitBtn = document.querySelector('.btn-login');

        form.addEventListener('submit', function() {
            submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>جاري تسجيل الدخول...';
            submitBtn.disabled = true;
        });

        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });

        // Quick fill demo credentials
        const demoCredentials = document.querySelector('.demo-credentials');
        if (demoCredentials) {
            const demoButtons = demoCredentials.querySelectorAll('.row');
            demoButtons.forEach((row, index) => {
                row.style.cursor = 'pointer';
                row.style.transition = 'background-color 0.2s ease';

                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(102, 126, 234, 0.1)';
                    this.style.borderRadius = '5px';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = 'transparent';
                });

                row.addEventListener('click', function() {
                    const emailField = document.getElementById('email');
                    const passwordField = document.getElementById('password');

                    if (index === 0) {
                        emailField.value = 'admin@courses.com';
                        passwordField.value = 'password';
                    } else {
                        emailField.value = 'admin@example.com';
                        passwordField.value = 'admin123';
                    }

                    // Show feedback
                    this.style.backgroundColor = 'rgba(40, 167, 69, 0.2)';
                    setTimeout(() => {
                        this.style.backgroundColor = 'transparent';
                    }, 1000);
                });
            });
        }
    });
</script>
@endpush