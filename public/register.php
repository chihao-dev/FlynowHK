<?php
require __DIR__.'/../db_connect.php';
require __DIR__ . '/../app/Http/Controllers/RegisterController.php';
include __DIR__ . '/includes/header.php';
?>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Đăng ký Flynow</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/register.css">
</head>
<body>

<video autoplay muted loop class="video-bg">
  <source src="./img/clouds2.mp4" type="video/mp4">
</video>

<div class="login-container">
  <div class="login-card">
    <h4>Đăng ký tài khoản</h4>
    <?php if($msg): ?>
      <div class="alert alert-info"><?=$msg?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label>Họ và tên</label>
        <input class="form-control" name="fullname" placeholder="Nguyễn Văn A" required>
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" class="form-control" name="email" placeholder="example@mail.com" required>
      </div>
      <div class="mb-3">
        <label>Mật khẩu</label>
        <input type="password" class="form-control" name="password" placeholder="********" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
      <a href="login.php" class="btn btn-link d-block text-center mt-3">Đã có tài khoản? Đăng nhập</a>
    </form>
  </div>
</div>

</body>
</html>

<?php include __DIR__.'/includes/footer.php'; ?>
