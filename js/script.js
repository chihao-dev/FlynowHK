document.addEventListener('DOMContentLoaded', function() {
    
    const DEPARTURE_INPUT_ID = 'departure-airport';
    const ARRIVAL_INPUT_ID = 'arrival-airport';
    const API_AIRPORT_FLIGHTS = 'api/api_search_airport.php';
    const API_URL = API_AIRPORT_FLIGHTS + '?q=';
    
    const searchForm = document.getElementById('searchForm');
    const resultContainer = document.getElementById('search-results-container');
    
    const returnDateWrap = document.getElementById('return-date-wrap'); 
    const tripTabs = document.querySelectorAll('.trip-tab'); 
    const tripTypeInput = document.getElementById('trip-type-input'); 
    const returnDateInput = document.getElementById('date-return-input'); 


    function closeAllLists() {
        document.querySelectorAll(".autocomplete-items").forEach(el => el.remove());
    }

    function fetchAirportData(input, query, dropdown) {
        closeAllLists(); 
        
        dropdown.innerHTML = `<div style="padding: 10px; text-align: center;">Đang tải...</div>`;

        fetch(API_URL + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                dropdown.innerHTML = '';
                if (data.length === 0) {
                    dropdown.innerHTML = `<div style="padding: 10px;">Không tìm thấy sân bay.</div>`;
                    return;
                }
                
                data.forEach(airport => {
                    const item = document.createElement('div');
                    item.innerHTML = `<strong>${airport.name_vn}</strong> - ${airport.city_vn} (${airport.code})`;
                    
                    item.setAttribute('data-full-value', `${airport.name_vn} - ${airport.city_vn} (${airport.code})`);
                    item.setAttribute('data-code', airport.code);
                    
                    item.addEventListener('click', function() {
                        input.value = this.getAttribute('data-full-value');
                        closeAllLists();
                    });
                    dropdown.appendChild(item);
                });
            })
            .catch(error => {
                dropdown.innerHTML = `<div style="padding: 10px; color: red;">Lỗi tải dữ liệu.</div>`;
                console.error("Error fetching airport data:", error);
            });
    }

    function setupAutocomplete(inputId) {
        const input = document.getElementById(inputId);
        input.addEventListener("input", function() {
            const val = this.value;
            const parent = this.parentElement;
            let dropdown = parent.querySelector(".autocomplete-items");
            
            closeAllLists(); 
            
            if (val.length < 2) return;
            
            if (!dropdown) {
                dropdown = document.createElement('div');
                dropdown.className = 'autocomplete-items';
                parent.appendChild(dropdown);
            }
            
            fetchAirportData(input, val, dropdown);
        });
    }

    function setupListIconClick(inputId) {
        const input = document.getElementById(inputId);
        const parent = input.parentElement; 
        const listIconWrap = parent.querySelector('.list-icon-wrap');

        if (listIconWrap) {
            listIconWrap.addEventListener('click', function(e) { 
                e.stopPropagation();
                
                let dropdown = parent.querySelector(".autocomplete-items");
                if (!dropdown) {
                    dropdown = document.createElement('div');
                    dropdown.className = 'autocomplete-items';
                    parent.appendChild(dropdown);
                }
                
                fetchAirportData(input, '', dropdown); 
                input.focus();
            });
        }
    }


    function toggleTripType(activeTab) {
        tripTabs.forEach(tab => tab.classList.remove('active'));
        activeTab.classList.add('active');
        
        const type = activeTab.getAttribute('data-type');
        
        if (tripTypeInput) {
             tripTypeInput.value = type;
        }

        if (type === 'round') {
            if (returnDateWrap) returnDateWrap.style.display = 'flex'; 
            if (returnDateInput) returnDateInput.setAttribute('required', 'required');
        } else {
            if (returnDateWrap) returnDateWrap.style.display = 'none';
            if (returnDateInput) {
                returnDateInput.removeAttribute('required');
                returnDateInput.value = ''; 
            }
        }
    }

    tripTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            toggleTripType(this);
        });
    });
    
    const initialActiveTab = document.getElementById('tab-one-way');
    if (initialActiveTab) {
        toggleTripType(initialActiveTab);
    }
    
    
    function setupBookButtonHandlers() {
        document.querySelectorAll('.btn-book').forEach(button => {
            button.addEventListener('click', function() {
                const details = this.getAttribute('data-flight-details');
                const fromCode = this.getAttribute('data-from-code');
                const toCode = this.getAttribute('data-to-code');
                
                if (details) {
                    const bookingUrl = `booking.php?flight_data=${details}&from=${fromCode}&to=${toCode}`;
                    window.location.href = bookingUrl;
                } else {
                    alert('Lỗi: Không tìm thấy thông tin chuyến bay.');
                }
            });
        });
    }

    function renderResults(data, fromCode, toCode, dateGo) {
        let html = `<h3>Kết quả chuyến bay ${fromCode} → ${toCode} (${dateGo})</h3>`;

        if (data.length === 0) {
            html += '<p class="no-flight">Không tìm thấy chuyến bay phù hợp.</p>';
            resultContainer.innerHTML = html;
            return;
        }

        data.forEach((flight, index) => {
            const bookedNormal = flight.booked_normal || 0;
            const bookedPremium = flight.booked_premium || 0;

            flight.seats_normal_remaining = (flight.seats_normal || 0) - bookedNormal;
            flight.seats_premium_remaining = (flight.seats_premium || 0) - bookedPremium;

            const depTime = new Date(flight.departure_time).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
            const arrTime = new Date(flight.arrival_time).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
            const priceNumber = Number(flight.base_price) || 0;
            const formattedPrice = priceNumber.toLocaleString('vi-VN');


            html += `
            <div class="flight-card" style="display:flex; align-items:center; justify-content:space-between; padding:15px; border-radius:14px; box-shadow:0 8px 20px rgba(0,0,0,0.1); margin-bottom:20px; background:#f8faff; gap:15px;">

                <!-- Thông tin hãng bay -->
                <div class="flight-info" style="display:flex; align-items:center; gap:12px; min-width:180px; background:#e0f0ff; padding:10px; border-radius:10px; flex-shrink:0;">
                    <img class="airline-logo" src="${flight.logo_url || 'default-logo.png'}" alt="${flight.airline_name}" style="width:60px; height:60px; object-fit:contain; border-radius:8px;">
                    <div>
                        <strong style="font-size:18px;">${flight.airline_name}</strong>
                        <div class="flight-number" style="font-size:14px; color:#333;"><i class="fa-solid fa-hashtag"></i> ${flight.flight_number}</div>
                    </div>
                </div>

                <!-- Thời gian & hành trình -->
                <div class="flight-route" style="text-align:center; flex:1; background:#fff4e6; padding:10px 15px; border-radius:10px;">
                    <div class="time" style="font-weight:600; font-size:16px; color:#1a73e8;"><i class="fa-solid fa-clock"></i> ${depTime}</div>
                    <div class="plane-path" style="display:flex; align-items:center; justify-content:center; gap:8px; font-weight:bold; color:#1a73e8; margin:5px 0; font-size:16px;">
                        <span>${fromCode}</span>
                        <i class="fa-solid fa-plane" style="transform: rotate(90deg); font-size:20px;"></i>
                        <span>${toCode}</span>
                    </div>
                    <div class="time" style="font-weight:600; font-size:16px; color:#1a73e8;">${arrTime}</div>
                    <div style="margin-top:8px; font-size:14px; color:#555; display:flex; justify-content:center; gap:10px; flex-wrap:wrap;">
                        <span style="background:#d9f0ff; padding:4px 8px; border-radius:6px;"><i class="fa-solid fa-route"></i> ${flight.flight_type}</span>
                        <span style="background:#d9f0ff; padding:4px 8px; border-radius:6px;"><i class="fa-solid fa-ticket"></i> ${flight.ticket_type}</span>
                        <span style="background:#d9f0ff; padding:4px 8px; border-radius:6px;"><i class="fa-solid fa-hourglass-half"></i> ${flight.duration}</span>
                        <span style="background:#d9f0ff; padding:4px 8px; border-radius:6px;"><i class="fa-solid fa-suitcase-rolling"></i> ${flight.baggage_limit}kg</span>
                    </div>
                    <div style="margin-top:6px; font-size:14px; color:#333; display:flex; justify-content:center; gap:10px; flex-wrap:wrap;">
                        <span style="background:#ffe0e0; padding:4px 8px; border-radius:6px;"><i class="fa-solid fa-chair"></i> Thường: ${flight.seats_normal_remaining} / ${flight.seats_normal}</span>
                        <span style="background:#e0ffe4; padding:4px 8px; border-radius:6px;"><i class="fa-solid fa-couch"></i> Cao cấp: ${flight.seats_premium_remaining} / ${flight.seats_premium}</span>
                    </div>
                </div>

                <!-- Giá & nút -->
                <div class="flight-price" style="text-align:right; display:flex; flex-direction:column; align-items:flex-end; gap:10px; min-width:130px; background:#fff0f5; padding:10px; border-radius:10px;">
                    <div class="price" style="font-size:20px; font-weight:800; color:#0d3b66;"><i class="fa-solid fa-money-bill-wave"></i> ${formattedPrice}đ</div>
                    <button class="btn-book" style="padding:8px 16px; font-weight:600; border:none; border-radius:8px; background:#1a73e8; color:#fff; cursor:pointer; transition:0.3s;">
                        <i class="fa-solid fa-plane-departure"></i> Đặt vé
                    </button>
                </div>

            </div>
            `;
        });

        resultContainer.innerHTML = html;

        document.querySelectorAll('.btn-detail').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                const detailsDiv = document.getElementById(`details-${index}`);
                if(detailsDiv.style.display === 'block') {
                    detailsDiv.style.display = 'none';
                    this.textContent = 'Xem chi tiết';
                } else {
                    detailsDiv.style.display = 'block';
                    this.textContent = 'Thu gọn';
                }
            });
        });
    }


    function extractAirportCode(fullAirportString) {
        const match = fullAirportString.match(/\(([A-Z]{3})\)$/);
        return match ? match[1] : '';
    }

    searchForm.addEventListener('submit', function(e) {
        e.preventDefault(); 

        const formData = new FormData(searchForm);
        
        const fromValue = formData.get('from'); 
        const toValue = formData.get('to');
        const dateGo = formData.get('date_go');
        const tripType = formData.get('trip_type');
        const dateReturn = formData.get('date_return');

        const fromCode = extractAirportCode(fromValue);
        const toCode = extractAirportCode(toValue);
        console.log('DEBUG: From:', fromCode, 'To:', toCode, 'Date:', dateGo);
        
        if (!fromCode || !toCode || !dateGo) {
            resultContainer.innerHTML = '';
            alert('Vui lòng chọn Điểm đi, Điểm đến từ danh sách gợi ý và Ngày đi.');
            return;
        }

        if (tripType === 'round' && !dateReturn) {
            alert('Vui lòng chọn ngày về cho chuyến khứ hồi.');
            return;
        }

        const apiUrl = `${API_AIRPORT_FLIGHTS}?action=flights&from=${fromCode}&to=${toCode}&date_go=${dateGo}`;
        
        resultContainer.innerHTML = '<p style="text-align: center; padding: 40px;">Đang tìm kiếm chuyến bay...</p>';

        fetch(apiUrl)
        .then(response => {
            if (!response.ok) throw new Error('Lỗi tìm kiếm chuyến bay.');
            return response.json();
        })
        .then(data => {
            renderResults(data, fromCode, toCode, dateGo);
        })
        .catch(error => {
            resultContainer.innerHTML = '<p style="text-align: center; padding: 40px; color: red;">Đã xảy ra lỗi khi tìm kiếm chuyến bay.</p>';
            console.error('Lỗi tìm kiếm chuyến bay:', error);
        });
    });

    document.addEventListener("click", closeAllLists);
    setupAutocomplete(DEPARTURE_INPUT_ID);
    setupAutocomplete(ARRIVAL_INPUT_ID);
    setupListIconClick(DEPARTURE_INPUT_ID);
    setupListIconClick(ARRIVAL_INPUT_ID);
});