document.addEventListener('DOMContentLoaded', function() {
    const API_URL = '/api/api_search_airport.php'; 

    function fetchAirportData(input, query, dropdown) {
        if (query.length < 2 && query !== '') {
            dropdown.innerHTML = '';
            return;
        }
        
        const url = API_URL + '?q=' + encodeURIComponent(query);
        
        dropdown.innerHTML = `<div>Đang tải sân bay...</div>`;
        
        fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Lỗi HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            dropdown.innerHTML = '';
            
            if (data && data.length > 0) {
                data.forEach(item => {
                    const displayValue = `${item.city_vn} - ${item.name_vn} (${item.code})`; 

                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'autocomplete-item'; 
                    itemDiv.innerHTML = displayValue; 
                    
                    itemDiv.addEventListener('click', function() {
                        input.value = displayValue; 
                        dropdown.innerHTML = ''; 
                    });

                    dropdown.appendChild(itemDiv);
                });
            } else if (query.length >= 2 || query === '') {
                dropdown.innerHTML = '<div>Không tìm thấy sân bay nào.</div>';
            }
        })
        .catch(error => {
            dropdown.innerHTML = '<div>Lỗi tải dữ liệu.</div>';
            console.error('Lỗi khi tải dữ liệu sân bay:', error);
        });
    }

    function setupAutocomplete(inputElementId) {
        const input = document.getElementById(inputElementId);
        const parent = input.parentElement;
        
        let dropdown = parent.querySelector(".autocomplete-items");
        if (!dropdown) {
            dropdown = document.createElement('div');
            dropdown.className = 'autocomplete-items';
            parent.appendChild(dropdown);
        }

        input.addEventListener('input', function() {
            const query = this.value.trim();
            
            dropdown.innerHTML = '';

            fetchAirportData(input, query, dropdown); 
        });
    }

    function setupListIconClick(inputElementId) {
        const input = document.getElementById(inputElementId);
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

    function setupSwapIcon() {
        const departureInput = document.getElementById('departure-airport');
        const arrivalInput = document.getElementById('arrival-airport');
        const swapIcon = document.getElementById('swap-icon');

        if (swapIcon) {
            swapIcon.addEventListener('click', function() {
                const tempValue = departureInput.value;
                departureInput.value = arrivalInput.value;
                arrivalInput.value = tempValue;
                this.classList.toggle('rotated');
            });
        }
    }


    setupAutocomplete('departure-airport'); 
    setupAutocomplete('arrival-airport');   
    
    setupListIconClick('departure-airport');
    setupListIconClick('arrival-airport');

    setupSwapIcon();

});

function renderResults(data, fromCode, toCode, dateGo) {

    data.forEach(flight => {
        const depTime = new Date(flight.departure_time).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
        const arrTime = new Date(flight.arrival_time).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
        const formattedPrice = Number(flight.price).toLocaleString('vi-VN');
        
        const flightDetailsJSON = encodeURIComponent(JSON.stringify({
            flight_number: flight.flight_number,
            airline_name: flight.airline_name,
            departure_time: flight.departure_time,
            arrival_time: flight.arrival_time,
            duration: flight.duration,
            price: flight.price,
            from: fromCode,
            to: toCode
        }));

        html += `
        <div class="flight-result-row">
            <div class="price-booking">
                <div class="price">${formattedPrice}đ</div>
                <button 
                    class="btn-book" 
                    data-flight-details="${flightDetailsJSON}" 
                    data-from-code="${fromCode}"
                    data-to-code="${toCode}"
                >CHỌN</button>
            </div>
        </div>
        `;
    });

    container.innerHTML = html;
    
    setupBookButtonHandlers(); 
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

