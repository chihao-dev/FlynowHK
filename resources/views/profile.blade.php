@extends('layouts.app')

@section('title', 'Thông tin cá nhân - Flynow')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<div class="login-container">
    <div class="login-card">
        <h4>Thông tin cá nhân</h4>
        
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="post" action="{{ route('profile') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3 text-center avatar-wrapper">
                <div class="avatar-circle">
                    <img id="avatarPreview"
                        src="{{ $user['avatar'] ? asset($user['avatar']) : asset('img/default-avatar.png') }}" 
                        alt="Avatar"
                        class="avatar-img">
                </div>
                <input type="file" name="avatar" id="avatarInput" class="form-control mt-2" accept="image/*">
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" class="form-control" value="{{ $user['email'] }}" readonly>
            </div>
            <div class="mb-3">
                <label>Họ và tên</label>
                <input type="text" class="form-control" name="fullname"
                    value="{{ old('fullname', $user['fullname'] ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label>Ngày sinh</label>
                <input type="date" class="form-control" name="birthdate"
                    value="{{ old('birthdate', $user['birthdate'] ?? '') }}">
            </div>
            <div class="mb-3">
                <label>Địa chỉ</label>
                <input type="text" class="form-control" name="address"
                    value="{{ old('address', $user['address'] ?? '') }}">
            </div>
            <div class="mb-3">
                <label>Số điện thoại</label>
                <input type="text" class="form-control" name="phone"
                    value="{{ old('phone', $user['phone'] ?? '') }}">
            </div>
            <button type="submit" class="btn btn-primary w-100">Cập nhật</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('avatarInput').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('Vui lòng chọn file ảnh');
            e.target.value = '';
            return;
        }

        const preview = document.getElementById('avatarPreview');

        const objectUrl = URL.createObjectURL(file);
        preview.src = objectUrl;

        preview.onload = () => URL.revokeObjectURL(objectUrl);
    });
</script>
@endsection