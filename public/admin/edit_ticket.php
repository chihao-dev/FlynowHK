<?php
include __DIR__ . '/../../db_connect.php';
require __DIR__ . '/../../app/Http/Controllers/ListTicketController.php';
include 'layout/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<title>Sửa chuyến bay</title>
<style>
body { font-family:'Poppins', sans-serif; background:#f0f4f8; }
.card { border-radius:20px; background:linear-gradient(145deg,#e0f7ff,#ffffff); }
input, select { border-radius:10px; }
#airlineLogo { max-width:70px; max-height:70px; object-fit:contain; display:none; }
</style>

<div class="container my-5">
    <div class="card shadow-lg p-4 border-0">
        <h2 class="mb-4 text-primary fw-bold">Chỉnh sửa chuyến bay</h2>

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        <form method="post" id="flightForm">
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Hãng bay</label>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <select name="airline_id" id="airlineSelect" class="form-select" style="flex:1;">
                            <option value="">-- Chọn hãng --</option>
                            <?php foreach ($airlines as $a): ?>
                                <option value="<?= $a['id'] ?>" data-logo="<?= $a['logo_url'] ?>" data-code="<?= $a['code'] ?>"
                                    <?= $a['id'] == $flight['airline_id'] ? 'selected' : '' ?>>
                                    <?= $a['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div style="width:80px;height:80px;border:1px solid #ddd;border-radius:12px;background:#f9f9f9;display:flex;align-items:center;justify-content:center;">
                            <img id="airlineLogo" src="/<?= htmlspecialchars($flight['airline_id'] ? $airlines[array_search($flight['airline_id'], array_column($airlines, 'id'))]['logo_url'] : '') ?>" 
                                 style="max-width:70px;max-height:70px;<?= $flight['airline_id'] ? 'display:block' : 'display:none' ?>;">
                        </div>
                    </div>

                    <label>Số hiệu chuyến bay</label>
                    <input name="flight_number" class="form-control mb-3" value="<?= htmlspecialchars($flight['flight_number']) ?>">

                    <label>Sân bay đi</label>
                    <select name="departure_airport" class="form-select mb-3">
                        <option value="">-- Chọn --</option>
                        <?php foreach($airports as $ap): ?>
                            <option value="<?= $ap['code'] ?>" <?= $ap['code'] == $flight['departure_airport'] ? 'selected' : '' ?>>
                                <?= $ap['name_vn'] ?> (<?= $ap['code'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Sân bay đến</label>
                    <select name="arrival_airport" class="form-select mb-3">
                        <option value="">-- Chọn --</option>
                        <?php foreach($airports as $ap): ?>
                            <option value="<?= $ap['code'] ?>" <?= $ap['code'] == $flight['arrival_airport'] ? 'selected' : '' ?>>
                                <?= $ap['name_vn'] ?> (<?= $ap['code'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Giờ khởi hành</label>
                    <input type="datetime-local" name="departure_time" class="form-control mb-3" value="<?= date('Y-m-d\TH:i', strtotime($flight['departure_time'])) ?>">

                    <label>Giờ đến</label>
                    <input type="datetime-local" name="arrival_time" class="form-control mb-3" value="<?= date('Y-m-d\TH:i', strtotime($flight['arrival_time'])) ?>">
                </div>

                <div class="col-md-6">
                    <label>Loại hình</label>
                    <select name="flight_type" class="form-select mb-3">
                        <option <?= $flight['flight_type']=='Bay thẳng'?'selected':'' ?>>Bay thẳng</option>
                        <option <?= $flight['flight_type']=='1 điểm dừng'?'selected':'' ?>>1 điểm dừng</option>
                        <option <?= $flight['flight_type']=='2 điểm dừng'?'selected':'' ?>>2 điểm dừng</option>
                    </select>

                    <label>Thời lượng bay</label>
                    <input name="duration" id="duration" class="form-control mb-3" value="<?= htmlspecialchars($flight['duration']) ?>">

                    <label>Giá vé cơ bản (VNĐ)</label>
                    <input type="number" name="base_price" class="form-control mb-3" value="<?= htmlspecialchars($flight['base_price']) ?>">

                    <label>Khứ hồi / 1 chiều</label>
                    <select name="ticket_type" class="form-select mb-3">
                        <option value="1 chiều" <?= $flight['ticket_type']=='1 chiều'?'selected':'' ?>>1 chiều</option>
                        <option value="Khứ hồi" <?= $flight['ticket_type']=='Khứ hồi'?'selected':'' ?>>Khứ hồi</option>
                    </select>

                    <label>Giới hạn hành lý (kg/người)</label>
                    <input type="number" name="baggage_limit" class="form-control mb-3" value="<?= htmlspecialchars($flight['baggage_limit']) ?>">

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg w-100">Cập nhật chuyến bay</button>
                        <a href="list_ticket.php" class="btn btn-secondary mt-2 w-100">Quay lại danh sách</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const airlineSelect = document.getElementById('airlineSelect');
const airlineLogo = document.getElementById('airlineLogo');
airlineSelect.addEventListener('change', function(){
    const selected = this.options[this.selectedIndex];
    const logo = selected.getAttribute('data-logo');
    if(logo){
        airlineLogo.src = '/' + logo;
        airlineLogo.style.display = 'inline-block';
    }else{
        airlineLogo.style.display = 'none';
    }
});

<?php if($success): ?>
Swal.fire({
    icon:'success',
    title:'Cập nhật thành công!',
    timer:2000,
    showConfirmButton:false
});
<?php endif; ?>
</script>
