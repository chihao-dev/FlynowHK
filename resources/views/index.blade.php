@extends('layouts.app')

@section('content')

<div class="background">
    <main class="container">
        <div class="top-section">

            <div class="left-col">

                <div class="card search-card">

                    <div class="search-tabs">

                        <button class="active">Tìm kiếm vé bay</button>

                    </div>

                    <form method="post" action="{{ route('cheap-tickets') }}" class="search-form">
                        @csrf
                        <div class="row row-trip-type">
                            <div class="trip-type-tabs">
                                <div class="trip-tab active" data-type="one-way" id="tab-one-way">
                                    Một chiều
                                </div>

                            </div>
                            <input type="hidden" name="trip_type" id="trip-type-input" value="one-way">
                        </div>
                        <div class="row" style="position: relative;">
                            <label>Điểm đi:</label>
                            <input type="text" id="departure-airport" name="from" required>

                            <span class="list-icon-wrap" data-input-id="departure-airport">
                                <i class="fa-solid fa-list-ul list-icon"></i>
                            </span>
                        </div>

                        <div class="swap-icon-wrap">
                            <i class="fa-solid fa-exchange-alt swap-icon" id="swap-icon"></i>
                        </div>

                        <div class="row" style="position: relative;">
                            <label>Điểm đến:</label>
                            <input type="text" id="arrival-airport" name="to" required>

                            <span class="list-icon-wrap" data-input-id="arrival-airport">
                                <i class="fa-solid fa-list-ul list-icon"></i>
                            </span>
                        </div>

                        <div class="row row-dates">
                            <div class="date-time-group">
                                <label><i class="fa-solid fa-calendar-alt"></i> Từ ngày:</label>
                                <div class="input-with-time">
                                    <input type="date" name="date_go" id="date-go-input" required>
                                </div>
                            </div>

                            <div class="date-time-group">
                                <label><i class="fa-solid fa-calendar-alt"></i> Đến ngày:</label>
                                <div class="input-with-time">
                                    <input type="date" name="date_return" id="date-return-input">
                                </div>
                            </div>
                        </div>


                        <button type="submit" name="search_submit" class="btn-primary">TÌM CHUYẾN BAY</button>
                    </form>

                </div>

            </div>

            <aside class="right-col">

                <div class="card banner">

                    <div class="swiper banner-slider">

                        <div class="swiper-wrapper">

                            <div class="swiper-slide">

                                <img src="{{ asset('img/combo_da_nang01.jpg') }}" alt="Đà Nẵng">

                            </div>

                            <div class="swiper-slide">

                                <img src="{{ asset('img/combo_da_lat01.jpg') }}" alt="Đà Lạt">

                            </div>

                            <div class="swiper-slide">

                                <img src="{{ asset('img/combo_phu_quoc01.jpg') }}" alt="Phú Quốc">

                            </div>

                            <div class="swiper-slide">

                                <img src="{{ asset('img/combo_ky_co01.jpg') }}" alt="Kỳ Co">

                            </div>

                        </div>

                    </div>

                </div>

            </aside>
        </div>
</div>
</main>
</div>


<div class="features-section">

    <div class="feature-item">
        <i class="fa fa-search-plus"></i>
        <h4>Dễ dàng tìm kiếm chuyến bay</h4>
        <p>Trên 100 hãng hàng không</p>
    </div>

    <div class="feature-item">
        <i class="fa fa-wallet"></i>
        <h4>Thanh toán nhanh chóng</h4>
        <p>Tiện lợi và tin cậy</p>
    </div>

    <div class="feature-item">
        <i class="fa fa-phone-volume"></i>
        <h4>Đặt vé máy bay 24/7</h4>
        <p>Chăm sóc tận tình chu đáo</p>
    </div>

    <div class="feature-item">
        <i class="fa fa-piggy-bank"></i>
        <h4>Săn vé máy bay giá rẻ</h4>
        <p>Khuyến mại quanh năm</p>
    </div>

</div>

<div class="contact-widget">
    <button class="contact-toggle-btn" onclick="document.querySelector('.contact-menu').classList.toggle('active')">
        <div class="icon-rotation-box">
            <i class="fa-solid fa-phone"></i> <i class="fa-solid fa-comments"></i> <i class="fa-solid fa-envelope"></i>
        </div>
        <i class="fa-solid fa-xmark close-icon"></i>
    </button>

    <div class="contact-menu">
        <div class="contact-list">

            <a href="sms:01234567890" class="contact-item">
                <div class="icon-box red-sms"><i class="fa-solid fa-sms"></i></div>
                <span>SMS</span>
            </a>

            <a href="https://m.me/yourpage" target="_blank" class="contact-item">
                <div class="icon-box blue-messenger"><i class="fa-brands fa-facebook-messenger"></i></div>
                <span>Messenger</span>
            </a>

            <a href="https://zalo.me/01234567890" target="_blank" class="contact-item zalo-item">
                <div class="icon-box green-zalo">
                    <img src="{{ asset('img/zalo-icon.png') }}" alt="Zalo" class="zalo-icon">
                </div>
                <span>Chat Zalo</span>
            </a>

            <a href="mailto:booking@flynow.vn" class="contact-item">
                <div class="icon-box orange-email"><i class="fa-solid fa-envelope"></i></div>
                <span>Email</span>
            </a>

            <a href="tel:01234567890" class="contact-item">
                <div class="icon-box green-call"><i class="fa-solid fa-phone"></i></div>
                <span>Gọi 01234567890</span>
            </a>

        </div>
    </div>
</div>

<script>
    const searchForm = document.querySelector('.search-form');
    const dateGoInput = document.getElementById('date-go-input');
    const dateReturnInput = document.getElementById('date-return-input');

    searchForm.addEventListener('submit', function (e) {
        const todayStr = new Date().toISOString().split('T')[0];
        const dateGo = dateGoInput.value;
        const dateReturn = dateReturnInput.value;

        if (dateGo && dateGo < todayStr) {
            alert("Ngày đi không được nhỏ hơn hôm nay.");
            dateGoInput.focus();
            e.preventDefault();
            return;
        }

        if (dateGo && dateReturn && dateReturn < dateGo) {
            alert("Ngày về không được nhỏ hơn ngày đi.");
            dateReturnInput.focus();
            e.preventDefault();
            return;
        }
    });

</script>

@endsection