<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

include __DIR__ . '/../../db_connect.php';
include 'layout/header.php';

$where = "WHERE u.role = 'user'";
if(!empty($_GET['name'])){
    $name = $conn->real_escape_string($_GET['name']);
    $where .= " AND u.fullname LIKE '%$name%'";
}
if(!empty($_GET['email'])){
    $email = $conn->real_escape_string($_GET['email']);
    $where .= " AND u.email LIKE '%$email%'";
}
if(!empty($_GET['date'])){
    $date = $conn->real_escape_string($_GET['date']);
    $where .= " AND DATE(u.created_at) = '$date'";
}

$sql = "SELECT u.*, ui.birthdate, ui.address, ui.phone, ui.avatar
        FROM users u
        LEFT JOIN user_info ui ON u.id = ui.user_id
        $where
        ORDER BY u.created_at DESC";

$users = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$active_limit = strtotime("-5 minutes");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý người dùng</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
.status-dot { width:12px; height:12px; border-radius:50%; display:inline-block; margin-right:6px; vertical-align:middle; background:#28a745; }
.inactive { background:#6c757d; }

.details-box { background:#eef3fb; padding:15px; border-radius:10px; margin-top:6px; overflow:hidden; max-height:0; transition:max-height 0.25s ease-out; }

.filter-bar {
    position: sticky;
    top: 10px;
    z-index: 200;
    background:white;
    padding:15px;
    border-radius:12px;
    margin-bottom:15px;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}
.filter-bar input {
    border-radius:8px;
    padding:8px 10px;
}
</style>
</head>

<body>
<div class="container py-5">
    <h2 class="mb-4" style="color:#0d6efd; font-weight:700;">Quản lý người dùng</h2>

    <div class="filter-bar row g-2 align-items-center">
        <form method="GET" class="row g-2 w-100">
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="Tìm theo tên..." value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="text" name="email" class="form-control" placeholder="Tìm theo email..." value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Lọc</button>
            </div>
        </form>
    </div>

    <table class="table table-bordered table-hover align-middle bg-white">
        <thead style="background:#e9f3ff; color:#0056b3; font-weight:600;">
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Ngày tạo</th>
                <th>Hoạt động</th>
                <th>Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($users)): ?>
                <tr><td colspan="6" class="text-center">Chưa có người dùng nào.</td></tr>
            <?php endif; ?>

            <?php foreach ($users as $i => $u): ?>
                <?php $active = strtotime($u['last_active']) >= $active_limit; ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($u['fullname']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
                    <td>
                        <span class="status-dot <?= $active ? 'active' : 'inactive' ?>"></span>
                        <?= $active ? 'Đang hoạt động' : 'Không hoạt động' ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="toggleDetails(<?= $u['id'] ?>)">Xem thông tin</button>
                    </td>
                </tr>

                <tr id="details-row-<?= $u['id'] ?>" style="display:none;">
                    <td colspan="6">
                        <div id="details-box-<?= $u['id'] ?>" class="details-box">
                            <h6 style="color:#0d6efd;">Thông tin tài khoản</h6>
                            <p><strong>Họ tên:</strong> <?= htmlspecialchars($u['fullname']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($u['email']) ?></p>
                            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($u['phone'] ?? '-') ?></p>
                            <p><strong>Ngày sinh:</strong> <?= htmlspecialchars($u['birthdate'] ?? '-') ?></p>
                            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($u['address'] ?? '-') ?></p>
                            <?php if (!empty($u['avatar'])): ?>
                                <p><strong>Avatar:</strong></p>
                                <img src="../<?= htmlspecialchars($u['avatar']) ?>" style="width:120px; border-radius:8px; object-fit:cover;">
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function toggleDetails(id) {
    document.querySelectorAll("tr[id^='details-row-']").forEach(row => {
        if(row.id !== "details-row-" + id){
            row.style.display = "none";
            let box = row.querySelector(".details-box");
            if(box) box.style.maxHeight = "0px";
        }
    });

    let row = document.getElementById("details-row-" + id);
    let box = document.getElementById("details-box-" + id);

    if(row.style.display === "table-row"){
        box.style.maxHeight = "0px";
        setTimeout(()=>row.style.display="none",250);
    } else {
        row.style.display="table-row";
        setTimeout(()=>box.style.maxHeight=box.scrollHeight+"px",10);
    }
}
</script>
</body>
</html>
