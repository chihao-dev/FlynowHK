<?php
include __DIR__ . '/../../db_connect.php';
require __DIR__ . '/../../app/Http/Controllers/ListTicketController.php';
include 'layout/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Flynow - Quản trị viên</title>
<link rel="stylesheet" href="./css/list_ticket.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    .btn-add {
        background: linear-gradient(135deg, #00b4db, #0083b0);
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 12px;
        padding: 10px 18px;
        transition: 0.2s;
    }
    .btn-add:hover {
        opacity: 0.9;
    }

    .btn-edit {
        background-color: #ffc107;
        color: black;
        border: none;
    }
    .btn-delete {
        background-color: #dc3545;
        color: white;
        border: none;
    }
    .btn-edit:hover, .btn-delete:hover {
        opacity: 0.85;
    }
</style>
</head>

<body>
<div class="container py-5">
    <div class="admin-header">
        <h2>Danh sách chuyến bay</h2>
        <a href="add_ticket.php" class="btn btn-add">Tạo chuyến bay</a>
    </div>

    <div class="filter-bar sticky-top bg-white p-3 rounded shadow-sm mb-4" style="top: 10px; z-index: 100;">
        <form method="GET" class="row g-2 align-items-center">

            <div class="col-md-2">
                <input type="text" name="code" class="form-control" placeholder="Tìm mã hiệu..."
                    value="<?= $_GET['code'] ?? '' ?>">
            </div>

            <div class="col-md-2">
                <select name="airline" class="form-control">
                    <option value="">Hãng bay</option>
                    <?php 
                    $airlines = $conn->query("SELECT id, name FROM airlines")->fetch_all(MYSQLI_ASSOC);
                    foreach ($airlines as $a): ?>
                        <option value="<?= $a['id'] ?>"
                        <?= (isset($_GET['airline']) && $_GET['airline'] == $a['id']) ? 'selected' : '' ?>>
                            <?= $a['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <input type="text" name="from" class="form-control" placeholder="Sân bay đi (VD: HAN)"
                    value="<?= $_GET['from'] ?? '' ?>">
            </div>

            <div class="col-md-2">
                <input type="text" name="to" class="form-control" placeholder="Sân bay đến (VD: SGN)"
                    value="<?= $_GET['to'] ?? '' ?>">
            </div>

            <div class="col-md-2">
                <input type="number" name="price" class="form-control" placeholder="Giá sấp xỉ..."
                    value="<?= $_GET['price'] ?? '' ?>">
            </div>


            <div class="col-md-2">
                <button class="btn btn-primary w-100">Tìm kiếm</button>
            </div>

        </form>
    </div>


    <?php if (empty($flights)): ?>
        <div class="alert alert-info">Chưa có chuyến bay nào trong hệ thống.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Hãng bay</th>
                    <th>Số hiệu</th>
                    <th>Sân bay đi</th>
                    <th>Sân bay đến</th>
                    <th>Giờ khởi hành</th>
                    <th>Giờ đến</th>
                    <th>Giá vé (VNĐ)</th>
                    <th>Hành lý (kg)</th>
                    <th>Loại</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($flights as $f): ?>
                    <?php
                        date_default_timezone_set('Asia/Ho_Chi_Minh');
                        $now = new DateTime('now');

                        $dep = new DateTime($f['departure_time']);
                        $arr = new DateTime($f['arrival_time']);

                        $statusText = "Chưa khởi hành";
                        $statusClass = "status-upcoming";

                        if ($now >= $arr) {
                            $statusText = "Đã kết thúc";
                            $statusClass = "status-ended";
                        } elseif ($now >= $dep) {
                            $statusText = "Đã khởi hành";
                            $statusClass = "status-departed";
                        }

                        $bookingCount = $conn->query("SELECT COUNT(*) AS cnt FROM bookings WHERE flight_id = ".$f['id'])->fetch_assoc()['cnt'];
                    ?>
                    <tr>
                        <td><img src="/<?= htmlspecialchars($f['logo_url']) ?>" class="airline-logo"></td>
                        <td><?= htmlspecialchars($f['airline_name']) ?></td>
                        <td><strong><?= htmlspecialchars($f['flight_number']) ?></strong></td>
                        <td><?= htmlspecialchars($f['dep_name']) ?> (<?= htmlspecialchars($f['departure_airport']) ?>)</td>
                        <td><?= htmlspecialchars($f['arr_name']) ?> (<?= htmlspecialchars($f['arrival_airport']) ?>)</td>
                        <td><?= date('d/m/Y H:i', strtotime($f['departure_time'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($f['arrival_time'])) ?></td>
                        <td><?= number_format($f['base_price'], 0, ',', '.') ?></td>
                        <td><?= $f['baggage_limit'] ?></td>
                        <td><?= htmlspecialchars($f['ticket_type']) ?></td>
                        <td>
                            <span class="flight-status <?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
                        <td>
                            <?php if ($statusText === 'Chưa khởi hành'): ?>
                                <?php if ($bookingCount > 0): ?>
                                    <span class="text-danger" title="Chuyến bay đã có vé đặt">Đã có vé đặt</span>

                                <?php else: ?>
                                    <a href="edit_ticket.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-edit">Sửa</a>
                                    <button class="btn btn-sm btn-delete" onclick="confirmDelete(<?= $f['id'] ?>)">Xóa</button>
                                <?php endif; ?>
                            <?php elseif ($statusText === 'Đã khởi hành'): ?>
                                <span class="text-muted">Không thao tác</span>
                            <?php elseif ($statusText === 'Đã kết thúc'): ?>
                                <span class="text-muted">Không thao tác</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: "Bạn có chắc muốn xóa chuyến bay này?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete_ticket.php?id=' + id;
        }
    });
}
</script>
</body>
</html>
