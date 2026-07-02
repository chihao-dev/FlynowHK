@extends('layouts.app')

@section('title', 'Vé của tôi - FlyNow')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/my-tickets.css') }}">
@endpush

@section('content')
<div class="flights-page">
    <h1>🎟 Vé máy bay của tôi</h1>

    @php $currentDate = ''; @endphp

    @forelse ($tickets as $ticket)
        @php
            $createdDate = date('d/m/Y', strtotime($ticket['created_at']));
            $qrURL = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($ticket['booking_code']);
            $depart = date('H:i d/m/Y', strtotime($ticket['departure_time']));
            $arrive = date('H:i d/m/Y', strtotime($ticket['arrival_time']));
            
            $isPremium = strtolower($ticket['ticket_type']) === 'cao cấp';
            $ticketClassName = $isPremium ? 'Premium Class' : 'Economy Class';
            $ticketClassCss = $isPremium ? 'premium' : 'normal';
            
            // Assume Controller/Service already calculated final_price and promoText
            // If not, we can do simple logic here or better in Service. 
            // Based on MyTicketController, it returns what the service gives.
            $final_price = $ticket['final_price'] ?? $ticket['total_price'];
            $promoText = $ticket['promo_text'] ?? '';
        @endphp

        @if ($createdDate !== $currentDate)
            @php $currentDate = $createdDate; @endphp
            <div class="date-divider"> Ngày {{ $currentDate }}</div>
        @endif

        <div class="ticket">
            <div class="ticket-class-banner {{ $ticketClassCss }}">
                {{ $ticketClassName }}
            </div>

            <div class="ticket-left">
                <div class="airline-info">
                    @if ($ticket['logo_url'])
                        <img src="{{ asset($ticket['logo_url']) }}" alt="Logo Hãng">
                    @endif
                    <span>{{ $ticket['airline_name'] }} - {{ $ticket['flight_number'] }}</span>
                </div>

                <div class="flight-details">
                    <div class="flight-route">{{ $ticket['departure_airport'] }} ✈ {{ $ticket['arrival_airport'] }}</div>
                    <div class="flight-time">{{ $depart }} → {{ $arrive }}</div>
                    <div class="info-group">
                        <span class="info-label">Loại vé:</span>
                        <span class="info-value">{{ $ticketClassName }}</span>
                    </div>
                </div>

                <div class="passenger-info">
                    <div class="info-group">
                        <span class="info-label">Tên liên hệ:</span>
                        <span class="info-value">{{ $ticket['contact_name'] }}</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">SĐT:</span>
                        <span class="info-value">{{ $ticket['contact_phone'] }}</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $ticket['contact_email'] }}</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Số ghế:</span>
                        <span class="info-value">{{ $ticket['seat_numbers'] }}</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Số lượng hành khách:</span>
                        <span class="info-value">{{ $ticket['people_count'] }}</span>
                    </div>
                </div>

                <div class="price">
                    Tổng tiền: {{ number_format($final_price, 0, ',', '.') }} VNĐ
                    <span style="color:#28a745; font-size:14px;">{{ $promoText }}</span>
                </div>
            </div>

            <div class="ticket-right">
                <div class="qr-code">
                    <img src="{{ $qrURL }}" alt="QR Code">
                </div>
                <div class="booking-code">{{ $ticket['booking_code'] }}</div>
                <div class="note">Vui lòng check-in tại quầy trước khi bay ✈</div>

                <div style="margin-top:10px;">
                    <a href="{{ route('ticket-detail', ['id' => $ticket['id']]) }}"
                        style="padding: 6px 12px; background:#007bff; color:#fff; border-radius:4px; text-decoration:none;">
                        Xem thông tin
                    </a>
                </div>
            </div>
        </div>
    @empty
        <p style="text-align:center; color:#555;">Bạn chưa có vé máy bay nào được thanh toán.</p>
    @endforelse
</div>
@endsection