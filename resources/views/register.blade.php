@extends('layouts.app')

@section('title', 'Đăng ký Flynow')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endpush

@section('content')
<video autoplay muted loop class="video-bg">
    <source src="{{ asset('img/clouds2.mp4') }}" type="video/mp4">
</video>

<div class="login-container">
    <div class="login-card">
        <h4>Đăng ký tài khoản</h4>
        
        @if (isset($msg))
            <div class="alert alert-info">{{ $msg }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="post" action="{{ route('register') }}">
            @csrf
            <div class="mb-3">
                <label>Họ và tên</label>
                <input class="form-control" name="fullname" placeholder="Nguyễn Văn A" value="{{ old('fullname') }}" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" class="form-control" name="email" placeholder="example@mail.com" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label>Mật khẩu</label>
                <input type="password" class="form-control" name="password" placeholder="********" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
            <a href="{{ route('login') }}" class="btn btn-link d-block text-center mt-3">Đã có tài khoản? Đăng nhập</a>
        </form>
    </div>
</div>
@endsection