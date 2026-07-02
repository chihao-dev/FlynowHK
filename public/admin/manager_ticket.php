<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

include __DIR__ . '/../../db_connect.php';
include 'layout/header.php';

$sql = "SELECT b.*, f.flight_number, f.departure_airport, f.arrival_airport
        FROM bookings b
        JOIN flights f ON b.flight_id = f.id
        WHERE 1=1";

if(!empty($_GET['code'])){
    $code = $conn->real_escape_string($_GET['code']);
    $sql .= " AND b.booking_code LIKE '%$code%'";
}

if(!empty($_GET['flight'])){
    $flight = $conn->real_escape_string($_GET['flight']);
    $sql .= " AND f.flight_number LIKE '%$flight%'";
}


if(isset($_GET['ticket_type']) && $_GET['ticket_type'] !== ''){
    $type = $conn->real_escape_string($_GET['ticket_type']);
    $sql .= " AND b.ticket_type = '$type'";
}

if(!empty($_GET['price'])){
    $price = floatval($_GET['price']);
    $delta = $price * 0.1; 
    $sql .= " AND b.total_price BETWEEN ".($price - $delta)." AND ".($price + $delta);
}


if(!empty($_GET['date'])){
    $date = $conn->real_escape_string($_GET['date']);
    $sql .= " AND DATE(b.created_at) = '$date'";
}

$sql .= " ORDER BY b.created_at DESC";

$bookings = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Flynow - Quản lý đặt vé</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    .admin-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; }
    .admin-header h2 { font-weight:700; color:#0d6efd; }
    table { background:white; border-radius:15px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
    thead { background:#e9f3ff; color:#0056b3; font-weight:600; }
    td, th { text-align:center; vertical-align:middle; }
    .btn-view { background:#0d6efd; color:white; border:none; border-radius:8px; padding:5px 12px; }
    .btn-view:hover { opacity:0.9; }
    .booking-details { display:none; background:#f1f5fb; padding:10px; border-radius:10px; margin-top:5px; text-align:left; }
    .booking-details p { margin:0; font-size:14px; color:#333; }
    .filter-bar {
        border-radius:12px !important;
        border:1px solid #dce6f3;
    }

    .filter-bar input,
    .filter-bar select {
        border-radius:10px !important;
        padding:8px 10px;
    }

    .sticky-top {
        background:white;
    }
</style>
</head>
<body>
<div class="container py-5">
    <div class="admin-header">
        <h2>Quản lý đặt vé</h2>
    </div>

    <div class="filter-bar sticky-top bg-white p-3 rounded shadow-sm mb-4" style="top:10px; z-index:200;">
        <form method="GET" class="row g-2 align-items-center">

            <div class="col-md-2">
                <input type="text" name="code" class="form-control" placeholder="Mã booking..."
                    value="<?= $_GET['code'] ?? '' ?>">
            </div>

            <div class="col-md-2">
                <input type="text" name="flight" class="form-control" placeholder="Mã chuyến bay..."
                    value="<?= htmlspecialchars($_GET['flight'] ?? '') ?>">
            </div>

            <div class="col-md-2">
                <select name="ticket_type" class="form-control">
                    <option value="">Loại vé</option>
                    <option value="Thường" <?= (@$_GET['ticket_type']=="Thường"?"selected":"") ?>>Thường</option>
                    <option value="Cao cấp" <?= (@$_GET['ticket_type']=="Cao cấp"?"selected":"") ?>>Cao cấp</option>
                </select>
            </div>

            <div class="col-md-2">
                <input type="number" name="price" class="form-control" placeholder="Giá (VNĐ)"
                    value="<?= $_GET['price'] ?? '' ?>">
            </div>


            <div class="col-md-2">
                <input type="date" name="date" class="form-control"
                    value="<?= $_GET['date'] ?? '' ?>">
            </div>

            <div class="col-md-1">
                <button class="btn btn-primary w-100">Lọc</button>
            </div>

        </form>
    </div>


    <?php if (empty($bookings)): ?>
        <div class="alert alert-info">Chưa có booking nào trong hệ thống.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>Mã booking</th>
                    <th>Flight ID</th>
                    <th>User ID</th>
                    <th>Loại vé</th>
                    <th>Số lượng</th>
                    <th>Tổng giá (VNĐ)</th>
                    <th>Số ghế</th>
                    <th>Status</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($b['booking_code']) ?></strong></td>
                    <td><?= $b['flight_id'] ?> (<?= htmlspecialchars($b['flight_number']) ?>)</td>
                    <td><?= $b['user_id'] ?></td>
                    <td><?= htmlspecialchars($b['ticket_type']) ?></td>
                    <td><?= $b['people_count'] ?></td>
                    <td><?= number_format($b['total_price'],0,',','.') ?></td>
                    <td><?= htmlspecialchars($b['seat_numbers']) ?></td>
                    <td><?= htmlspecialchars($b['status']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($b['created_at'])) ?></td>
                    <td>
                        <button class="btn btn-sm btn-view" onclick="toggleDetails(<?= $b['id'] ?>)">Xem chi tiết</button>

                        <tr id="details-<?= $b['id'] ?>" class="booking-details-row" style="display:none;">
                            <td colspan="10" style="padding:0; border:none;">
                                <div class="booking-details-card p-3 mb-2" style="background:#f1f5fb; border-radius:12px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
                                    <div style="display:flex; gap:20px; flex-wrap:wrap;">
                                        <div style="flex:1; min-width:220px;">
                                            <h5 style="color:#0d6efd; margin-bottom:10px;">Thông tin liên hệ</h5>
                                            <p><strong>Họ tên:</strong> <?= htmlspecialchars($b['contact_name']) ?></p>
                                            <p><strong>Email:</strong> <?= htmlspecialchars($b['contact_email']) ?></p>
                                            <p><strong>Phone:</strong> <?= htmlspecialchars($b['contact_phone']) ?></p>
                                        </div>

                                        <div style="flex:2; min-width:300px;">
                                            <h5 style="color:#0d6efd; margin-bottom:10px;">Hành khách</h5>
                                            <table class="table table-sm table-bordered" style="background:white; border-radius:8px; overflow:hidden;">
                                                <thead style="background:#e9f3ff; color:#0056b3;">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Tên</th>
                                                        <th>Ngày sinh</th>
                                                        <th>CCCD</th>
                                                        <th>Hành lý (kg)</th>
                                                        <th>Số ghế</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $passengers = json_decode($b['passengers'], true);

                                                        $seats = [];
                                                        if (!empty($b['seat_numbers'])) {
                                                            $seats = array_map('trim', explode(',', $b['seat_numbers']));
                                                        }

                                                        if ($passengers && count($passengers) > 0) {
                                                            foreach ($passengers as $i => $p) {
                                                                echo '<tr>';
                                                                echo '<td>'.($i+1).'</td>';
                                                                echo '<td>'.htmlspecialchars($p['name'] ?? '-').'</td>';
                                                                echo '<td>'.htmlspecialchars($p['dob'] ?? '-').'</td>';
                                                                echo '<td>'.htmlspecialchars($p['doc'] ?? '-').'</td>';
                                                                echo '<td>'.htmlspecialchars($p['baggage'] ?? '-').'</td>';
                                                                echo '<td>'.htmlspecialchars($seats[$i] ?? '-').'</td>'; // map theo index hành khách
                                                                echo '</tr>';
                                                            }
                                                        } else {
                                                            echo '<tr><td colspan="6">Không có hành khách</td></tr>';
                                                        }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
function toggleDetails(id) {
    const row = document.getElementById('details-' + id);
    if(row.style.display === 'table-row') {
        row.style.display = 'none';
    } else {
        row.style.display = 'table-row';
    }
}
</script>
</body>
</html>
