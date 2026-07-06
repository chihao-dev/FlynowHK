const defaultAdultName = window.checkoutData.defaultAdultName;
const priceNormal = window.checkoutData.priceNormal;
const departure = window.checkoutData.departure;
const arrival = window.checkoutData.arrival;
const airline = window.checkoutData.airline;
const baggageLimit = window.checkoutData.baggageLimit;
const baggageFeePerKg = window.checkoutData.baggageFeePerKg;
const promotions = window.checkoutData.promotions;
const seatsOccupiedServer = window.checkoutData.bookedSeats;
let preventAutoSave = false;

  document.addEventListener('DOMContentLoaded', () => {
      const userId = window.checkoutData?.user_id || 0;
      const bookingKey = 'booking_data_' + userId;

      const saved = localStorage.getItem(bookingKey);
      let data = null;

      if (saved) {
          data = JSON.parse(saved);
          document.getElementById('contactName').value = data.contactName || '';
          document.getElementById('contactPhone').value = data.contactPhone || '';
          document.getElementById('contactEmail').value = data.contactEmail || '';
          document.getElementById('promoCode').value = data.promoCode || '';
          document.getElementById('adultCount').value = data.adult || 1;
          document.getElementById('childCount').value = data.child || 0;
          document.getElementById('babyCount').value = data.baby || 0;
          if (data.ticketType === 'premium')
              document.getElementById('ticketPremium').checked = true;
      }

      renderPassengerInputs();

      if (data && Array.isArray(data.passengers)) {
          const cards = document.querySelectorAll('.passenger-card');
          data.passengers.forEach((p, i) => {
              const card = cards[i];
              if (!card) return;
              const nameInput = card.querySelector('input[type="text"]');
              const genderSelect = card.querySelector('select');
              const dobInput = card.querySelector('input[type="date"]');
              const docInput = card.querySelector('input[placeholder="123456789"]');
              const baggageInput = card.querySelector('.baggage-input');


              if (nameInput) nameInput.value = p.name || '';
              if (genderSelect) genderSelect.value = p.gender || '';
              if (dobInput) dobInput.value = p.dob || '';
              if (docInput) docInput.value = p.doc || '';
              if (baggageInput) baggageInput.value = p.baggage || '';

              const warn = card.querySelector('.baggage-warning');
              if (p.extraFee > 0 && warn) {
                warn.textContent = p.overWeight || `Phí hành lý vượt mức: ${p.extraFee.toLocaleString('vi-VN')}đ`;
                warn.dataset.extrafeefor = p.extraFee;
              } else if (warn) {
                warn.textContent = '';
                warn.dataset.extrafeefor = 0;
              }
          });
      }

      const updateBtn = document.getElementById('updatePassengerBtn');
      if (updateBtn) updateBtn.addEventListener('click', renderPassengerInputs);

      ['adultCount', 'childCount', 'babyCount'].forEach(id => {
          const el = document.getElementById(id);
          if (el) el.addEventListener('input', updateTotal);
      });

      document.querySelectorAll('input, select, textarea').forEach(el => {
          el.addEventListener('change', saveBookingData);
      });

      window.addEventListener('beforeunload', saveBookingData);
      updateTotal();
  });

  function saveBookingData() {
    const userId = window.checkoutData?.user_id || 0;
    const bookingKey = 'booking_data_' + userId;

    const booking = {
      contactName: document.getElementById('contactName')?.value || '',
      contactPhone: document.getElementById('contactPhone')?.value || '',
      contactEmail: document.getElementById('contactEmail')?.value || '',
      promoCode: document.getElementById('promoCode')?.value || '',
      adult: parseInt(document.getElementById('adultCount')?.value || 1),
      child: parseInt(document.getElementById('childCount')?.value || 0),
      baby: parseInt(document.getElementById('babyCount')?.value || 0),
      ticketType: document.getElementById('ticketPremium')?.checked ? 'premium' : 'normal',
      passengers: []
    };

    document.querySelectorAll('.passenger-card').forEach((card) => {
      const warn = card.querySelector('.baggage-warning');
      const passenger = {
        type: card.dataset.type,
        gender: card.querySelector('select')?.value || '',
        name: card.querySelector('input[type="text"]')?.value || '',
        dob: card.querySelector('input[type="date"]')?.value || '',
        doc: card.querySelector('input[placeholder="123456789"]')?.value || '',
        baggage: card.querySelector('.baggage-input')?.value || '',
        extraFee: warn ? +(warn.dataset.extrafeefor || 0) : 0,
        overWeight: warn ? warn.textContent : ''
      };
      booking.passengers.push(passenger);
    });

    localStorage.setItem(bookingKey, JSON.stringify(booking));
  }

  function renderPassengerInputs() {
    const userId = window.checkoutData?.user_id || 0;
    const bookingKey = 'booking_data_' + userId;

    const adult = +document.getElementById('adultCount').value || 1;
    const child = +document.getElementById('childCount').value || 0;
    const baby = +document.getElementById('babyCount').value || 0;
    const baggageLimit = window.checkoutData.baggageLimit;
    const baggageFeePerKg = 50000;

    const prevData = { adult: [], child: [], baby: [] };
    document.querySelectorAll('.passenger-card').forEach(card => {
      const type = card.dataset.type;
      const gender = card.querySelector('select')?.value || '';
      const name = card.querySelector('input[type="text"]')?.value || '';
      const birth = card.querySelector('input[type="date"]')?.value || '';
      const cccd = card.querySelector('input[placeholder="123456789"]')?.value || '';
      const baggage = card.querySelector('.baggage-input')?.value || '';
      const baggageWarn = card.querySelector('.baggage-warning')?.textContent || '';
      const ageWarn = card.querySelector('.age-warning')?.textContent || '';
      prevData[type].push({ gender, name, birth, cccd, baggage, baggageWarn, ageWarn });
    });

    for (let key of ['adult', 'child', 'baby']) {
      prevData[key] = prevData[key].filter(d =>
        d.name.trim() || d.baggage || d.birth || d.ageWarn || d.baggageWarn
      );
    }

    let html = `<h4>Danh sách hành khách (${adult + child + baby})</h4>`;

    function renderCard(type, i, placeholders, fixed = false) {
      let genderOptions = '';
      if (type === 'adult') genderOptions = `<option value="Ông">Ông</option><option value="Bà">Bà</option>`;
      else if (type === 'child') genderOptions = `<option value="Trai">Trai</option><option value="Gái">Gái</option>`;
      else if (type === 'baby') genderOptions = `<option value="Bé trai">Bé trai</option><option value="Bé gái">Bé gái</option>`;

      return `
        <div class="passenger-card" data-type="${type}"
            style="border:1px solid #ccc; padding:10px; margin-bottom:10px; border-radius:6px;">
          <h5>
            ${type === 'adult' ? '(Trên 12 tuổi)<br><br>Người lớn' : type === 'child' ? '(Từ 2 đến dưới 12 tuổi)<br><br>Trẻ em' : '(Dưới 2 tuổi)<br><br>Em bé'} ${i}
            ${fixed ? '' : `<button type="button" class="btn-delete" onclick="removePassenger(this)" style="float:right;"><i class="fa-solid fa-trash"></i></button>`}
          </h5>
          <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
            <label>Giới tính:<select>${genderOptions}</select></label>
            <label>Họ tên:<input type="text" placeholder="${placeholders.name}" value="${placeholders.name}"></label>
            <label>Ngày sinh:
              <input type="date" max="${new Date().toISOString().split('T')[0]}">
            </label>            ${placeholders.cccd ? `<label>CCCD:<input type="text" placeholder="${placeholders.cccd}"></label>` : ''}
            ${placeholders.baggage ? `<label>Hành lý (kg): <input type="number" class="baggage-input" min="0" value="${type === 'child' ? Math.floor(baggageLimit * 0.75) : baggageLimit}"> </label>` : ''}
          </div>
          <div class="age-warning" style="font-size:14px; margin-top:5px;"></div>
          <div class="baggage-warning" style="color:red; font-size:14px; margin-top:5px;"></div>
        </div>`;
    }

      html += renderCard('adult', 1, { name: defaultAdultName, cccd: '123456789', baggage: true }, true);
      for (let i = 2; i <= adult; i++) html += renderCard('adult', i, { name: '', cccd: '', baggage: true });
      for (let i = 1; i <= child; i++) html += renderCard('child', i, { name: '', baggage: true });
      for (let i = 1; i <= baby; i++) html += renderCard('baby', i, { name: '' });

      document.getElementById('passengerInputs').innerHTML = html;

      document.querySelectorAll('.passenger-card').forEach(card => {
          const type = card.dataset.type;
          const dataArr = prevData[type];
          if (dataArr && dataArr.length > 0) {
              const data = dataArr.shift();
              if (!data) return;

              const nameInput = card.querySelector('input[type="text"]');
              const genderSelect = card.querySelector('select');
              const dobInput = card.querySelector('input[type="date"]');
              const docInput = card.querySelector('input[placeholder="123456789"]');
              const baggageInput = card.querySelector('.baggage-input');
              const ageWarn = card.querySelector('.age-warning');
              const warn = card.querySelector('.baggage-warning');

              if (nameInput) nameInput.value = data.name || '';
              if (genderSelect) genderSelect.value = data.gender || '';
              if (dobInput) dobInput.value = data.birth || '';
              if (docInput) docInput.value = data.cccd || '';
              if (baggageInput) baggageInput.value = data.baggage || '';
              if (ageWarn) ageWarn.textContent = data.ageWarn || '';
              if (warn) {
                  warn.textContent = data.baggageWarn || '';
                  warn.dataset.extrafeefor = data.baggageWarn ? +(warn.dataset.extrafeefor || 0) : 0;
              }
          }
      });

      updateTotal();

      document.querySelectorAll('.passenger-card input[type="date"]').forEach(input => {
        input.addEventListener('change', function () {
          const card = this.closest('.passenger-card');
          const type = card.dataset.type;
          const birthDate = new Date(this.value);
          const today = new Date();
          if (isNaN(birthDate)) return;
          let age = today.getFullYear() - birthDate.getFullYear();
          const mDiff = today.getMonth() - birthDate.getMonth();
          const dDiff = today.getDate() - birthDate.getDate();
          if (mDiff < 0 || (mDiff === 0 && dDiff < 0)) age--;
          let msg = '';
          if (type === 'baby' && age >= 2) msg = '❌ Em bé phải dưới 2 tuổi.';
          else if (type === 'child' && (age < 2 || age >= 12)) msg = '❌ Trẻ em phải từ 2 đến dưới 12 tuổi.';
          else if (type === 'adult' && age < 12) msg = '❌ Người lớn phải từ 12 tuổi trở lên.';
          const warn = card.querySelector('.age-warning');
          warn.textContent = msg;
          warn.style.color = msg ? 'red' : 'green';
          if (msg) {
            this.value = '';
            setTimeout(() => alert(msg), 100);
          }
        });
      });

      document.querySelectorAll('.baggage-input').forEach(input => {
        input.addEventListener('input', function () {
          const card = this.closest('.passenger-card');
          const warn = card.querySelector('.baggage-warning');
          const weight = +this.value || 0;

          let effectiveLimit = baggageLimit;
          if (card.dataset.type === 'child') effectiveLimit = baggageLimit * 0.75;

          const over = Math.max(0, weight - effectiveLimit);
          const extraFee = over * baggageFeePerKg;

          if (over > 0) {
            warn.textContent = `Phí hành lý vượt mức: ${extraFee.toLocaleString('vi-VN')}đ (vượt ${over}kg)`;
            warn.dataset.extrafeefor = extraFee;
          } else {
            warn.textContent = '';
            warn.dataset.extrafeefor = 0;
          }
          updateTotal();
        });
      });
      saveBookingData();
  }

  function removePassenger(btn) {
      const card = btn.closest('.passenger-card');
      const type = card.dataset.type;
      card.remove();

      const countInputMap = {
          adult: 'adultCount',
          child: 'childCount',
          baby: 'babyCount'
      };
      const input = document.getElementById(countInputMap[type]);
      if (input && +input.value > 0) {
          input.value = +input.value - 1;
      }

      renderPassengerInputs();
      updateTotal();
  }

  function applyPromo() {
      const codeInput = document.getElementById('promoCode');
      const code = codeInput.value.trim().toUpperCase();
      const adult = +document.getElementById('adultCount').value || 0;
      const child = +document.getElementById('childCount').value || 0;
      const baby = +document.getElementById('babyCount').value || 0;
      const totalPeople = adult + child + baby;

      let valid = false;
      let promoValue = 0;

      for (let promo of promotions) {
          if (promo.code.toUpperCase() === code) {
              const airlineMatch = !promo.airline_id || promo.airline_id.toString() === window.checkoutData.airline_id.toString();

              const fromOk = promo.route_from === 'ALL' || departure.trim().toUpperCase() === promo.route_from.trim().toUpperCase();
              const toOk = promo.route_to === 'ALL' || arrival.trim().toUpperCase() === promo.route_to.trim().toUpperCase();
              const routeMatch = fromOk && toOk;

              const minTicketsOk = totalPeople >= parseInt(promo.min_tickets);

              const today = new Date();
              const start = new Date(promo.start_date + 'T00:00:00');
              const end = new Date(promo.end_date + 'T23:59:59');
              const dateOk = today >= start && today <= end;

              if (airlineMatch && routeMatch && minTicketsOk && dateOk) {
                  valid = true;
                  promoValue = promo.discount_type === 'fixed'
                      ? parseFloat(promo.discount_value)
                      : promo.discount_value + '%';
                  break;
              }
          }
      }

      if (valid) {
          Swal.fire('Áp dụng thành công', 'Mã khuyến mãi đã được áp dụng!', 'success');
          codeInput.dataset.discount = promoValue;
      } else {
          Swal.fire('Không hợp lệ', 'Mã khuyến mãi không phù hợp điều kiện.', 'error');
          codeInput.dataset.discount = '';
      }

      updateTotal();
  }


  function updateTotal() {
      const adult = +document.getElementById('adultCount').value || 0;
      const child = +document.getElementById('childCount').value || 0;
      const baby = +document.getElementById('babyCount').value || 0;

      const ticketType = document.getElementById('ticketNormal').checked ? 'normal' : 'premium';
      let basePrice = ticketType === 'premium' ? priceNormal * 1.5 : priceNormal;

      const adultPrice = adult * basePrice;
      const childPrice = child * basePrice * 0.75;
      const babyPrice = baby * basePrice * 0.5;

      let extraFees = 0;
      document.querySelectorAll('.baggage-warning').forEach(w => {
          extraFees += +w.dataset.extrafeefor || 0;
      });

      let subtotal = adultPrice + childPrice + babyPrice + extraFees;

      const promo = document.getElementById('promoCode').dataset.discount || 0;
      let discountText = '';
      let discountValue = 0;

      if (typeof promo === 'string' && promo.endsWith('%')) {
          const percent = parseFloat(promo.replace('%',''));
          if (!isNaN(percent)) {
              discountValue = subtotal * (percent / 100);
              subtotal -= discountValue;
              discountText = `Khuyến mãi ${percent}%`;
          }
      } else if (!isNaN(promo) && promo > 0) {
          discountValue = +promo;
          subtotal -= discountValue;
          discountText = 'Khuyến mãi cố định';
      }

      if (subtotal < 0) subtotal = 0;


      const totalEl = document.getElementById('totalPrice');
      totalEl.innerHTML = `
          <div>- Vé người lớn: ${adult} x ${basePrice.toLocaleString('vi-VN')} = ${adultPrice.toLocaleString('vi-VN')}đ</div>
          <div>- Vé trẻ em: ${child} x ${(basePrice*0.75).toLocaleString('vi-VN')} = ${childPrice.toLocaleString('vi-VN')}đ</div>
          <div>- Vé em bé: ${baby} x ${(basePrice*0.5).toLocaleString('vi-VN')} = ${babyPrice.toLocaleString('vi-VN')}đ</div>
          <div>- Phí hành lý vượt: ${extraFees.toLocaleString('vi-VN')}đ</div>
          ${discountValue > 0 ? `<div>- ${discountText}: -${discountValue.toLocaleString('vi-VN')}đ</div>` : ''}
          <div id="totalPriceContainer">
              <div id="selectedSeatsInfo" style="margin-top:15px;font-weight:bold;color:#0069d9;"></div>
              <button id="chooseSeatBtn" type="button" class="btn-choose-seat">Chọn ghế</button>
          </div>
          <hr>
          <div><strong>Tổng cộng: ${subtotal.toLocaleString('vi-VN')}đ</strong></div>
      `;
      totalEl.dataset.total = subtotal;
    renderSelectedSeatsInfo();

    const chooseSeatBtn = document.getElementById('chooseSeatBtn');
    if (chooseSeatBtn) {
      chooseSeatBtn.addEventListener('click', chooseSeatBtnHandler);
    }
  }

  function chooseTicket(type) {
      const normal = document.getElementById('ticketNormal');
      const premium = document.getElementById('ticketPremium');

      if(type === 'normal') normal.checked = true;
      else premium.checked = true;

      updateTotal();
  }

  window.addEventListener('beforeunload', () => {
      if (preventAutoSave) return;

      const userId = window.checkoutData?.user_id || 0;
      const bookingKey = 'booking_data_' + userId;

      const prevBooking = JSON.parse(localStorage.getItem(bookingKey) || '{}');

      const data = {
          contactName: document.getElementById('contactName').value,
          contactPhone: document.getElementById('contactPhone').value,
          contactEmail: document.getElementById('contactEmail').value,
          adult: document.getElementById('adultCount').value,
          child: document.getElementById('childCount').value,
          baby: document.getElementById('babyCount').value,
          promoCode: document.getElementById('promoCode').value,
          ticketType: document.getElementById('ticketNormal').checked ? 'normal' : 'premium',
          passengers: prevBooking.passengers || [],
      };

      localStorage.setItem(bookingKey, JSON.stringify(data));
  });

  function cancelBooking() {
    Swal.fire({
      title: 'Hủy quá trình đặt vé?',
      text: 'Thông tin bạn đã nhập sẽ bị xóa. Bạn có muốn quay lại trang chọn chuyến bay không?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Đồng ý',
      cancelButtonText: 'Không, tiếp tục đặt',
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6'
    }).then((result) => {
      if (result.isConfirmed) {
        resetBookingForm();
        Swal.fire({
          icon: 'success',
          title: 'Đã hủy!',
          text: 'Đang chuyển hướng về trang chọn chuyến bay...',
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          window.location.href = 'cheap-tickets.php';
        });
      }
    });
  }

  function validateBookingForm() {
      let valid = true;
      let msg = '';

      const contactName = document.getElementById('contactName')?.value.trim();
      const contactPhone = document.getElementById('contactPhone')?.value.trim();
      const contactEmail = document.getElementById('contactEmail')?.value.trim();

      if (!contactName) { valid = false; msg = 'Vui lòng nhập họ tên liên hệ.'; }
      else if (!contactPhone) { valid = false; msg = 'Vui lòng nhập số điện thoại.'; }
      else if (!contactEmail) { valid = false; msg = 'Vui lòng nhập email.'; }

      if (valid) {
          const passengerCards = document.querySelectorAll('.passenger-card');
          passengerCards.forEach((card, index) => {
              const name = card.querySelector('input[type="text"]')?.value.trim();
              const dob = card.querySelector('input[type="date"]')?.value.trim();
              const gender = card.querySelector('select')?.value.trim();
              if (!name || !dob || !gender) {
                  valid = false;
                  msg = `Vui lòng điền đầy đủ thông tin cho hành khách thứ ${index + 1}.`;
              }
          });
      }

      if (!valid) Swal.fire('Thông tin chưa đầy đủ', msg, 'warning');
      return valid;
  }

  let currentPassengerIndex = 0;

  function getSelectedTicketClass() {
      const normal = document.getElementById('ticketNormal');
      const premium = document.getElementById('ticketPremium');
      if (normal.checked) return 'economy';
      if (premium.checked) return 'premium';
      return 'economy';
  }

  function chooseSeatBtnHandler() {
      saveBookingData();
      if (!validateBookingForm()) return;

      currentPassengerIndex = 0;
      renderSeatMap();
      const modalEl = document.getElementById('seatSelectionModal');
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
  }

  function renderSeatMap() {
      const userId = window.checkoutData?.user_id || 0;
      const bookingKey = 'booking_data_' + userId;

      const seatMap = document.getElementById('seatMap');
      seatMap.innerHTML = '';

      const rowLabels = 'ABCDEFGHIJ'.split('');
      const cols = 10;
      const premiumRows = ['A','B','C','D'];
      const exitRows = ['E'];

      const passengerCards = document.querySelectorAll('.passenger-card');
      const bookingData = JSON.parse(localStorage.getItem(bookingKey) || '{}');
      if (!Array.isArray(bookingData.selectedSeats)) bookingData.selectedSeats = [];

      let passengerSelect = document.getElementById('passengerSelect');
      if (!passengerSelect) {
          passengerSelect = document.createElement('select');
          passengerSelect.id = 'passengerSelect';
          passengerSelect.style.marginBottom = '10px';
          passengerCards.forEach((card, idx) => {
              const option = document.createElement('option');
              option.value = idx;
              option.textContent = card.querySelector('input[type="text"]')?.value || `Hành khách ${idx+1}`;
              passengerSelect.appendChild(option);
          });
          seatMap.parentNode.insertBefore(passengerSelect, seatMap);
      }

      const seatMapTitle = document.getElementById('seatMapTitle');
      if (seatMapTitle) seatMapTitle.textContent = `Chọn ghế cho hành khách`;

      const planeHead = document.createElement('div');
      planeHead.className = 'plane-section plane-head';
      planeHead.innerHTML = '<h5>ĐẦU MÁY BAY</h5>';
      seatMap.appendChild(planeHead);

      const topFacilities = document.createElement('div');
      topFacilities.className = 'facility-row';
      topFacilities.innerHTML = `<div class="facility wc-room">🚽 WC</div><div class="facility galley-room">🍽️ Galley</div>`;
      seatMap.appendChild(topFacilities);

      rowLabels.forEach(rowLabel => {
          if (exitRows.includes(rowLabel)) {
              const exit = document.createElement('div');
              exit.className = 'exit-row';
              exit.innerHTML = '🚪 CỬA LỐI THOÁT HIỂM 🚪';
              seatMap.appendChild(exit);
          }

          const rowDiv = document.createElement('div');
          rowDiv.className = 'seat-map';

          for (let col=1; col<=cols; col++) {
              if (col===6) {
                  const aisle = document.createElement('div');
                  aisle.className = 'aisle';
                  rowDiv.appendChild(aisle);
              }

              const seatCode = rowLabel + col;
              const seat = document.createElement('div');
              seat.className = 'seat';
              seat.textContent = seatCode;

              const isPremium = premiumRows.includes(rowLabel);
              seat.classList.add(isPremium?'premium':'economy');

              if (seatsOccupiedServer.includes(seatCode)) {
                  seat.classList.add('occupied');
                  seat.innerHTML = `
                      <img src="img/logoflynow.png" class="seat-icon" title="Đã có người">
                  `;
                  seat.style.pointerEvents = "none";
                  rowDiv.appendChild(seat);
                  continue;
              }

              if (bookingData.selectedSeats.includes(seatCode)) {
                  seat.classList.add('selected');
              }

              seat.addEventListener('click', () => {
                  const selectedSeats = bookingData.selectedSeats || [];
                  const passengerIdx = parseInt(passengerSelect.value);

                  const ticketClass = getSelectedTicketClass();
                  const isPremiumSeat = seat.classList.contains('premium');
                  const isEconomySeat = seat.classList.contains('economy');

                  if ((ticketClass === 'premium' && !isPremiumSeat) || (ticketClass === 'economy' && !isEconomySeat)) {
                      Swal.fire('Ghế không hợp lệ', 'Vui lòng chọn ghế đúng hạng vé đã chọn.', 'warning');
                      return;
                  }

                  if (selectedSeats[passengerIdx] === seatCode) {
                      selectedSeats[passengerIdx] = null;
                  } else if (!selectedSeats.includes(seatCode)) {
                      selectedSeats[passengerIdx] = seatCode;
                  } else {
                      Swal.fire('Ghế đã có người chọn', 'Vui lòng chọn ghế khác.', 'warning');
                      return;
                  }

                  bookingData.selectedSeats = selectedSeats;
                  localStorage.setItem(bookingKey, JSON.stringify(bookingData));

                  document.querySelectorAll('.seat').forEach(s => {
                      if (selectedSeats.includes(s.textContent)) s.classList.add('selected');
                      else s.classList.remove('selected');
                  });

                  updateSelectedSeatsInfo();
              });

              rowDiv.appendChild(seat);
          }
          seatMap.appendChild(rowDiv);
      });

      const bottomFacilities = document.createElement('div');
      bottomFacilities.className = 'facility-row';
      bottomFacilities.innerHTML = `<div class="facility wc-room">🚽 WC</div><div class="facility galley-room">🍽️ Galley</div>`;
      seatMap.appendChild(bottomFacilities);

      const planeTail = document.createElement('div');
      planeTail.className = 'plane-section plane-tail';
      planeTail.innerHTML = '<h5>ĐUÔI MÁY BAY</h5>';
      seatMap.appendChild(planeTail);
  }

  function updateSelectedSeatsInfo() {
      const userId = window.checkoutData?.user_id || 0;
      const bookingKey = 'booking_data_' + userId;

      const bookingData = JSON.parse(localStorage.getItem(bookingKey) || '{}');
      const passengerCards = document.querySelectorAll('.passenger-card');
      const seatsInfoEl = document.getElementById('selectedSeatsInfo');
      const chooseSeatBtn = document.getElementById('chooseSeatBtn');
      if (!seatsInfoEl || !chooseSeatBtn) return;

      seatsInfoEl.innerHTML = '';

      if (Array.isArray(bookingData.selectedSeats) && bookingData.selectedSeats.some(s => s)) {
          let html = '';
          passengerCards.forEach((card, idx) => {
              const name = card.querySelector('input[type="text"]')?.value || 'Hành khách';
              const seat = bookingData.selectedSeats[idx] || '-';
              html += `<div>${name}: <strong>${seat}</strong></div>`;
          });
          seatsInfoEl.innerHTML = html;

          chooseSeatBtn.style.display = 'none';

          let changeBtn = document.getElementById('changeSeatBtn');
          if (!changeBtn) {
              changeBtn = document.createElement('button');
              changeBtn.id = 'changeSeatBtn';
              changeBtn.textContent = 'Thay đổi';
              changeBtn.style.cssText = `
                  margin-left:8px;
                  padding:2px 6px;
                  font-size:0.85rem;
                  vertical-align:middle;
                  cursor:pointer;
                  border:1px solid #0069d9;
                  background:#fff;
                  color:#0069d9;
                  border-radius:4px;
              `;
              seatsInfoEl.appendChild(changeBtn);

              changeBtn.addEventListener('click', () => {
                  currentPassengerIndex = 0;
                  renderSeatMap();
                  const modalEl = document.getElementById('seatSelectionModal');
                  const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                  modalInstance.show();
              });
          }
      } else {
          chooseSeatBtn.style.display = '';
      }
  }

  document.getElementById('confirmSeatBtn').addEventListener('click', () => {
      const userId = window.checkoutData?.user_id || 0;
      const bookingKey = 'booking_data_' + userId;

      const passengerCards = document.querySelectorAll('.passenger-card');
      const bookingData = JSON.parse(localStorage.getItem(bookingKey) || '{}');
      if (!Array.isArray(bookingData.selectedSeats)) bookingData.selectedSeats = [];

      if (bookingData.selectedSeats.length < passengerCards.length || bookingData.selectedSeats.includes(null)) {
          Swal.fire('Chưa chọn đủ ghế', `Vui lòng chọn đủ ghế cho tất cả hành khách.`, 'warning');
          return;
      }

      updateSelectedSeatsInfo();

      const modalEl = document.getElementById('seatSelectionModal');
      const modalInstance = bootstrap.Modal.getInstance(modalEl);
      if (modalInstance) modalInstance.hide();
  });

  document.addEventListener('DOMContentLoaded', () => {
      renderSelectedSeatsInfo();
      document.getElementById('chooseSeatBtn')?.addEventListener('click', chooseSeatBtnHandler);
  });


  function renderSelectedSeatsInfo() {
      const userId = window.checkoutData?.user_id || 0;
      const bookingKey = 'booking_data_' + userId;

      const bookingData = JSON.parse(localStorage.getItem(bookingKey) || '{}');
      const seatsInfoEl = document.getElementById('selectedSeatsInfo');
      const chooseSeatBtn = document.getElementById('chooseSeatBtn');
      if (!seatsInfoEl || !chooseSeatBtn) return;

      seatsInfoEl.innerHTML = '';

      if (Array.isArray(bookingData.selectedSeats) && bookingData.selectedSeats.some(s => s)) {
          const passengerCards = document.querySelectorAll('.passenger-card');
          let html = '';
          passengerCards.forEach((card, idx) => {
              const name = card.querySelector('input[type="text"]')?.value || 'Hành khách';
              const seat = bookingData.selectedSeats[idx] || '-';
              html += `<div>${name}: <strong>${seat}</strong></div>`;
          });
          seatsInfoEl.innerHTML = html;

          chooseSeatBtn.style.display = 'none';

          let changeBtn = document.getElementById('changeSeatBtn');
          if (!changeBtn) {
              changeBtn = document.createElement('button');
              changeBtn.id = 'changeSeatBtn';
              changeBtn.textContent = 'Thay đổi';
              changeBtn.style.cssText = `
                  margin-left:8px;
                  padding:2px 6px;
                  font-size:0.85rem;
                  vertical-align:middle;
                  cursor:pointer;
                  border:1px solid #0069d9;
                  background:#fff;
                  color:#0069d9;
                  border-radius:4px;
              `;
              seatsInfoEl.appendChild(changeBtn);

              changeBtn.addEventListener('click', () => {
                  currentPassengerIndex = 0;
                  renderSeatMap();
                  const modalEl = document.getElementById('seatSelectionModal');
                  const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                  modalInstance.show();
              });
          }
      } else {
          chooseSeatBtn.style.display = '';
      }
  }


  document.getElementById('payBtn').addEventListener('click', () => {
      if (!window.checkoutData || !window.checkoutData.flight_id) {
          Swal.fire({
              icon: 'error',
              title: 'Lỗi dữ liệu',
              text: 'Không tìm thấy thông tin chuyến bay. Vui lòng chọn lại!',
              confirmButtonText: 'Quay lại trang đặt vé'
          }).then(() => {
              window.location.href = 'cheap-tickets.php';
          });
          return;
      }

      const userId = window.checkoutData?.user_id || 0;
      const bookingKey = 'booking_data_' + userId;
      const flightKey = 'selected_flight_' + userId;

      const bookingData = JSON.parse(localStorage.getItem(bookingKey) || '{}');

      bookingData.flight_id = window.checkoutData.flight_id;
      bookingData.user_id = window.checkoutData.user_id;

      if (!validateBookingForm()) return;

      if (!Array.isArray(bookingData.selectedSeats) || bookingData.selectedSeats.length === 0) {
          Swal.fire('Chưa chọn ghế', 'Vui lòng chọn ghế trước khi thanh toán.', 'warning');
          return;
      }

      const randomSuffix = Math.floor(1000 + Math.random() * 9000);
      const datePart = new Date().toISOString().slice(0,10).replace(/-/g, '');
      bookingData.booking_code = `FN-${datePart}-${randomSuffix}`;

      bookingData.status = 'Đã thanh toán';

      let baggageExtra = 0;
      if (bookingData.passengers && Array.isArray(bookingData.passengers)) {
          bookingData.passengers.forEach(p => {
              baggageExtra += Number(p.extraFee || 0);
          });
      }
      bookingData.baggage_extra = baggageExtra;

      const baseTotal = calculateTicketPrice(bookingData);
      bookingData.total_price = baseTotal;

      console.log("=== Dữ liệu JSON gửi lên checkout.php ===");
      console.log(bookingData);

      fetch('checkout.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
          },
          body: JSON.stringify(bookingData)
      })
      .then(res => res.json())
      .then(data => {
          console.log('Kết quả thanh toán:', data);

          if (data.success) {
              localStorage.removeItem(bookingKey);
              localStorage.removeItem('selectedSeats');
              localStorage.removeItem(flightKey);
              preventAutoSave = true;

              const firstAdultCard = document.querySelector('.passenger-card[data-type="adult"]');
              if(firstAdultCard){
                  const baggageInput = firstAdultCard.querySelector('.baggage-input');
                  if(baggageInput){
                      baggageInput.value = window.checkoutData.baggageLimit;
                      const warn = firstAdultCard.querySelector('.baggage-warning');
                      if(warn){
                          warn.textContent = '';
                          warn.dataset.extrafeefor = 0;
                      }
                  }
              }

              updateTotal();

              const bookingCode = bookingData.booking_code;
              const qrURL = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${bookingCode}`;

              Swal.fire({
                  width: 800,
                  showConfirmButton: true,
                  confirmButtonText: 'Xem vé của tôi',
                  confirmButtonColor: '#007BFF',
                  background: '#f4f8ff',
                  html: `
                      <div style="display:flex; flex-direction:row; align-items:stretch; border-radius:20px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.15); font-family:'Poppins',sans-serif;">
                          <div style="flex:2; background:linear-gradient(135deg,#007bff,#00b4d8); color:white; padding:40px 30px; display:flex; flex-direction:column; justify-content:center;">
                              <h2 style="font-size:28px; font-weight:700; margin-bottom:10px;">Chúc mừng bạn!</h2>
                              <p style="font-size:18px; margin-bottom:20px;">Bạn đã <strong>đặt vé thành công</strong>.</p>
                              <p style="font-size:16px;">Hãy vào mục <strong>“Vé của bạn”</strong> để xem chi tiết chuyến bay.</p>
                              <div style="margin-top:30px; font-size:14px; opacity:0.9;">
                                  <em>Vui lòng đến quầy check-in trước khi bay ít nhất 90 phút để làm thủ tục.</em>
                              </div>
                          </div>
                          <div style="flex:1; background:white; padding:30px; text-align:center; display:flex; flex-direction:column; justify-content:center;">
                              <img src="${qrURL}" alt="QR Code" style="width:150px; height:150px; margin:0 auto 15px; border-radius:10px;">
                              <div style="font-size:16px; font-weight:600; color:#333;">Mã đặt chỗ</div>
                              <div style="font-size:22px; font-weight:700; color:#007bff; margin-top:5px;">${bookingCode}</div>
                              <div style="margin-top:15px; font-size:13px; color:#666;">Giữ mã này để tra cứu và làm thủ tục bay.</div>
                          </div>
                      </div>
                  `,
              }).then(() => {
                  updateTotal();
                  resetBookingForm();
                  window.location.href = 'my-tickets.php';
              });

          } else {
              Swal.fire('Lỗi', data.message || 'Thanh toán thất bại', 'error');
          }
      })
      .catch(err => {
          console.error('Lỗi khi gửi dữ liệu checkout:', err);
          Swal.fire('Lỗi hệ thống', 'Không thể gửi yêu cầu thanh toán', 'error');
      });
  });

  function calculateTicketPrice(data) {
    let total = 0;
    const ticketType = data.ticketType || 'normal';
    const adult = +data.adult || 0;
    const child = +data.child || 0;
    const baby = +data.baby || 0;

    const priceNormal = window.checkoutData.priceNormal;
    const pricePremium = Math.round(priceNormal * 1.5);

    const price = (ticketType === 'premium') ? pricePremium : priceNormal;
    const adultPrice = adult * price;
    const childPrice = child * (price * 0.75);
    const babyPrice = baby * (price * 0.5);

    total = adultPrice + childPrice + babyPrice;
    return total;
  }

  function resetBookingForm() {
      preventAutoSave = true;

      const userId = window.checkoutData?.user_id || 0;
      localStorage.removeItem('booking_data_' + userId);
      localStorage.removeItem('selected_flight_' + userId);
      localStorage.removeItem('selectedSeats'); // Legacy

      document.getElementById('contactName').value = '';
      document.getElementById('contactPhone').value = '';
      document.getElementById('contactEmail').value = '';
      document.getElementById('promoCode').value = '';
      document.getElementById('adultCount').value = 1;
      document.getElementById('childCount').value = 0;
      document.getElementById('babyCount').value = 0;
      document.getElementById('ticketNormal').checked = true;
      document.getElementById('ticketPremium').checked = false;

      renderPassengerInputs();

      const firstAdultCard = document.querySelector('.passenger-card[data-type="adult"] input[type="date"]');
      if (firstAdultCard) firstAdultCard.value = '';

      const firstAdultCCCD = document.querySelector('.passenger-card[data-type="adult"] input[placeholder="123456789"]');
      if (firstAdultCCCD) firstAdultCCCD.value = '';

      const dot = document.getElementById('checkoutDot');
      if (dot) dot.style.display = 'none';
  }

