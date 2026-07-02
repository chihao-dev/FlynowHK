<?php 
require __DIR__ . '/../app/Http/Controllers/PromotionController.php';
include __DIR__.'/includes/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin khuyến mãi - Flynow</title>
    <link rel="stylesheet" href="css/promotions.css">
</head>
<body>

<section class="promo-hero">
    <div class="promo-hero-content">
        <h1>Tin khuyến mãi & Mã giảm giá hot nhất</h1>
        <p>Nhận ngay ưu đãi độc quyền từ Flynow – giảm giá cho các chuyến bay nội địa & quốc tế, áp dụng cho các hãng hàng không hàng đầu Việt Nam.</p>
    </div>
</section>

<section class="promotions">

    <section class="promo-filter sticky-filter">
        <div class="filter-container">
            <input type="text" id="searchCode" placeholder="Tìm kiếm mã..." oninput="applyFilters()">

            <select id="filterAirline" onchange="applyFilters()">
                <option value="">-- Chọn hãng --</option>
                <?php 
                $airlines = $conn->query("SELECT id, name FROM airlines")->fetch_all(MYSQLI_ASSOC);
                foreach($airlines as $a) {
                    echo "<option value='{$a['name']}'>" . htmlspecialchars($a['name']) . "</option>";
                }
                ?>
            </select>

            <select id="filterExpiry" onchange="applyFilters()">
                <option value="">-- Hạn dùng --</option>
                <option value="valid">Còn hiệu lực</option>
                <option value="expired">Đã hết hạn</option>
            </select>
        </div>
    </section>

    <div class="promo-grid" id="promoGrid">
        <?php foreach($full_promotions as $promo): ?>
            <div class="promo-card" 
                 data-code="<?= htmlspecialchars($promo['code']) ?>"
                 data-airline="<?= htmlspecialchars($promo['airline_name']) ?>"
                 data-end="<?= strtotime($promo['end_date']) ?>">
                <img src="<?= htmlspecialchars($promo['logo_url']) ?>" alt="<?= htmlspecialchars($promo['airline_name']) ?>">
                <div class="promo-info">
                    <h3><?= htmlspecialchars($promo['airline_name']) ?> - Mã: <span class="code"><?= htmlspecialchars($promo['code']) ?></span></h3>
                    <p class="promo-description"><?= !empty($promo['description']) ? htmlspecialchars($promo['description']) : "Không có mô tả." ?></p>
                    <p>Điểm đi: <strong><?= $promo['route_from'] === 'ALL' ? 'Tất cả' : htmlspecialchars($promo['route_from']) ?></strong> 
                       → Điểm đến: <strong><?= $promo['route_to'] === 'ALL' ? 'Tất cả' : htmlspecialchars($promo['route_to']) ?></strong>
                    </p>
                    <p>Áp dụng từ <?= intval($promo['min_tickets']) ?> vé trở lên</p>
                    <p>Hạn dùng: <?= date('d/m/Y', strtotime($promo['start_date'])) ?> → <?= date('d/m/Y', strtotime($promo['end_date'])) ?></p>
                    <button class="btn-copy" onclick="copyCode('<?= htmlspecialchars($promo['code']) ?>')">Sao chép mã</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
const promotions = <?php 
    $promoList = $conn->query("
        SELECT * FROM promotions
    ")->fetch_all(MYSQLI_ASSOC); 
    echo json_encode($promoList); 
?>;

function copyCode(code) {
    navigator.clipboard.writeText(code);
    alert("Đã sao chép mã: " + code);
}

function applyFilters() {
    const search = document.getElementById('searchCode').value.toLowerCase();
    const airline = document.getElementById('filterAirline').value;
    const expiry = document.getElementById('filterExpiry').value;
    const today = new Date().getTime() / 1000; // timestamp

    document.querySelectorAll('#promoGrid .promo-card').forEach(card => {
        const code = card.dataset.code.toLowerCase();
        const cardAirline = card.dataset.airline;
        const endDate = parseInt(card.dataset.end);

        let show = true;

        if(search && !code.includes(search)) show = false;
        if(airline && airline !== cardAirline) show = false;
        if(expiry === 'valid' && endDate < today) show = false;
        if(expiry === 'expired' && endDate >= today) show = false;

        card.style.display = show ? 'block' : 'none';
    });
}
</script>

</body>
</html>

<?php include __DIR__.'/includes/footer.php'; ?>
