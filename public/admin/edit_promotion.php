<?php 
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

include 'layout/header.php';
include __DIR__ . '/../../db_connect.php';

$errors = [];
$success = "";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Thiếu ID khuyến mãi hợp lệ.");
}
$promo_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM promotions WHERE id=?");
$stmt->bind_param("i", $promo_id);
$stmt->execute();
$result = $stmt->get_result();
$promo = $result->fetch_assoc();

if (!$promo) {
    die("Không tìm thấy khuyến mãi.");
}

$airlines = $conn->query("SELECT id, name, logo_url FROM airlines")->fetch_all(MYSQLI_ASSOC);
$airports = $conn->query("SELECT code, name_vn, city_vn FROM airports ORDER BY city_vn ASC")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $airline_id = $_POST['airline_id'];
    $title = trim($_POST['title']);
    $code = strtoupper(trim($_POST['code']));
    $description = trim($_POST['description']);
    $discount_type = $_POST['discount_type'];
    $discount_value = $_POST['discount_value'];
    $min_tickets = $_POST['min_tickets'] ?: 1;
    $route_from = trim($_POST['route_from']);
    $route_to = trim($_POST['route_to']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $image_url = trim($_POST['image_url']);

    if (!$airline_id) $errors[] = "Vui lòng chọn hãng hàng không.";
    if (!$title) $errors[] = "Tiêu đề không được để trống.";
    if (!$code) $errors[] = "Mã khuyến mãi không được để trống.";
    if (!$discount_value || $discount_value <= 0) $errors[] = "Giá trị giảm phải lớn hơn 0.";
    if (!$route_from) $errors[] = "Vui lòng chọn sân bay đi.";
    if (!$route_to) $errors[] = "Vui lòng chọn sân bay đến.";
    if ($route_from !== 'ALL' && $route_to !== 'ALL' && $route_from === $route_to) {
        $errors[] = "Sân bay đi và đến không được giống nhau.";
    }
    if (!$start_date) $errors[] = "Vui lòng chọn ngày bắt đầu.";
    if (!$end_date) $errors[] = "Vui lòng chọn ngày kết thúc.";

    $today = date('Y-m-d');

    if ($start_date < $today) {
        $errors[] = "Ngày bắt đầu không được là ngày cũ hơn hôm nay.";
    }

    if ($end_date < $start_date) {
        $errors[] = "Ngày kết thúc không được nhỏ hơn ngày bắt đầu.";
    }

    if ($route_from !== 'ALL' && $route_to !== 'ALL' && $route_from === $route_to) {
        $errors[] = "Sân bay đi và đến không được giống nhau.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE promotions 
            SET airline_id=?, title=?, code=?, description=?, discount_type=?, discount_value=?, min_tickets=?, 
                route_from=?, route_to=?, start_date=?, end_date=?, image_url=?
            WHERE id=?");
        $stmt->bind_param(
            "issssdisssssi", 
            $airline_id, $title, $code, $description, $discount_type, $discount_value,
            $min_tickets, $route_from, $route_to, $start_date, $end_date, $image_url, $promo_id
        );

        if ($stmt->execute()) {
            $success = "Cập nhật khuyến mãi thành công!";
            $stmt2 = $conn->prepare("SELECT * FROM promotions WHERE id=?");
            $stmt2->bind_param("i", $promo_id);
            $stmt2->execute();
            $promo = $stmt2->get_result()->fetch_assoc();
        } else {
            $errors[] = "Lỗi khi cập nhật: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chỉnh sửa khuyến mãi</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body { font-family:'Poppins', sans-serif; background:#f0f4f8; }
.card { border-radius:20px; background:linear-gradient(145deg,#e0f7ff,#ffffff); }
label { font-weight:600; margin-top:10px; display:block; }
input, select, textarea { border-radius:10px; }
#promoImagePreview { max-width:70px; max-height:70px; display:block; margin-top:10px; }
</style>
</head>
<body>

<div class="container my-5">
    <div class="card shadow-lg p-4 border-0">
        <h2 class="mb-4 text-primary fw-bold">Chỉnh sửa Khuyến Mãi</h2>

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Hãng hàng không</label>
                    <select name="airline_id" id="airlineSelect" class="form-select" required>
                        <option value="">-- Chọn hãng --</option>
                        <?php foreach($airlines as $a): ?>
                            <option value="<?= $a['id'] ?>" data-logo="../<?= $a['logo_url'] ?>" 
                                <?= $promo['airline_id'] == $a['id'] ? 'selected' : '' ?>>
                                <?= $a['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <img id="promoImagePreview" src="<?= htmlspecialchars($promo['image_url']) ?>" alt="Logo">

                    <input type="hidden" name="image_url" id="imageUrl" value="<?= htmlspecialchars($promo['image_url']) ?>">

                    <label>Tiêu đề khuyến mãi</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($promo['title']) ?>" required>

                    <label>Mã khuyến mãi</label>
                    <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($promo['code']) ?>" readonly>

                    <label>Mô tả</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($promo['description']) ?></textarea>

                    <label>Loại giảm</label>
                    <select name="discount_type" class="form-select">
                        <option value="fixed" <?= $promo['discount_type']=='fixed'?'selected':'' ?>>Giảm cố định (VNĐ)</option>
                        <option value="percent" <?= $promo['discount_type']=='percent'?'selected':'' ?>>Giảm theo %</option>
                    </select>

                    <label>Giá trị giảm</label>
                    <input type="number" step="0.01" name="discount_value" class="form-control" value="<?= $promo['discount_value'] ?>" required>

                    <label>Số vé tối thiểu</label>
                    <input type="number" name="min_tickets" class="form-control" value="<?= $promo['min_tickets'] ?>">
                </div>

                <div class="col-md-6">
                    <label>Tuyến bay áp dụng</label>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label>Sân bay đi</label>
                            <select name="route_from" class="form-select" required>
                                <option value="">-- Chọn sân bay đi --</option>
                                <option value="ALL" <?= ($promo['route_from'] == 'ALL') ? 'selected' : '' ?>>Tất cả</option>

                                <?php foreach($airports as $a): ?>
                                    <option value="<?= $a['code'] ?>" 
                                        <?= ($promo['route_from'] == $a['code']) ? 'selected' : '' ?>>
                                        <?= $a['code'] ?> - <?= $a['name_vn'] ?> (<?= $a['city_vn'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col">
                            <label>Sân bay đến</label>
                            <select name="route_to" class="form-select" required>
                                <option value="">-- Chọn sân bay đến --</option>
                                <option value="ALL" <?= ($promo['route_to'] == 'ALL') ? 'selected' : '' ?>>Tất cả</option>

                                <?php foreach($airports as $a): ?>
                                    <option value="<?= $a['code'] ?>" 
                                        <?= ($promo['route_to'] == $a['code']) ? 'selected' : '' ?>>
                                        <?= $a['code'] ?> - <?= $a['name_vn'] ?> (<?= $a['city_vn'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <label>Ngày bắt đầu</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $promo['start_date'] ?>" required>

                    <label>Ngày kết thúc</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $promo['end_date'] ?>" required>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg w-100">Cập nhật Khuyến Mãi</button>
                        <a href="list_promotion.php" class="btn btn-secondary mt-2 w-100">Quay lại danh sách</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const airlineSelect = document.getElementById('airlineSelect');
const imageUrlInput = document.getElementById('imageUrl');
const promoImagePreview = document.getElementById('promoImagePreview');
const promoForm = document.getElementById('promoForm') || document.querySelector('form'); 

airlineSelect.addEventListener('change', function() {
    const selectedOption = airlineSelect.selectedOptions[0];
    const logo = selectedOption.dataset.logo;

    if (logo) {
        imageUrlInput.value = logo;
        promoImagePreview.src = logo;
        promoImagePreview.style.display = 'block';
    } else {
        imageUrlInput.value = '';
        promoImagePreview.style.display = 'none';
    }
});

promoForm.addEventListener('submit', function(e){
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    const routeFrom = document.querySelector('select[name="route_from"]').value;
    const routeTo = document.querySelector('select[name="route_to"]').value;
    const today = new Date().toISOString().split('T')[0];

    let errorMsg = '';

    if(startDate < today){
        errorMsg = 'Ngày bắt đầu không được là ngày cũ hơn hôm nay.';
    } else if(endDate < startDate){
        errorMsg = 'Ngày kết thúc không được nhỏ hơn ngày bắt đầu.';
    } else if(routeFrom !== 'ALL' && routeTo !== 'ALL' && routeFrom === routeTo){
        errorMsg = 'Sân bay đi và đến không được giống nhau.';
    }

    if(errorMsg){
        e.preventDefault();
        Swal.fire({ icon: 'error', title: 'Lỗi!', text: errorMsg });
    }
});

<?php if($success): ?>
Swal.fire({
    icon: 'success',
    title: '<?=$success?>',
    confirmButtonText: 'OK'
});
<?php endif; ?>
</script>


</body>
</html>
