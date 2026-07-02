@extends('layouts.app')

@section('title', 'Đặt vé - ' . $flight['airline_name'])

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endpush

@section('content')
<div class="flights-page">
    <div class="container">
      <h2>Thông tin chuyến bay</h2>
      <div style="display:flex; justify-content:flex-end; margin-bottom:10px;">
        <button type="button" class="btn-cancel-booking" onclick="cancelBooking()">
          <i class="fa-solid fa-xmark"></i> Hủy đặt vé
        </button>
      </div>
      <div class="flight-info card p-3 mb-4" style="border:1px solid #ccc;border-radius:10px;">
        <div
          style="display:flex;align-items:center;gap:60px; background: linear-gradient(to bottom, #e3f0ff, #f1f7ff); border-left: 3px solid #d0e4ff;">
          <img src="{{ asset($flight['logo_url']) }}" alt="{{ $flight['airline_name'] }}" width="80">

          <div class="flight-details-info">
            <h3>{{ $flight['airline_name'] }}</h3>
            <div class="flight-columns">
              <ul class="flight-info-list">
                <li><i class="fa-solid fa-plane-up"></i> <strong>Số hiệu:</strong> {{ $flight['flight_number'] }}</li>
                <li><i class="fa-solid fa-location-dot"></i> <strong>Điểm đi:</strong> {{ $flight['departure_airport'] }}</li>
                <li><i class="fa-solid fa-location-dot"></i> <strong>Điểm đến:</strong> {{ $flight['arrival_airport'] }}</li>
                <li><i class="fa-solid fa-clock"></i> <strong>Khởi hành:</strong> {{ date("H:i d/m/Y", strtotime($flight['departure_time'])) }}</li>
                <li><i class="fa-solid fa-clock"></i> <strong>Đến nơi:</strong> {{ date("H:i d/m/Y", strtotime($flight['arrival_time'])) }}</li>
                <li><i class="fa-solid fa-hourglass-half"></i> <strong>Thời gian bay:</strong> {{ $flight['duration'] }}</li>
              </ul>

              <ul class="flight-info-list">
                <li><i class="fa-solid fa-ticket-simple"></i> <strong>Loại vé:</strong> {{ $flight['ticket_type'] }}</li>
                <li><i class="fa-solid fa-route"></i> <strong>Kiểu chuyến:</strong> {{ $flight['flight_type'] }}</li>
                <li><i class="fa-solid fa-suitcase"></i> <strong>Hành lý miễn phí:</strong> {{ $flight['baggage_limit'] }}kg</li>
                <li><i class="fa-solid fa-chair"></i> <strong>Ghế thường còn:</strong> {{ $flight['seats_normal'] }}</li>
                <li><i class="fa-solid fa-crown"></i> <strong>Ghế cao cấp còn:</strong> {{ $flight['seats_premium'] }}</li>
                <li><i class="fa-solid fa-money-bill-wave"></i> <strong>Giá vé thường:</strong> {{ number_format($flight['base_price'], 0, ',', '.') }}đ</li>
              </ul>
            </div>
          </div>


          <div class="ticket-selector-container">
            <label class="ticket-option ticket-normal">
              <input type="radio" name="ticket" id="ticketNormal" checked onclick="chooseTicket('normal')">
              <div class="option-content">
                <h4>Vé Thường</h4>
                <div class="price">{{ number_format($flight['base_price'], 0, ',', '.') }}đ</div>
                <div class="seats-left">Còn {{ $flight['seats_normal'] }} / 60 ghế</div>
              </div>
              <span class="checkmark"></span>
            </label>

            <label class="ticket-option ticket-premium">
              <input type="radio" name="ticket" id="ticketPremium" onclick="chooseTicket('premium')">
              <div class="option-content">
                <h4>Vé Cao Cấp</h4>
                <div class="price">{{ number_format($flight['base_price'] * 1.5, 0, ',', '.') }}đ</div>
                <div class="seats-left">Còn {{ $flight['seats_premium'] }} / 40 ghế</div>
              </div>
              <span class="checkmark"></span>
            </label>
          </div>
        </div>
      </div>

      <h3>Thông tin đặt vé</h3>
      <form id="bookingForm">
        @csrf
        <div class="passenger-count" style="display:flex; align-items:center; gap:10px;">
          <label>Người lớn:
            <input type="number" id="adultCount" value="1" min="1" max="10" style="width:60px;">
          </label>

          <label>Trẻ em:
            <input type="number" id="childCount" value="0" min="0" max="10" style="width:60px;">
          </label>

          <label>Em bé:
            <input type="number" id="babyCount" value="0" min="0" max="10" style="width:60px;">
          </label>

          <button type="button" class="btn-primary" id="updatePassengerBtn" style=" font-size:14px; width: 10%;">Cập nhật</button>
        </div>

        <div id="passengerInputs"></div>

        <h3>Thông tin liên hệ</h3>
        <div class="contact-info">
          <label><i class="fa-solid fa-user"></i> Họ tên:</label>
          <input type="text" id="contactName" placeholder="Nguyễn Văn A">

          <label><i class="fa-solid fa-phone"></i> Số điện thoại:</label>
          <input type="text" id="contactPhone" placeholder="0123456789">

          <label><i class="fa-solid fa-envelope"></i> Email:</label>
          <input type="email" id="contactEmail" placeholder="abc@example.com">
        </div>

        <div class="promo-code">
          <label for="promoCode">Mã khuyến mãi:</label>
          <input type="text" id="promoCode" placeholder="Nhập mã (nếu có)">
          <button type="button" class="btn-apply" onclick="applyPromo()">Áp dụng</button>
        </div>

        <div class="total" id="totalPrice">Tổng tiền: 0đ</div>
        <button type="button" class="btn-primary" id="payBtn">Thanh toán</button>

      </form>

    </div>
  </div>

  <div class="modal fade" id="seatSelectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content" style="border-radius:20px; overflow:hidden;">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">🛫 Chọn chỗ ngồi & Suất ăn</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="airplane-container">
            <h5 id="seatMapTitle"></h5>
            <div id="seatMap" class="seat-map-body"></div>
          </div>

          <div class="mt-3">
            <h6><i class="bi bi-info-circle"></i> Chú thích:</h6>
            <div class="d-flex flex-wrap gap-3">
              <div><span class="seat premium" style="width:20px;height:20px;"></span> Vé cao cấp</div>
              <div><span class="seat economy" style="width:20px;height:20px;"></span> Vé phổ thông</div>
              <div><span class="seat selected" style="width:20px;height:20px;"></span> Đang chọn</div>
              <div>
                <span class="seat occupied" style="width:20px;height:20px;">
                  <img src="{{ asset('img/logoflynow.png') }}" alt="Đã có người" class="seat-icon">
                </span> Đã có người
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button class="btn btn-success" id="confirmSeatBtn">Xác nhận chọn ghế</button>
        </div>
      </div>
    </div>
  </div>

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.checkoutData = {
      defaultAdultName: "{{ $defaultAdultName }}",
      priceNormal: {{ $flight['base_price'] }},
      baggageLimit: {{ $flight['baggage_limit'] }},
      baggageFeePerKg: 50000,
      promotions: @json($promotionsData),
      bookedSeats: @json($flightBookedSeats),
      flight_id: {{ $flight_id }},
      user_id: {{ $user_id }},
      departure: "{{ $flight['departure_airport'] }}",
      arrival: "{{ $flight['arrival_airport'] }}",
      airline_id: {{ $flight['airline_id'] }}
    };
  </script>
  <script src="{{ asset('js/checkout.js') }}"></script>
@endpush
@endsection