<?php
require __DIR__ . '/../app/Http/Controllers/CheapTicketController.php';
include __DIR__.'/includes/header.php'; 

include __DIR__ . '/../db_connect.php';

$sql = "SELECT * FROM flights ORDER BY departure_time ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Danh sách chuyến bay</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="./css/cheap-tickets.css">
<style>
body { font-family: Poppins, sans-serif; margin:0; padding:0; background:#f0f2f5; }
.flights-page { display:flex; gap:20px; padding:20px; }
.flights-list { flex:1; display:flex; flex-direction:column; }
</style>
</head>
<body>

<div class="flights-page">
    <div class="filters">
        <h3>Bộ lọc chuyến bay</h3>
        <div class="filter-group">
            <label>Hãng bay:</label>
            <select id="filter-airline">
                <option value="">Tất cả</option>
                <?php
                $airlines = array_unique(array_column($flights, 'airline_name'));
                foreach($airlines as $al){
                    $selected = ($al === ($_POST['airline'] ?? '')) ? 'selected' : '';
                    echo "<option value='$al' $selected>$al</option>";
                }
                ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Tìm theo mã hiệu chuyến bay:</label>
            <input type="text" id="filter-flight-code" placeholder="VD: VN123">
        </div>

        <div class="filter-group">
            <label>Điểm đi (From):</label>
            <select id="filter-from">
                <option value="">Tất cả</option>
                <?php 
                    $froms = array_unique(array_column($flights, 'departure_airport'));
                    foreach($froms as $f){
                        $label = $airports[$f] ?? $f;
                        $selected = ($label === ($from ?? '')) ? 'selected' : '';
                        echo "<option value='$f' $selected>$label</option>";
                    }
                ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Điểm đến (To):</label>
            <select id="filter-to">
                <option value="">Tất cả</option>
                <?php 
                    $tos = array_unique(array_column($flights, 'arrival_airport'));
                    foreach($tos as $t){
                        $label = $airports[$t] ?? $t;
                        $selected = ($label === ($to ?? '')) ? 'selected' : '';
                        echo "<option value='$t' $selected>$label</option>";
                    }
                ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Các vé từ ngày:</label>
            <input type="date" id="filter-date-from" value="<?php echo htmlspecialchars($date_go ?? ''); ?>">
        </div>
        <div class="filter-group">
            <label>Đến ngày:</label>
            <input type="date" id="filter-date-to" value="<?php echo htmlspecialchars($date_return ?? ''); ?>">
        </div>
        <button id="btn-apply-filters">Áp dụng</button>
        <button id="btn-clear-filters" style="margin-top:10px; background:#f44336; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer;">
            Xóa bộ lọc
        </button>
    </div>

    <div class="flights-list-wrapper">
        <div class="flights-controls">
            <div class="date-scroll">
                <button class="scroll-left"><i class="fa-solid fa-chevron-left"></i></button>
                <div class="dates-container" id="dates-container"></div>
                <button class="scroll-right"><i class="fa-solid fa-chevron-right"></i></button>
            </div>

            <div class="sort-buttons">
                <button id="sort-date" data-order="esc">Ngày <i class="fa-solid fa-arrow-right"></i></button>
                <button id="sort-price" data-order="asc">Giá <i class="fa-solid fa-arrow-up"></i></button>
            </div>
        </div>

        <h2 id="flights-title" style="margin-bottom:20px; color:#1a73e8;">Lịch trình theo ngày</h2>

        <div class="flights-list" id="flights-container"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  window.FLIGHT_DATA = {
    flights: <?= json_encode($flights, JSON_UNESCAPED_UNICODE) ?>,
    selectedDate: "<?= $date_go ?>",
    z: "<?= $_POST['z'] ?? '' ?>",
    hasPostData: <?= (!empty($_POST['from']) || !empty($_POST['to']) || !empty($_POST['date_go']) || !empty($_POST['date_return']) || !empty($_POST['airline'])) ? 'true' : 'false' ?>
  };
</script>

<script src="js/cheap-ticket.js"></script>



</body>
</html>

<?php include __DIR__.'/includes/footer.php'; ?>