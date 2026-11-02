@extends('layouts.auth')

@section('content')

<div class="d-flex align-items-center mb-4">
    <div class="bg-primary text-white rounded-3 p-2 me-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
        <i class="bi bi-building-fill fs-5"></i>
    </div>
    <div>
        <h4 class="fw-bold mb-0 text-dark">Prima Bangunan</h4>
        <small class="text-muted">Aplikasi Toko Bahan Bangunan & POS</small>
    </div>
</div>

<h5 class="fw-bold text-dark mb-1">Masuk ke Akun Toko</h5>
<p class="text-muted small mb-3">Silakan masukkan email dan kata sandi Anda untuk mengakses sistem.</p>

<div class="alert alert-info py-2 px-3 mb-3 rounded-3 border-0 small">
    <div class="fw-bold"><i class="bi bi-key-fill me-1"></i> Akun Demo Default:</div>
    <div>Email: <code>admin@gmail.com</code> | Password: <code>prima3003</code></div>
</div>

@if ($errors->any())
    <div class="alert alert-danger py-2 rounded-3 border-0">
        <ul class="mb-0 small ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">Alamat Email</label>
        <input type="email" name="email"
            class="form-control form-control-lg rounded-3 fs-6"
            placeholder="nama@email.com" value="{{ old('email', 'admin@gmail.com') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">Kata Sandi</label>
        <input type="password" name="password"
            class="form-control form-control-lg rounded-3 fs-6"
            placeholder="Masukkan kata sandi" value="prima3003" required>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
            <label class="form-check-label small text-muted" for="rememberMe">Ingat saya</label>
        </div>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-bold fs-6 shadow-sm" style="background-color: #2563EB; border-color: #2563EB;">
            Masuk ke Sistem
        </button>
    </div>

    <p class="small text-muted text-center mb-0">
        Belum memiliki akun toko?
        <a href="{{ route('register') }}" class="fw-bold text-primary text-decoration-none">
            Daftar Akun Baru
        </a>
    </p>

</form>

@endsection
