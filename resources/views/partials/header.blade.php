@php
    $current_page = basename($_SERVER['PHP_SELF']);
@endphp

<div class="top-header">
  <div class="container top-header-inner">
    <div class="left-part">
      <img src="{{ asset('img/logoflynow.png') }}" alt="Flynow" class="logo-flynow">
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
      <li class="{{ Request::is('/') ? 'active' : '' }}"><a href="/">Trang chủ</a></li>
      <li class="{{ Request::is('about') ? 'active' : '' }}"><a href="{{ route('about') }}">Giới thiệu</a></li>
      <li class="{{ Request::is('cheap-tickets') ? 'active' : '' }}"><a href="{{ route('cheap-tickets') }}">Vé giá tốt</a></li>
      <li class="{{ Request::is('promotions') ? 'active' : '' }}"><a href="{{ route('promotions') }}">Tin khuyến mại</a></li>
      <li class="{{ Request::is('guide') ? 'active' : '' }}"><a href="{{ route('guide') }}">Hướng dẫn đặt vé</a></li>
      <li class="{{ Request::is('checkout') ? 'active' : '' }}">
        <a href="javascript:void(0)" onclick="goToCheckout()" id="checkoutMenuLink">
          Thanh toán
          <span id="checkoutDot" class="checkout-dot" style="display:none;"></span>
        </a>
      </li>
    </ul>

    <div class="user-info">
      @php
        $fullname = session('fullname') ?? ($_SESSION['fullname'] ?? null);
        $avatar = session('avatar') ?? ($_SESSION['avatar'] ?? null);
        $avatarPath = !empty($avatar) ? $avatar : 'img/default-avatar.png';
        if (strpos($avatarPath, 'http') !== 0 && strpos($avatarPath, '/') !== 0) {
            $avatarPath = '/' . $avatarPath;
        }
      @endphp

      @if($fullname)
      <div class="user-menu">
        <img src="{{ $avatarPath }}?v={{ time() }}"
            class="user-avatar">
        <span class="user-name">{{ $fullname }}</span>
        <span class="arrow">&#9662;</span>
        <ul class="dropdown">
          <li><a href="{{ route('profile') }}">Thông tin cá nhân</a></li>
          <li><a href="{{ route('my-tickets') }}">Vé của tôi</a></li>
          <li><a href="{{ route('logout') }}">Đăng xuất</a></li>
        </ul>
      </div>
      @else
      <button class="login-btn" onclick="window.location.href='{{ route('login') }}'">
      <i class="fa fa-user"></i> Đăng nhập
      </button>
      @endif
    </div>
  </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const dot = document.getElementById('checkoutDot');

  const userId = {{ session('user_id') ?? ($_SESSION['user_id'] ?? 0) }};
  const isLoggedIn = {{ $fullname ? 'true' : 'false' }};
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
  const userId = {{ session('user_id') ?? ($_SESSION['user_id'] ?? 0) }};
  if (!userId) {
    window.location.href = '{{ route('login') }}';
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
    window.location.href = '/checkout.php?flight_id=' + flight.id;
  } else {
    Swal.fire({
      icon: 'info',
      title: 'Chưa có thông tin đặt vé',
      text: 'Bạn chưa chọn chuyến bay nào. Vui lòng chọn chuyến bay trước khi thanh toán!',
      confirmButtonText: 'Quay lại trang đặt vé',
      allowOutsideClick: false,
      allowEscapeKey: false
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = '{{ route('cheap-tickets') }}';
      }
    });
  }
}
</script>
