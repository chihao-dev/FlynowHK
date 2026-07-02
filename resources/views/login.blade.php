@extends('layouts.app')

@section('title', 'Đăng nhập Flynow')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<video autoplay muted loop class="video-bg">
    <source src="{{ asset('img/clouds2.mp4') }}" type="video/mp4">
</video>

<div class="login-container">
    <div class="login-card">
        <h4>Đăng nhập Flynow</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (isset($err))
            <div class="alert alert-danger">{{ $err }}</div>
        @endif

        <form method="post" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label>Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
                <label>Mật khẩu</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <button class="btn btn-primary w-100 mb-3">Đăng nhập</button>
            <a href="{{ route('register') }}" class="btn btn-link d-block text-center mt-3">Chưa có tài khoản? Đăng ký</a>
        </form>
    </div>
</div>
@endsection