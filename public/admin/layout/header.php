<?php 
if (session_status() === PHP_SESSION_NONE) session_start(); 
$current_page = basename($_SERVER['PHP_SELF']); 
?>

<link rel="stylesheet" href="/admin/css/header.css">

<div class="top-header">
  <div class="container top-header-inner">
    <div class="left-part">
      <a href="/admin/">
        <img src="/img/logoflynow.png" alt="Flynow" class="logo-flynow">
      </a>
    </div>
    <div class="slogan-container">
      <span class="slogan">Vé máy bay, đặt mua vé máy bay tại đại lý vé máy bay Flynow</span>
    </div>
    <div class="right-part">
      <span class="hotline-label">Hotline:</span>
      <span class="hotline-number">1900 6432</span>
    </div>
  </div>
</div>

<header class="main-menu">
  <div class="container menu-inner">
    <ul class="menu-list">
    <li class="<?= ($current_page == 'dashboard.php' || $current_page == 'dashboard.php' || $current_page == 'dashboard.php') ? 'active' : '' ?>">
        <a href="/admin/dashboard.php">Dashboard</a>
    </li>
    <li class="<?= ($current_page == 'list_ticket.php' || $current_page == 'edit_ticket.php' || $current_page == 'add_ticket.php') ? 'active' : '' ?>">
          <a href="/admin/list_ticket.php">Danh sách chuyến bay</a>
      </li>
      <li class="<?= ($current_page == 'list_promotion.php' || $current_page == 'edit_promotion.php' || $current_page == 'add_promotion.php') ? 'active' : '' ?>">
        <a href="/admin/list_promotion.php">Danh sách khuyến mãi</a>
      </li>
      <li class="<?= ($current_page == 'manager_ticket.php' || $current_page == 'manager_ticket.php') ? 'active' : '' ?>">
        <a href="/admin/manager_ticket.php">Quản lý đặt vé</a>
      </li>
      <li class="<?= ($current_page == 'manager_user.php' || $current_page == 'manager_user.php') ? 'active' : '' ?>">
        <a href="/admin/manager_user.php">Quản lý người dùng</a>
      </li>
    </ul>

    <div class="user-info">
      <span class="admin-welcome">👋 Xin chào, <strong>Quản trị viên</strong></span>
      <a href="/logout.php" class="logout-btn">
         Đăng xuất
      </a>
    </div>
  </div>
</header>
