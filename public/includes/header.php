<?php
include __DIR__ . '/../../db_connect.php'; 

if (isset($_SESSION['user_id'])) { 
    $userId = $_SESSION['user_id'];
    $now = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("UPDATE users SET last_active = ? WHERE id = ?");
    $stmt->bind_param("si", $now, $userId);
    $stmt->execute();
}

$current_page = basename($_SERVER['PHP_SELF']); 
?>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Flynow - Đại lý vé máy bay</title>
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/header.css">
</head>
<body>

<div class="top-header">
  <div class="container top-header-inner">
    <div class="left-part">
      <img src="/img/logoflynow.png" alt="Flynow" class="logo-flynow">
    </div>
    <div class="slogan-container">
      <span class="slogan">Vé máy bay, đặt mua vé máy bay tại đại lý vé máy bay Flynow</span>
    </div>
    <div class="right-part">
      <span class="hotline-label">Hotline:</span>
      <span class="hotline-number">1900 6432 (024) 7300 1133 - (028) 7300 1133</span>
    </div>
  </div>
</div>

<header class="main-menu">
  <div class="container menu-inner">
    <ul class="menu-list">
      <li class="<?= ($current_page == 'index.php') ? 'active' : '' ?>"><a href="/">Trang chủ</a></li>
      <li class="<?= ($current_page == 'about.php') ? 'active' : '' ?>"><a href="about.php">Giới thiệu</a></li>
      <li class="<?= ($current_page == 'cheap-tickets.php') ? 'active' : '' ?>"><a href="cheap-tickets.php">Vé giá tốt</a></li>
      <li class="<?= ($current_page == 'promo.php') ? 'active' : '' ?>"><a href="promotions.php">Tin khuyến mại</a></li>
      <li class="<?= ($current_page == 'guide.php') ? 'active' : '' ?>"><a href="guide.php">Hướng dẫn đặt vé</a></li>
      <li class="<?= ($current_page == 'checkout.php') ? 'active' : '' ?>">
        <a href="checkout.php" id="checkoutMenuLink">
          Thanh toán
          <span id="checkoutDot" class="checkout-dot" style="display:none;"></span>
        </a>
      </li>    
    </ul>

    <div class="user-info">
      <?php if(isset($_SESSION['fullname'])): ?>
      <div class="user-menu">
        <img src="<?= '/' . ($_SESSION['avatar'] ?? 'img/default-avatar.png') . '?v=' . time() ?>"
            class="user-avatar">
        <span class="user-name"><?=htmlspecialchars($_SESSION['fullname'])?></span>
        <span class="arrow">&#9662;</span>
        <ul class="dropdown">
          <li><a href="/profile.php">Thông tin cá nhân</a></li>
          <li><a href="/my-tickets.php">Vé của tôi</a></li>
          <li><a href="/logout.php">Đăng xuất</a></li>
        </ul>
      </div>
      <?php else: ?>
      <button class="login-btn" onclick="window.location.href='/login.php'">
      <i class="fa fa-user"></i> Đăng nhập
      </button>
      <?php endif; ?>
    </div>
  </div>
</header>

<script>

document.addEventListener('DOMContentLoaded', () => {
  const dot = document.getElementById('checkoutDot');

  const isLoggedIn = <?= isset($_SESSION['fullname']) ? 'true' : 'false' ?>;
  if (!isLoggedIn) {
    dot.style.display = 'none';
    return;
  }

  const booking = localStorage.getItem('booking_data');
  const flightId = localStorage.getItem('selected_flight');

  if (!booking || !flightId) {
    dot.style.display = 'none';
    return;
  }

  try {
    const data = JSON.parse(booking);
    if (data.contactName || (data.passengers && data.passengers.length > 0)) {
      dot.style.display = 'inline-block';
    } else {
      dot.style.display = 'none';
    }
  } catch (e) {
    console.error('Lỗi đọc booking_data:', e);
    dot.style.display = 'none';
  }
});

</script>


