@extends('layouts.app')

@section('title', 'Danh sách chuyến bay - Flynow')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cheap-tickets.css') }}">
    <style>
        .flights-page {
            display: flex;
            gap: 20px;
            padding: 20px;
        }

        .flights-list {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
    </style>
@endpush

@section('content')
<div class="flights-page">
    <div class="filters">
        <h3>Bộ lọc chuyến bay</h3>
        <div class="filter-group">
            <label>Hãng bay:</label>
            <select id="filter-airline">
                <option value="">Tất cả</option>
                @php
                    $airlines = array_unique(array_column($flights, 'airline_name'));
                @endphp
                @foreach ($airlines as $al)
                    <option value="{{ $al }}" {{ ($al === ($airline ?? '')) ? 'selected' : '' }}>{{ $al }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Tìm theo mã hiệu chuyến bay:</label>
            <input type="text" id="filter-flight-code" placeholder="VD: VN123">
        </div>

        <div class="filter-group">
            <label>Điểm đi (From):</label>
            <select id="filter-from">
                <option value="">Tất cả</option>
                @php
                    $froms = array_unique(array_column($flights, 'departure_airport'));
                @endphp
                @foreach ($froms as $f)
                    <option value="{{ $f }}" {{ (($airports[$f] ?? $f) === ($from ?? '')) ? 'selected' : '' }}>
                        {{ $airports[$f] ?? $f }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label>Điểm đến (To):</label>
            <select id="filter-to">
                <option value="">Tất cả</option>
                @php
                    $tos = array_unique(array_column($flights, 'arrival_airport'));
                @endphp
                @foreach ($tos as $t)
                    <option value="{{ $t }}" {{ (($airports[$t] ?? $t) === ($to ?? '')) ? 'selected' : '' }}>
                        {{ $airports[$t] ?? $t }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label>Các vé từ ngày:</label>
            <input type="date" id="filter-date-from" value="{{ $date_go ?? '' }}">
        </div>
        <div class="filter-group">
            <label>Đến ngày:</label>
            <input type="date" id="filter-date-to" value="{{ $date_return ?? '' }}">
        </div>
        <button id="btn-apply-filters">Áp dụng</button>
        <button id="btn-clear-filters"
            style="margin-top:10px; background:#f44336; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer;">
            Xóa bộ lọc
        </button>
    </div>

    <div class="flights-list-wrapper">
        <div class="flights-controls">
            <div class="date-scroll">
                <button class="scroll-left"><i class="fa-solid fa-chevron-left"></i></button>
                <div class="dates-container" id="dates-container"></div>
                <button class="scroll-right"><i class="fa-solid fa-chevron-right"></i></button>
            </div>

            <div class="sort-buttons">
                <button id="sort-date" data-order="esc">Ngày <i class="fa-solid fa-arrow-right"></i></button>
                <button id="sort-price" data-order="asc">Giá <i class="fa-solid fa-arrow-up"></i></button>
            </div>
        </div>

        <h2 id="flights-title" style="margin-bottom:20px; color:#1a73e8;">Lịch trình theo ngày</h2>

        <div class="flights-list" id="flights-container"></div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.FLIGHT_DATA = {
            flights: @json($flights),
            selectedDate: "{{ $date_go }}",
            z: "{{ request('z', '') }}",
            hasPostData: {{ (request('from') || request('to') || request('date_go') || request('date_return') || request('airline')) ? 'true' : 'false' }}
        };
    </script>
    <script src="{{ asset('js/cheap-ticket.js') }}"></script>
@endpush
@endsection