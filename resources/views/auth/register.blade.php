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

<h5 class="fw-bold text-dark mb-1">Daftar Akun Baru</h5>
<p class="text-muted small mb-3">Buat akun untuk mengelola inventori dan transaksi toko Anda.</p>

@if ($errors->any())
    <div class="alert alert-danger py-2 rounded-3 border-0">
        <ul class="mb-0 small ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">Nama Lengkap</label>
        <input type="text" name="name"
            class="form-control form-control-lg rounded-3 fs-6"
            placeholder="Contoh: Budi Santoso" value="{{ old('name') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">Alamat Email</label>
        <input type="email" name="email"
            class="form-control form-control-lg rounded-3 fs-6"
            placeholder="nama@email.com" value="{{ old('email') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">Kata Sandi</label>
        <input type="password" name="password"
            class="form-control form-control-lg rounded-3 fs-6"
            placeholder="Minimal 6 karakter" required>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-dark">Konfirmasi Kata Sandi</label>
        <input type="password" name="password_confirmation"
            class="form-control form-control-lg rounded-3 fs-6"
            placeholder="Ulangi kata sandi" required>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-bold fs-6 shadow-sm" style="background-color: #2563EB; border-color: #2563EB;">
            Daftar Sekarang
        </button>
    </div>

    <p class="small text-muted text-center mb-0">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none">
            Masuk ke Akun
        </a>
    </p>

</form>

@endsection
