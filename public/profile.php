<?php
session_start();
include __DIR__ . '/../db_connect.php';
require_once __DIR__.'/../app/Http/Controllers/ProfileController.php';

/* CHECK LOGIN TRƯỚC */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$controller = $app->make(\App\Http\Controllers\ProfileController::class);
$user_id = $_SESSION['user_id'];
$user = $controller->getProfile($user_id);

$msg = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($controller->updateProfile($user_id, $_POST, $_FILES)) {

        // 🔥 LẤY LẠI DỮ LIỆU MỚI TỪ DB
        $updatedUser = $controller->getProfile($user_id);

        $_SESSION['fullname'] = $updatedUser['fullname'];
        $_SESSION['avatar']   = $updatedUser['avatar'];

        header("Location: profile.php");
        exit;
    } else {
        $errors[] = "Cập nhật thất bại.";
    }
}


include __DIR__.'/includes/header.php';
?>


<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Flynow - Đại lý vé máy bay</title>
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/header.css">
  <link rel="stylesheet" href="./css/profile.css">
</head>
<body>

<div class="login-container">
  <div class="login-card">
    <h4>Thông tin cá nhân</h4>
    <?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
    <?php if($errors): foreach($errors as $e): ?>
        <div class="alert alert-danger"><?=htmlspecialchars($e)?></div>
    <?php endforeach; endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3 text-center avatar-wrapper">
            <div class="avatar-circle">
                <img
                    id="avatarPreview"
                    src="<?= $user['avatar'] ? '/'.$user['avatar'] : '/img/default-avatar.png' ?>"
                    alt="Avatar"
                    class="avatar-img"
                >
            </div>
            <input
                type="file"
                name="avatar"
                id="avatarInput"
                class="form-control mt-2"
                accept="image/*"
            >
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" value="<?=htmlspecialchars($user['email'])?>" readonly>
        </div>
        <div class="mb-3">
            <label>Họ và tên</label>
            <input type="text" class="form-control" name="fullname"
                value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label>Ngày sinh</label>
            <input type="date" class="form-control" name="birthdate"
                value="<?= htmlspecialchars($user['birthdate'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label>Địa chỉ</label>
            <input type="text" class="form-control" name="address"
                value="<?= htmlspecialchars($user['address'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label>Số điện thoại</label>
            <input type="text" class="form-control" name="phone"
                value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
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

</body>
</html>

<?php include __DIR__.'/includes/footer.php'; ?>
