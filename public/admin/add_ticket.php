<?php
include __DIR__ . '/../../db_connect.php';
require __DIR__ . '/../../app/Http/Controllers/ListTicketController.php';
include 'layout/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<title>Flynow - Quản trị viên</title>
<style>
body { font-family:'Poppins', sans-serif; background: #f0f4f8; }
label { font-weight:600; margin-top:10px; display:block; }
.card { border-radius:20px; background: linear-gradient(145deg,#e0f7ff,#ffffff); }
input, select, textarea { border-radius:10px; }
#airlineLogo, #promoImagePreview { max-width:70px; max-height:70px; object-fit:contain; display:none; }
</style>

<div class="container my-5">
    <div class="card shadow-lg p-4 border-0" style="border-radius:20px; background: linear-gradient(145deg, #e0f7ff, #ffffff);">
        <h2 class="mb-4 text-primary fw-bold">Tạo chuyến bay mới</h2>

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger"><?=htmlspecialchars($e)?></div>
        <?php endforeach; ?>

        <form method="post" id="flightForm">
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Hãng bay</label>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <select name="airline_id" id="airlineSelect" class="form-select" style="flex:1;">
                            <option value="">-- Chọn hãng --</option>
                            <?php foreach($airlines as $a): ?>
                                <option value="<?= $a['id'] ?>" data-logo="<?= $a['logo_url'] ?>" data-code="<?= $a['code'] ?>"><?= $a['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div style="width:80px; height:80px; display:flex; align-items:center; justify-content:center; border-radius:12px; border:1px solid #ddd; background:#f9f9f9;">
                            <img id="airlineLogo" src="" style="max-width:70px; max-height:70px; display:none; object-fit:contain;">
                        </div>
                    </div>

                    <label>Số hiệu chuyến bay</label>
                    <input name="flight_number" id="flightNumber" class="form-control mb-3" readonly placeholder="Tự động theo hãng">

                    <label>Sân bay đi</label>
                    <select name="departure_airport" class="form-select mb-3">
                        <option value="">-- Chọn --</option>
                        <?php foreach($airports as $ap): ?>
                            <option value="<?= $ap['code'] ?>"><?= $ap['name_vn'] ?> (<?= $ap['code'] ?>)</option>
                        <?php endforeach; ?>
                    </select>

                    <label>Sân bay đến</label>
                    <select name="arrival_airport" class="form-select mb-3">
                        <option value="">-- Chọn --</option>
                        <?php foreach($airports as $ap): ?>
                            <option value="<?= $ap['code'] ?>"><?= $ap['name_vn'] ?> (<?= $ap['code'] ?>)</option>
                        <?php endforeach; ?>
                    </select>

                    <label>Giờ khởi hành</label>
                    <input type="datetime-local" name="departure_time" class="form-control mb-3">

                    <label>Giờ đến</label>
                    <input type="datetime-local" name="arrival_time" class="form-control mb-3">
                </div>

                <div class="col-md-6">
                    <label>Loại hình</label>
                    <select name="flight_type" class="form-select mb-3">
                        <option>Bay thẳng</option>
                        <option>1 điểm dừng</option>
                        <option>2 điểm dừng</option>
                    </select>

                    <label>Thời lượng bay</label>
                    <input name="duration" id="duration" class="form-control mb-3" readonly placeholder="Tự động tính">

                    <label>Giá vé cơ bản 1 chiều (VNĐ)</label>
                    <input type="number" name="base_price" id="basePrice" class="form-control mb-3">

                    <label>Khứ hồi / 1 chiều</label>
                    <select name="ticket_type" id="ticketType" class="form-select mb-3">
                        <option value="1 chiều">1 chiều</option>
                        <option value="Khứ hồi">Khứ hồi</option>
                    </select>

                    <label>Giới hạn hành lý (kg/người)</label>
                    <input type="number" name="baggage_limit" class="form-control mb-3" value="15">

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">Lưu chuyến bay</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const airlineSelect = document.getElementById('airlineSelect');
const airlineLogo = document.getElementById('airlineLogo');
const flightNumberInput = document.getElementById('flightNumber');
const departureTime = document.querySelector('input[name="departure_time"]');
const arrivalTime = document.querySelector('input[name="arrival_time"]');
const durationInput = document.getElementById('duration');
const basePriceInput = document.getElementById('basePrice');
const ticketTypeSelect = document.getElementById('ticketType');

airlineSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const logoUrl = selectedOption.getAttribute('data-logo');
    const code = selectedOption.getAttribute('data-code') || 'XX';
    if(logoUrl){
        airlineLogo.src = '/' + logoUrl;
        airlineLogo.style.display = 'inline-block';
    } else {
        airlineLogo.style.display = 'none';
    }
    flightNumberInput.value = code.toUpperCase() + Math.floor(Math.random()*900+100);
});

function calculateDuration() {
    if(departureTime.value && arrivalTime.value){
        const start = new Date(departureTime.value);
        const end = new Date(arrivalTime.value);
        if(end > start){
            let diff = (end - start)/1000/60;
            let hours = Math.floor(diff/60);
            let minutes = diff % 60;
            durationInput.value = hours + 'h ' + minutes + 'm';
        } else {
            durationInput.value = '';
        }
    }
}

document.getElementById('flightForm').addEventListener('submit', function(e){
    const departure = document.querySelector('select[name="departure_airport"]').value;
    const arrival = document.querySelector('select[name="arrival_airport"]').value;
    if(departure && arrival && departure === arrival){
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Lỗi!',
            text: 'Sân bay đi và sân bay đến không được giống nhau'
        });
    }
});


departureTime.addEventListener('change', calculateDuration);
arrivalTime.addEventListener('change', calculateDuration);

<?php if($success): ?>
Swal.fire({
    icon: 'success',
    title: 'Thành công!',
    text: 'Tạo chuyến bay thành công',
    timer: 2000,
    showConfirmButton: false
});
<?php endif; ?>
</script>
