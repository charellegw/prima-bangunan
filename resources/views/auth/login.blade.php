@extends('layouts.auth', ['title' => 'Masuk — Prima Bangunan'])

@section('content')

<div class="mb-4">
    <h4 class="fw-bold text-main mb-1" style="font-size: 22px;">Masuk ke Akun Toko</h4>
    <p class="text-secondary small mb-0" style="font-size: 13px;">Silakan masukkan email dan kata sandi Anda untuk mengakses sistem.</p>
</div>

{{-- Demo Credential Banner --}}
<div class="pb-alert pb-alert-info py-2.5 px-3 mb-3 d-flex align-items-center">
    <div class="small">
        <div class="fw-semibold text-primary mb-0.5" style="font-size: 12px;">Akun Demo Default:</div>
        <div class="text-secondary" style="font-size: 12px;">Email: <strong>admin@gmail.com</strong> &bull; Sandi: <strong>prima3003</strong></div>
    </div>
</div>

@if ($errors->any())
    <x-alert variant="danger" class="mb-3">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-3">
        <label class="pb-label">Alamat Email</label>
        <input type="email" name="email"
            class="form-control"
            placeholder="nama@email.com" value="{{ old('email', 'admin@gmail.com') }}" required>
    </div>

    <div class="mb-3">
        <label class="pb-label">Kata Sandi</label>
        <input type="password" name="password"
            class="form-control"
            placeholder="Masukkan kata sandi" value="prima3003" required>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
            <label class="form-check-label small text-secondary" for="rememberMe" style="font-size: 13px;">Ingat saya</label>
        </div>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="pb-btn pb-btn-primary pb-btn-lg">
            Masuk ke Sistem
        </button>
    </div>

    <p class="small text-secondary text-center mb-0" style="font-size: 13px;">
        Belum memiliki akun toko?
        <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-none">
            Daftar Akun Baru
        </a>
    </p>

</form>

@endsection
