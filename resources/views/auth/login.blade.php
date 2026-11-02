@extends('layouts.auth')

@section('content')

<h4 class="fw-bold mb-4">Masuk</h4>

@if ($errors->any())
    <div class="alert alert-danger py-2">
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-3">
        <input type="email" name="email" 
            class="form-control form-control-lg rounded-3" 
            placeholder="Email" required>
    </div>

    <div class="mb-4">
        <input type="password" name="password" 
            class="form-control form-control-lg rounded-3" 
            placeholder="Password" required>
    </div>

    <div class="d-grid mb-3">
        <button class="btn btn-dark btn-lg rounded-3">
            Masuk
        </button>
    </div>

    <p class="small text-muted">
        Belum punya akun?
        <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">
            Daftar
        </a>
    </p>

</form>

@endsection