<?php
require_once __DIR__ . '/../../db_connect.php';

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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <a href="javascript:void(0)" onclick="goToCheckout()" id="checkoutMenuLink">
          Thanh toán
          <span id="checkoutDot" class="checkout-dot" style="display:none;"></span>
        </a>
      </li>
    </ul>

    <div class="user-info">
      <?php if(isset($_SESSION['fullname'])):
        $avatarPath = !empty($_SESSION['avatar']) ? $_SESSION['avatar'] : 'img/default-avatar.png';
        if (strpos($avatarPath, 'http') !== 0 && strpos($avatarPath, '/') !== 0) {
            $avatarPath = '/' . $avatarPath;
        }
      ?>
      <div class="user-menu">
        <img src="<?= $avatarPath . '?v=' . time() ?>"
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

  const userId = <?= $_SESSION['user_id'] ?? 0 ?>;
  const isLoggedIn = <?= isset($_SESSION['fullname']) ? 'true' : 'false' ?>;
  if (!isLoggedIn) {
    dot.style.display = 'none';
    return;
  }

  let booking = localStorage.getItem('booking_data_' + userId);
  let flightId = localStorage.getItem('selected_flight_' + userId);

  // Quick check for guest data to migrate visually
  if (!booking && !flightId) {
    booking = localStorage.getItem('booking_data_0');
    flightId = localStorage.getItem('selected_flight_0');
  }

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

function goToCheckout() {
  const userId = <?= $_SESSION['user_id'] ?? 0 ?>;
  if (!userId) {
    window.location.href = '/login.php';
    return;
  }

  let savedFlight = localStorage.getItem('selected_flight_' + userId);

  // Migrate from guest if exists
  if (!savedFlight) {
    const guestFlight = localStorage.getItem('selected_flight_0');
    if (guestFlight) {
      localStorage.setItem('selected_flight_' + userId, guestFlight);
      localStorage.removeItem('selected_flight_0');

      const guestBooking = localStorage.getItem('booking_data_0');
      if (guestBooking) {
        localStorage.setItem('booking_data_' + userId, guestBooking);
        localStorage.removeItem('booking_data_0');
      }
      savedFlight = guestFlight;
    }
  }

  if (savedFlight) {
    const flight = JSON.parse(savedFlight);
    if (flight && flight.id) {
      window.location.href = 'checkout.php?flight_id=' + flight.id;
      return;
    }
  }

  Swal.fire({
      icon: 'info',
      title: 'Chưa có thông tin đặt vé',
      text: 'Bạn chưa chọn chuyến bay nào. Vui lòng chọn chuyến bay trước khi thanh toán!',
      confirmButtonText: 'Quay lại trang đặt vé',
      allowOutsideClick: false,
      allowEscapeKey: false
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'cheap-tickets.php';
      }
    });
}

</script>


