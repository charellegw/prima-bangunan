@extends('layouts.auth', ['title' => 'Daftar Akun — Prima Bangunan'])

@section('content')

<div class="mb-4">
    <h4 class="fw-bold text-main mb-1" style="font-size: 22px;">Daftar Akun Baru</h4>
    <p class="text-secondary small mb-0" style="font-size: 13px;">Buat akun baru untuk mengelola inventori, transaksi kasir, dan pengiriman.</p>
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

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3">
        <label class="pb-label">Nama Lengkap</label>
        <input type="text" name="name"
            class="form-control"
            placeholder="Contoh: Budi Santoso" value="{{ old('name') }}" required>
    </div>

    <div class="mb-3">
        <label class="pb-label">Alamat Email</label>
        <input type="email" name="email"
            class="form-control"
            placeholder="nama@email.com" value="{{ old('email') }}" required>
    </div>

    <div class="mb-3">
        <label class="pb-label">Kata Sandi</label>
        <input type="password" name="password"
            class="form-control"
            placeholder="Minimal 6 karakter" required>
    </div>

    <div class="mb-4">
        <label class="pb-label">Konfirmasi Kata Sandi</label>
        <input type="password" name="password_confirmation"
            class="form-control"
            placeholder="Ulangi kata sandi" required>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="pb-btn pb-btn-primary pb-btn-lg">
            Daftar Sekarang
        </button>
    </div>

    <p class="small text-secondary text-center mb-0" style="font-size: 13px;">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-none">
            Masuk ke Akun
        </a>
    </p>

</form>

@endsection
