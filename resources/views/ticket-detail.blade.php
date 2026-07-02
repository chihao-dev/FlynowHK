@extends('layouts.app')

@section('title', 'Chi tiết vé - FlyNow')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/my-tickets.css') }}">
@endpush

@section('content')
<div class="ticket-detail-page">
    <h1>🎟 Chi tiết vé máy bay</h1>

    <div class="section-title">Thông tin chuyến bay</div>
    <table class="details-table">
        <tr>
            <th>Hãng bay</th>
            <td>
                @if (!empty($ticket['logo_url']))
                <img src="{{ asset($ticket['logo_url']) }}"
                    alt="Logo {{ $ticket['airline_name'] }}"
                    style="height:40px; vertical-align:middle; margin-right:10px;">
                @endif
                {{ $ticket['airline_name'] }} ({{ $ticket['flight_number'] }})
            </td>
        </tr>
        <tr>
            <th>Điểm đi</th>
            <td>{{ $ticket['departure_airport'] }}</td>
        </tr>
        <tr>
            <th>Điểm đến</th>
            <td>{{ $ticket['arrival_airport'] }}</td>
        </tr>
        <tr>
            <th>Khởi hành</th>
            <td>{{ $depart }}</td>
        </tr>
        <tr>
            <th>Hạ cánh</th>
            <td>{{ $arrive }}</td>
        </tr>
        <tr>
            <th>Thời gian bay</th>
            <td>{{ $ticket['duration'] }}</td>
        </tr>
        <tr>
            <th>Loại chuyến bay</th>
            <td>{{ $ticket['flight_type_desc'] }}</td>
        </tr>
        <tr>
            <th>Loại vé</th>
            <td>{{ $ticket['flight_ticket_type'] }}</td>
        </tr>
        <tr>
            <th>Hạng vé</th>
            <td>{{ $ticketClassName }}</td>
        </tr>
        <tr>
            <th>Giá cơ bản</th>
            <td>{{ number_format($ticket['base_price'], 0, ",", ".") }} VNĐ</td>
        </tr>
        <tr>
            <th>Hành lý một người lớn <br> (Trẻ em = 75% với người lớn)</th>
            <td>{{ $ticket['baggage_limit'] }} kg</td>
        </tr>
    </table>


    <div class="section-title">Danh sách hành khách</div>
    <table class="details-table">
        <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Ngày sinh</th>
            <th>CCCD</th>
            <th>Hành lý (kg)</th>
            <th>Số ghế</th>
        </tr>
        @foreach ($passengers as $index => $p)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $p['name'] ?? 'Không có' }}</td>
            <td>{{ $p['dob'] ?? 'Không có' }}</td>
            <td>{{ !empty($p['doc']) ? $p['doc'] : 'Không có' }}</td>
            <td>{{ !empty($p['baggage']) ? $p['baggage'] : 'Không có' }}
                {{ $p['overWeight'] > 0 ? "(Vượt: {$p['overWeight']} kg)" : '' }}
            </td>
            <td>{{ !empty($seatNumbers[$index]) ? $seatNumbers[$index] : 'Không có' }}</td>
        </tr>
        @endforeach
    </table>



    <div class="section-title">Thông tin liên hệ</div>
    <table class="details-table">
        <tr>
            <th>Tên liên hệ</th>
            <td>{{ $ticket['contact_name'] }}</td>
        </tr>
        <tr>
            <th>SĐT</th>
            <td>{{ $ticket['contact_phone'] }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $ticket['contact_email'] }}</td>
        </tr>
        <tr>
            <th>Mã khuyến mãi</th>
            <td>{{ $ticket['promo_code'] ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Chi tiết tổng cộng</div>
    <div id="totalPrice" class="total-section">
        <div>- Vé người lớn: {{ $adult }} x {{ number_format($basePrice, 0, ",", ".") }} = {{ number_format($adultPrice, 0, ",", ".") }}đ</div>
        <div>- Vé trẻ em: {{ $child }} x {{ number_format($basePrice * 0.75, 0, ",", ".") }} = {{ number_format($childPrice, 0, ",", ".") }}đ</div>
        <div>- Vé em bé: {{ $baby }} x {{ number_format($basePrice * 0.5, 0, ",", ".") }} = {{ number_format($babyPrice, 0, ",", ".") }}đ</div>
        <div>- Phí hành lý vượt: {{ number_format($extraFees, 0, ",", ".") }}đ</div>
        @if ($discountValue > 0)
            <div>- {{ $discountText }}: -{{ number_format($discountValue, 0, ",", ".") }}đ</div>
        @endif
        <hr>
        <div><strong>Tổng cộng: {{ number_format($subtotal, 0, ",", ".") }}đ</strong></div>
    </div>

    <div class="qr-code">
        <img src="{{ $qrURL }}" alt="QR Code">
        <div class="booking-code">{{ $ticket['booking_code'] }}</div>
    </div>
    <div class="note">Vui lòng check-in tại quầy trước khi bay ✈</div>

    <div style="text-align:center; margin-top:20px;">
        <a href="{{ route('my-tickets') }}"
            style="padding:6px 12px; background:#007bff; color:#fff; border-radius:4px; text-decoration:none;">←
            Quay lại danh sách vé</a>
    </div>
</div>
@endsection