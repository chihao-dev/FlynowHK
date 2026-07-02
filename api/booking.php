<?php
$flightDataJSON = $_GET['flight_data'] ?? '';
$fromCode = $_GET['from'] ?? '';
$toCode = $_GET['to'] ?? '';

$flight = null;
if (!empty($flightDataJSON)) {
    $decodedJSON = urldecode($flightDataJSON);
    $flight = json_decode($decodedJSON, true);
}

if (!$flight) {
    die("Lỗi: Không tìm thấy thông tin chuyến bay. Vui lòng quay lại trang tìm kiếm.");
}

$depTime = date('H:i', strtotime($flight['departure_time']));
$depDate = date('d/m/Y', strtotime($flight['departure_time']));
$arrTime = date('H:i', strtotime($flight['arrival_time']));
$price = number_format($flight['price'], 0, ',', '.'); 

?>
<?php include __DIR__.'/includes/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt vé chuyến bay <?= $flight['flight_number'] ?></title>
    <link rel="stylesheet" href="style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<script src="./js/script.js"></script>
<div class="container booking-page">
    <h2>Thông tin đặt vé</h2>

    <div class="flight-summary-box">
        <h3>Chuyến bay đã chọn</h3>
        <div class="summary-row">
            <span class="airline-name">
                <i class="fa-solid fa-plane-departure"></i> <?= $flight['airline_name'] ?> (<?= $flight['flight_number'] ?>)
            </span>
        </div>
        <div class="summary-row time-detail">
            <div>
                <strong><?= $depTime ?></strong>
                <span class="airport-code"><?= $fromCode ?></span>
                <p class="date-detail">Ngày: <?= $depDate ?></p>
            </div>
            
            <div class="duration-detail">
                <?= $flight['duration'] ?> / Bay thẳng
            </div>
            
            <div>
                <strong><?= $arrTime ?></strong>
                <span class="airport-code"><?= $toCode ?></span>
                <p class="date-detail">Giá: <?= $price ?> đ</p>
            </div>
        </div>
    </div>

    <div class="passenger-form-box">
        <h3>Nhập thông tin hành khách</h3>
        <form action="process_booking.php" method="POST">
            <input type="hidden" name="flight_data_json" value="<?= htmlspecialchars($flightDataJSON) ?>">
            
            <div class="input-group">
                <label for="name">Họ và Tên (Người lớn 1):</label>
                <input type="text" id="name" name="passenger_name_1" required>
            </div>
            
            <div class="input-group">
                <label for="email">Email liên hệ:</label>
                <input type="email" id="email" name="contact_email" required>
            </div>

            <button type="submit" class="btn-checkout">TIẾN HÀNH THANH TOÁN</button>
        </form>
    </div>
</div>

</body>
</html>
<?php include __DIR__.'/includes/footer.php'; ?>