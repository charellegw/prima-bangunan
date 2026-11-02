@extends('layouts.app')

@section('title', 'Prima Bangunan')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-3">
        <h1 class="m-0 h4 text-dark" style="font-weight: 700;">Dashboard Utama</h1>
        <div class="text-muted small">{{ date('l, d F Y H:i:s T') }}</div>
    </div>

    @include('partials.stats-widgets')

    <div class="mb-4">
        @include('partials.shortcuts-content')
    </div>

    @include('partials.stock-alerts')
</div>
@endsection