@extends('layouts.app')

@section('title', 'Tin khuyến mãi - Flynow')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/promotions.css') }}">
    <style>
        .promo-status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .promo-status-badge.active {
            background: #d4f8e8;
            color: #0a7c45;
            border: 1px solid #a6e7c7;
        }
        .promo-status-badge.upcoming {
            background: #e8eaf6;
            color: #3949ab;
            border: 1px solid #c5cae9;
        }
    </style>
@endpush

@section('content')
<section class="promo-hero">
    <div class="promo-hero-content">
        <h1>Tin khuyến mãi &amp; Mã giảm giá hot nhất</h1>
        <p>Nhận ngay ưu đãi độc quyền từ Flynow – giảm giá cho các chuyến bay nội địa &amp; quốc tế, áp dụng cho các
            hãng hàng không hàng đầu Việt Nam.</p>
    </div>
</section>

<section class="promotions">
    <section class="promo-filter sticky-filter">
        <div class="filter-container">
            <input type="text" id="searchCode" placeholder="Tìm kiếm mã..." oninput="applyFilters()">

            <select id="filterAirline" onchange="applyFilters()">
                <option value="">-- Chọn hãng --</option>
                @foreach ($airlines as $a)
                    <option value="{{ $a['name'] }}">{{ $a['name'] }}</option>
                @endforeach
            </select>

            <select id="filterStatus" onchange="applyFilters()">
                <option value="">-- Trạng thái --</option>
                <option value="active">Còn hạn</option>
                <option value="upcoming">Chưa bắt đầu</option>
            </select>
        </div>
    </section>

    <div class="promo-grid" id="promoGrid">
        @foreach ($full_promotions as $promo)
        <div class="promo-card"
            data-code="{{ $promo['code'] }}"
            data-airline="{{ $promo['airline_name'] }}"
            data-status="{{ $promo['status'] }}"
            data-end="{{ strtotime($promo['end_date']) }}">
            <img src="{{ asset($promo['logo_url']) }}" alt="{{ $promo['airline_name'] }}">
            <div class="promo-info">
                {{-- Badge trạng thái --}}
                <span class="promo-status-badge {{ $promo['status'] }}">
                    @if($promo['status'] === 'active')
                        ✅ Còn hạn
                    @else
                        🕐 Chưa bắt đầu
                    @endif
                </span>

                <h3>{{ $promo['airline_name'] }} - Mã: <span class="code">{{ $promo['code'] }}</span></h3>
                <p class="promo-description">
                    {{ !empty($promo['description']) ? $promo['description'] : "Không có mô tả." }}
                </p>
                <p>Điểm đi:
                    <strong>{{ $promo['route_from'] === 'ALL' ? 'Tất cả' : $promo['route_from'] }}</strong>
                    → Điểm đến:
                    <strong>{{ $promo['route_to'] === 'ALL' ? 'Tất cả' : $promo['route_to'] }}</strong>
                </p>
                <p>Áp dụng từ {{ intval($promo['min_tickets']) }} vé trở lên</p>
                <p>Hạn dùng: {{ date('d/m/Y', strtotime($promo['start_date'])) }} →
                    {{ date('d/m/Y', strtotime($promo['end_date'])) }}</p>
                <button class="btn-copy" onclick="copyCode('{{ $promo['code'] }}')">Sao chép mã</button>
            </div>
        </div>
        @endforeach
    </div>
</section>

@push('scripts')
<script>
    function copyCode(code) {
        navigator.clipboard.writeText(code);
        Swal.fire({
            icon: 'success',
            title: 'Đã sao chép!',
            text: 'Mã khuyến mãi: ' + code,
            timer: 1500,
            showConfirmButton: false
        });
    }

    function applyFilters() {
        const search  = document.getElementById('searchCode').value.toLowerCase();
        const airline = document.getElementById('filterAirline').value;
        const status  = document.getElementById('filterStatus').value;

        document.querySelectorAll('#promoGrid .promo-card').forEach(card => {
            const code        = card.dataset.code.toLowerCase();
            const cardAirline = card.dataset.airline;
            const cardStatus  = card.dataset.status;

            let show = true;
            if (search  && !code.includes(search))         show = false;
            if (airline && airline !== cardAirline)         show = false;
            if (status  && status  !== cardStatus)          show = false;

            card.style.display = show ? 'block' : 'none';
        });
    }
</script>
@endpush
@endsection