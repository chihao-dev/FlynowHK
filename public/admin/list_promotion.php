<?php
include __DIR__ . '/../../db_connect.php';
require __DIR__ . '/../../app/Http/Controllers/ListPromotionController.php';
include 'layout/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Flynow - Quản lý khuyến mãi</title>
    <link rel="stylesheet" href="./css/list_promotion.css">
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
        <h2>Danh sách khuyến mãi</h2>
        <a href="add_promotion.php" class="btn btn-add">Thêm khuyến mãi</a>
    </div>

    <div class="filter-bar sticky-top bg-white p-3 rounded shadow-sm mb-4" style="top: 10px; z-index: 200;">
        <form method="GET" class="row g-2 align-items-center">

            <div class="col-md-2">
                <input type="text" name="code" class="form-control" placeholder="Mã KM..."
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
                <select name="discount" class="form-control">
                    <option value="">Loại giảm</option>
                    <option value="percent" <?= (@$_GET['discount']=="percent"?"selected":"") ?>>Phần trăm</option>
                    <option value="fixed" <?= (@$_GET['discount']=="fixed"?"selected":"") ?>>Giảm cố định</option>
                </select>
            </div>

            <div class="col-md-2">
                <input type="date" name="start" class="form-control"
                    value="<?= $_GET['start'] ?? '' ?>">
            </div>

            <div class="col-md-2">
                <input type="date" name="end" class="form-control"
                    value="<?= $_GET['end'] ?? '' ?>">
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">Lọc</button>
            </div>

        </form>
    </div>


    <?php if (empty($promos)): ?>
        <div class="alert alert-info">Chưa có khuyến mãi nào trong hệ thống.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Hãng</th>
                    <th>Tiêu đề</th>
                    <th>Mã KM</th>
                    <th>Loại giảm</th>
                    <th>Giá trị</th>
                    <th>Tuyến</th>
                    <th>Vé tối thiểu</th>
                    <th>Thời gian áp dụng</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promos as $p): ?>
                     <?php
                        date_default_timezone_set('Asia/Ho_Chi_Minh');
                        $now = strtotime(date('Y-m-d'));

                        $start = strtotime($p['start_date']);
                        $end   = strtotime($p['end_date']);

                        if ($now < $start) {
                            $statusText = "Chưa bắt đầu";
                            $statusClass = "badge bg-secondary";
                        } elseif ($now > $end) {
                            $statusText = "Hết hạn";
                            $statusClass = "badge bg-danger";
                        } else {
                            $statusText = "Còn hạn";
                            $statusClass = "badge bg-success";
                        }
                    ?>
                    <tr>
                        <td><img src="/<?= htmlspecialchars($p['logo_url']) ?>" class="airline-logo"></td>
                        <td><?= htmlspecialchars($p['airline_name']) ?></td>
                        <td><?= htmlspecialchars($p['title']) ?></td>
                        <td><strong><?= htmlspecialchars($p['code']) ?></strong></td>
                        <td><?= $p['discount_type'] === 'fixed' ? 'Giảm cố định' : 'Phần trăm' ?></td>
                        <td>
                        <?= $p['discount_type'] === 'fixed' 
                            ? number_format($p['discount_value'], 0, ',', '.') . ' ₫' 
                            : rtrim(rtrim(number_format($p['discount_value'], 2, '.', ''), '0'), '.') . '%' ?>
                        </td>

                        <td>
                            <?php if ($p['route_from'] === 'ALL' || $p['route_to'] === 'ALL'): ?>
                                Tất cả
                            <?php else: ?>
                                <?= htmlspecialchars($p['route_from']) ?> → <?= htmlspecialchars($p['route_to']) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= (int)$p['min_tickets'] ?></td>
                        <td>
                            <?= date('d/m/Y', strtotime($p['start_date'])) ?> - 
                            <?= date('d/m/Y', strtotime($p['end_date'])) ?>
                        </td>
                        <td><span class="<?= $statusClass ?>"><?= $statusText ?></span></td>
                        <td>
                            <a href="edit_promotion.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-edit">Sửa</a>
                            <button class="btn btn-sm btn-delete" onclick="confirmDelete(<?= $p['id'] ?>)">Xóa</button>
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
        text: "Bạn có chắc muốn xóa khuyến mãi này?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete_promotion.php?id=' + id;
        }
    });
}
</script>
</body>
</html>
