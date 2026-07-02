<?php
include __DIR__ . '/../../db_connect.php';
require __DIR__ . '/../../app/Http/Controllers/ListPromotionController.php';
include 'layout/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Flynow - Quản trị viên</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body { font-family:'Poppins', sans-serif; background: #f0f4f8; }
label { font-weight:600; margin-top:10px; display:block; }
.card { border-radius:20px; background: linear-gradient(145deg,#e0f7ff,#ffffff); }
input, select, textarea { border-radius:10px; }
#airlineLogo, #promoImagePreview { max-width:70px; max-height:70px; object-fit:contain; display:none; }
</style>
</head>
<body>

<div class="container my-5">
    <div class="card shadow-lg p-4 border-0">
        <h2 class="mb-4 text-primary fw-bold">Tạo Khuyến Mãi Mới</h2>

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger"><?=htmlspecialchars($e)?></div>
        <?php endforeach; ?>

        <form method="POST" id="promoForm">
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Hãng hàng không</label>
                    <div class="d-flex gap-3 mb-3">
                        <select name="airline_id" id="airlineSelect" class="form-select" style="flex:2;" required>
                            <option value="">-- Chọn hãng --</option>
                            <?php foreach($airlines as $a): ?>
                                <option 
                                    value="<?= $a['id'] ?>"
                                    data-logo="../<?= $a['logo_url'] ?>"
                                    <?= (isset($_POST['airline_id']) && $_POST['airline_id'] == $a['id']) ? 'selected' : '' ?>
                                >
                                    <?= $a['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <div class="mt-2 text-center">
                            <img id="promoImagePreview"
                                src="<?= isset($_POST['image_url']) ? $_POST['image_url'] : '' ?>"
                                style="<?= isset($_POST['image_url']) ? 'display:block;' : 'display:none;' ?>"
                            >
                        </div>
                        <input type="hidden" name="image_url" id="imageUrl" 
                            value="<?= htmlspecialchars($_POST['image_url'] ?? '') ?>">
                    </div>

                    <label>Tiêu đề khuyến mãi</label>
                    <input type="text" name="title" class="form-control"
                        value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>

                    <label>Mã khuyến mãi</label>
                    <input type="text" name="code" id="promoCode" class="form-control"
                        value="<?= htmlspecialchars($_POST['code'] ?? '') ?>" readonly required>

                    <label>Mô tả</label>
                    <textarea name="description" class="form-control"
                            rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

                    <label>Loại giảm</label>
                    <select name="discount_type" class="form-select">
                        <option value="fixed" <?= (($_POST['discount_type'] ?? '') == 'fixed') ? 'selected' : '' ?>>
                            Giảm cố định (VNĐ)
                        </option>
                        <option value="percent" <?= (($_POST['discount_type'] ?? '') == 'percent') ? 'selected' : '' ?>>
                            Giảm theo %
                        </option>
                    </select>

                    <label>Giá trị giảm</label>
                    <input type="number" step="0.01" name="discount_value" class="form-control"
                        value="<?= htmlspecialchars($_POST['discount_value'] ?? '') ?>" required>

                    <label>Số vé tối thiểu</label>
                    <input type="number" name="min_tickets" value="<?= htmlspecialchars($_POST['min_tickets'] ?? 1) ?>"
                        class="form-control">
                </div>

                <div class="col-md-6">

                    <label>Tuyến bay áp dụng</label>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <select name="route_from" class="form-select" required>
                                <option value="">-- Chọn sân bay đi --</option>
                                <option value="ALL" <?= (($_POST['route_from'] ?? '') == 'ALL') ? 'selected' : '' ?>>Tất cả</option>
                                <?php foreach($airports as $a): ?>
                                    <option value="<?= $a['code'] ?>" 
                                        <?= (($_POST['route_from'] ?? '') == $a['code']) ? 'selected' : '' ?>>
                                        <?= $a['code'] ?> - <?= $a['name_vn'] ?> (<?= $a['city_vn'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col">
                            <select name="route_to" class="form-select" required>
                                <option value="">-- Chọn sân bay đến --</option>
                                <option value="ALL" <?= (($_POST['route_to'] ?? '') == 'ALL') ? 'selected' : '' ?>>Tất cả</option>
                                <?php foreach($airports as $a): ?>
                                    <option value="<?= $a['code'] ?>"
                                        <?= (($_POST['route_to'] ?? '') == $a['code']) ? 'selected' : '' ?>>
                                        <?= $a['code'] ?> - <?= $a['name_vn'] ?> (<?= $a['city_vn'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <label>Ngày bắt đầu</label>
                    <input type="date" name="start_date" class="form-control"
                        value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>" required>

                    <label>Ngày kết thúc</label>
                    <input type="date" name="end_date" class="form-control"
                        value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>" required>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">Tạo Khuyến Mãi</button>
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
const promoCodeInput = document.getElementById('promoCode');

airlineSelect.addEventListener('change', function() {
    const selectedOption = airlineSelect.selectedOptions[0];
    const logo = selectedOption.dataset.logo || '';
    const airlineCode = selectedOption.textContent.trim().substring(0,2).toUpperCase(); 

    if (logo) {
        imageUrlInput.value = logo;
        promoImagePreview.src = logo;
        promoImagePreview.style.display = 'block';
    } else {
        imageUrlInput.value = '';
        promoImagePreview.style.display = 'none';
    }

    if (airlineCode) {
        const randomNumber = Math.floor(Math.random() * 90 + 10); 
        promoCodeInput.value = airlineCode + randomNumber + 'OFF'; 
    } else {
        promoCodeInput.value = '';
    }
});

<?php if(!empty($success)): ?>
    Swal.fire({
        icon: 'success',
        title: '<?=$success?>',
        confirmButtonText: 'OK'
    }).then(()=>{ window.location='add_promotion.php'; });
<?php elseif(!empty($errors)): ?>
    Swal.fire({
        icon: 'error',
        title: 'Thất bại!',
        html: '<?=implode("<br>", $errors)?>',
        confirmButtonText: 'OK'
    });
<?php endif; ?>

</script>

</body>
</html>