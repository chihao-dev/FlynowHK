console.log('cheap-ticket.js loaded');

const flightsData = window.FLIGHT_DATA?.flights || [];
let selectedDate = window.FLIGHT_DATA?.selectedDate || '';
let displayedFlights = flightsData.slice();
const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
let filterUsingDateBar = true;

let z = window.FLIGHT_DATA?.z || '';

window.addEventListener('DOMContentLoaded', () => {
    const hasPostData = window.FLIGHT_DATA?.hasPostData || false;

    if (hasPostData) {
        document.getElementById('btn-apply-filters')?.click();
    }
});

console.log(timezone);

function toVietnamDate(dateStr) {
    if (typeof dateStr === 'string') {
        return dateStr.substring(0, 10);
    }
    
    return new Date(dateStr).toLocaleDateString('sv-SE', { timeZone: 'Asia/Ho_Chi_Minh' });
}



function renderFlights(flights) {
displayedFlights = flights;
const container = document.getElementById('flights-container');
container.innerHTML = '';

if (flights.length === 0) {
    container.innerHTML = `<p style="text-align:center; color:#888;">Không có chuyến bay nào cho ngày này.</p>`;
    return;
}

flights.forEach(flight => {
    const depTime = new Date(flight.departure_time).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    const arrTime = new Date(flight.arrival_time).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    const depDate = new Date(flight.departure_time).toLocaleDateString('vi-VN', { weekday:'short', day:'2-digit', month:'2-digit', year:'numeric' }); // ngày khởi hành
    const formattedPrice = (Number(flight.base_price) || 0).toLocaleString('vi-VN');

    container.innerHTML += `
    <div class="flight-card" style="display:flex; align-items:center; justify-content:space-between; padding:15px; border-radius:14px; box-shadow:0 8px 20px rgba(0,0,0,0.1); margin-bottom:20px; background:#f8faff; gap:15px;">
        <div class="flight-info" style="display:flex; align-items:center; gap:12px; min-width:180px; background:#e0f0ff; padding:10px; border-radius:10px; flex-shrink:0;">
        <img src="${flight.logo_url}" alt="${flight.airline_name}" style="width:60px; height:60px; object-fit:contain; border-radius:8px;">
        <div>
            <strong style="font-size:18px;">${flight.airline_name}</strong>
            <div style="font-size:14px; color:#333;"><i class="fa-solid fa-hashtag"></i> ${flight.flight_number}</div>
        </div>
        </div>
        <div class="flight-route" style="text-align:center; flex:1; background:#fff4e6; padding:10px 15px; border-radius:10px;">
        <!-- Hiển thị ngày khởi hành -->
        <div style="font-size:14px; font-weight:600; margin-bottom:4px; color:#1a73e8;">
            <i class="fa-solid fa-calendar-days"></i> ${depDate}
        </div>
        <div style="font-weight:600; font-size:16px; color:#1a73e8;"><i class="fa-solid fa-clock"></i> ${depTime}</div>
        <div style="display:flex; align-items:center; justify-content:center; gap:8px; font-weight:bold; color:#1a73e8; margin:5px 0;">
            <span>${flight.departure_city}</span>
            <i class="fa-solid fa-plane" style="transform: rotate(90deg);"></i>
            <span>${flight.arrival_city}</span>
        </div>
        <div style="margin-top:6px; font-size:14px; color:#333; display:flex; flex-direction:column; gap:6px; align-items:center;">
            <div style="display:flex; justify-content:center; gap:10px; flex-wrap:wrap;">
            <span style="background:#d9f0ff; padding:4px 8px; border-radius:6px;"><i class="fa-solid fa-route"></i> ${flight.flight_type}</span>
            <span style="background:#d9f0ff; padding:4px 8px; border-radius:6px;"><i class="fa-solid fa-ticket"></i> ${flight.ticket_type}</span>
            <span style="background:#d9f0ff; padding:4px 8px; border-radius:6px;"><i class="fa-solid fa-hourglass-half"></i> ${flight.duration}</span>
            <span style="background:#d9f0ff; padding:4px 8px; border-radius:6px;"><i class="fa-solid fa-suitcase-rolling"></i> ${flight.baggage_limit}kg</span>
            </div>
            <div style="display:flex; justify-content:center; gap:10px; flex-wrap:wrap;">
            <span style="background:#ffe0e0; padding:4px 8px; border-radius:6px;"><i class="fa-solid fa-chair"></i> Thường: ${flight.seats_normal_remaining} / 60</span>
            <span style="background:#e0ffe4; padding:4px 8px; border-radius:6px;"><i class="fa-solid fa-couch"></i> Cao cấp: ${flight.seats_premium_remaining} / 40</span>
            </div>
        </div>
        </div>
        <div class="flight-price" style="text-align:right; display:flex; flex-direction:column; align-items:flex-end; gap:10px; min-width:130px; background:#fff0f5; padding:10px; border-radius:10px;">
        <div style="font-size:20px; font-weight:800; color:#0d3b66;"><i class="fa-solid fa-money-bill-wave"></i> ${formattedPrice}đ</div>
        <button onclick="handleBooking(${flight.id})"
                style="padding:8px 16px; font-weight:600; border:none; border-radius:8px; background:#1a73e8; color:#fff; cursor:pointer;">
            <i class="fa-solid fa-plane-departure"></i> Đặt vé
        </button>
        </div>
    </div>`;
});
}

function handleBooking(flightId) {
const oldBooking = JSON.parse(localStorage.getItem('booking_data') || '{}');
const oldFlight = JSON.parse(localStorage.getItem('selected_flight') || '{}');

if ((oldBooking && (oldBooking.passengers?.length > 0 || oldBooking.contactName)) && oldFlight?.id) {
    const confirmCancel = confirm(
    "⚠️ Bạn đang có vé đang chờ thanh toán.\nBạn có muốn hủy vé cũ và đặt vé mới không?"
    );

    if (!confirmCancel) {
    return;
    }

    localStorage.removeItem('booking_data');
    localStorage.removeItem('selected_flight');
}

const selectedFlight = {
    id: flightId,
    time: new Date().toISOString(), 
};

localStorage.setItem('selected_flight', JSON.stringify(selectedFlight));

window.location.href = `checkout.php?flight_id=${flightId}`;
}

renderFlights(flightsData);

document.getElementById('btn-apply-filters').addEventListener('click', () => {
    const airline = document.getElementById('filter-airline').value;
    const flightCode = document.getElementById('filter-flight-code').value.trim().toUpperCase();
    const dateFrom = document.getElementById('filter-date-from').value;
    const dateTo = document.getElementById('filter-date-to').value;
    filterUsingDateBar = false;

    const from = document.getElementById('filter-from').value.trim().toUpperCase();
    const to = document.getElementById('filter-to').value.trim().toUpperCase();

    const todayStr = new Date().toLocaleDateString('en-CA');

    const todayStrVN = toVietnamDate(new Date());
    if(dateFrom && dateFrom < todayStrVN){
        alert("⛔ Ngày bắt đầu không được nhỏ hơn hôm nay!");
        return;
    }
    if (dateFrom && dateTo && dateTo < dateFrom) {
        alert("⛔ Ngày kết thúc không được nhỏ hơn ngày bắt đầu!");
        return;
    }

    const today = new Date().toLocaleDateString('en-CA');

    const filtered = flightsData.filter(f => {
        const flightDate = new Date(f.departure_time).toLocaleDateString('en-CA');
        let ok = true;

        if (flightDate < today) return false;

        if (airline) ok = ok && f.airline_name === airline;
        if (flightCode) ok = ok && f.flight_number.toUpperCase().includes(flightCode);
        if (from) ok = ok && f.departure_airport.toUpperCase().includes(from);
        if (to) ok = ok && f.arrival_airport.toUpperCase().includes(to);
        if (dateFrom) ok = ok && flightDate >= dateFrom;
        if (dateTo) ok = ok && flightDate <= dateTo;

        return ok;
    });


    displayedFlights = filtered;
    renderFlights(displayedFlights);

    document.getElementById('flights-title').textContent = "Kết quả tìm kiếm";

    document.querySelectorAll('.date-item').forEach(el => el.classList.remove('active'));
});

const datesContainer = document.getElementById('dates-container');
const today = new Date();
const todayStr = toVietnamDate(today);

for (let i = 0; i < 30; i++) {
    const d = new Date(today);
    d.setDate(today.getDate() + i);
    const dateStr = d.toLocaleDateString('en-CA');

    const div = document.createElement('div');
    div.className = 'date-item';
    div.dataset.date = dateStr;

    if(new Date(dateStr) < new Date(todayStr)){
        div.classList.add('disabled'); 
    }

    div.innerHTML = `<div>${d.toLocaleDateString('vi-VN', { weekday: 'short' })}</div>
                    <div>${d.getDate()}/${d.getMonth()+1}</div>`;

    if (dateStr === todayStr) div.classList.add('active');

    div.addEventListener('click', () => {
        if(div.classList.contains('disabled')) return; 
        document.querySelectorAll('.date-item').forEach(el => el.classList.remove('active'));
        div.classList.add('active');
        filterByDate(dateStr);
    });

    datesContainer.appendChild(div);
}

document.querySelector('.scroll-left').addEventListener('click', () => {
datesContainer.scrollBy({ left: -120, behavior: 'smooth' });
});
document.querySelector('.scroll-right').addEventListener('click', () => {
datesContainer.scrollBy({ left: 120, behavior: 'smooth' });
});

if (selectedDate) {
filterByDate(selectedDate);

document.querySelectorAll('.date-item').forEach(el => el.classList.remove('active'));
const selectedItem = document.querySelector(`.date-item[data-date="${selectedDate}"]`);
if (selectedItem) selectedItem.classList.add('active');
} else {
filterByDate(todayStr);
const todayItem = document.querySelector(`.date-item[data-date="${todayStr}"]`);
if (todayItem) todayItem.classList.add('active');
}


const sortDateBtn = document.getElementById('sort-date');
const sortPriceBtn = document.getElementById('sort-price');

sortDateBtn.addEventListener('click', () => {
    const order = sortDateBtn.dataset.order;
    displayedFlights.sort((a,b) => order==='asc' ? new Date(a.departure_time)-new Date(b.departure_time) : new Date(b.departure_time)-new Date(a.departure_time));
    renderFlights(displayedFlights);
    sortDateBtn.dataset.order = order==='asc' ? 'desc':'asc';
    sortDateBtn.querySelector('i').className = order==='asc' ? 'fa-solid fa-arrow-right' : 'fa-solid fa-arrow-left';
});

sortPriceBtn.addEventListener('click', () => {
    const order = sortPriceBtn.dataset.order;

    if(order === 'asc'){
        displayedFlights.sort((a,b) => a.base_price - b.base_price);
        sortPriceBtn.querySelector('i').className = "fa-solid fa-arrow-up"; // ↑
    } else {
        displayedFlights.sort((a,b) => b.base_price - a.base_price);
        sortPriceBtn.querySelector('i').className = "fa-solid fa-arrow-down"; // ↓
    }

    renderFlights(displayedFlights);

    sortPriceBtn.dataset.order = order === 'asc' ? 'desc' : 'asc';
});


function filterByDate(selectedDate) {
    const todayStr = new Date().toLocaleDateString('sv-SE', { timeZone: 'Asia/Ho_Chi_Minh' });

    const filtered = flightsData.filter(flight => {
        const flightDate = flight.departure_time.substring(0, 10);

        if (flightDate < todayStr) return false;

        return flightDate === selectedDate;
    });

    console.log("Dữ liệu sau khi lọc cho ngày " + selectedDate + ":", filtered);
    
    document.getElementById('flights-title').textContent = "Lịch trình theo ngày";
    renderFlights(filtered);
}

const scrollContainer = document.querySelector(".dates-container");
document.querySelector(".scroll-left").addEventListener("click", () => {
scrollContainer.scrollBy({ left: -200, behavior: "smooth" });
});
document.querySelector(".scroll-right").addEventListener("click", () => {
scrollContainer.scrollBy({ left: 200, behavior: "smooth" });
});

const todayItem = document.querySelector(`.date-item[data-date="${todayStr}"]`);
if (todayItem) {
todayItem.classList.add('active');
}

document.getElementById('btn-clear-filters').addEventListener('click', () => {
    document.getElementById('filter-airline').value = '';
    document.getElementById('filter-flight-code').value = '';
    document.getElementById('filter-from').value = '';
    document.getElementById('filter-to').value = '';
    document.getElementById('filter-date-from').value = '';
    document.getElementById('filter-date-to').value = '';

    filterByDate(todayStr);
    document.getElementById('flights-title').textContent = "Lịch trình theo ngày";

    document.querySelectorAll('.date-item').forEach(el => el.classList.remove('active'));

    const todayItem = document.querySelector(`.date-item[data-date="${todayStr}"]`);
    if(todayItem) todayItem.classList.add('active');

    filterUsingDateBar = true;
});

document.addEventListener('DOMContentLoaded', function() {
    const flights = window.FLIGHT_DATA.flights || [];
    const hasPostData = window.FLIGHT_DATA.hasPostData;

    
    if (hasPostData && flights.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Không tìm thấy chuyến bay',
            text: 'Không có chuyến bay nào cho lựa chọn của bạn.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = window.location.pathname;
        });
    }
});

document.getElementById('btn-clear-filters').addEventListener('click', function() {
    document.getElementById('filter-airline').value = '';
    document.getElementById('filter-flight-code').value = '';
    document.getElementById('filter-from').value = '';
    document.getElementById('filter-to').value = '';
    document.getElementById('filter-date-from').value = '';
    document.getElementById('filter-date-to').value = '';
});

document.getElementById('btn-apply-filters').addEventListener('click', function() {
    const airline = document.getElementById('filter-airline').value;
    const flightCode = document.getElementById('filter-flight-code').value;
    const from = document.getElementById('filter-from').value;
    const to = document.getElementById('filter-to').value;
    const dateFrom = document.getElementById('filter-date-from').value;
    const dateTo = document.getElementById('filter-date-to').value;

    if (!airline && !flightCode && !from && !to && !dateFrom && !dateTo) {
        window.location.href = window.location.pathname; 
        return;
    }

    applyFilters(); 
});