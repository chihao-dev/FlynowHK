<?php
session_start();

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: /admin/dashboard.php');
    exit;
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Không phải quản trị viên</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Flynow - Quản trị viên</title>
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm border-danger">
        <div class="card-body text-center">
          <h3 class="text-danger mb-3">⚠️ Bạn không phải quản trị viên</h3>
          <p class="mb-4">Vui lòng kiểm tra quyền truy cập hoặc đăng nhập bằng tài khoản admin để vào trang này.</p>
          <a href="/login.php" class="btn btn-primary">Đăng nhập</a>
          <a href="/index.php" class="btn btn-outline-secondary ms-2">Về trang chủ</a>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
