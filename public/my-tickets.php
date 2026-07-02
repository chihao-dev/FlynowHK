<?php 
session_start();
include __DIR__.'/../db_connect.php';
include __DIR__.'/includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT b.*, f.flight_number, f.departure_airport, f.arrival_airport, 
               f.departure_time, f.arrival_time, f.base_price, f.seats_normal, f.seats_premium,
               f.ticket_type AS flight_ticket_type,
               a.name AS airline_name, a.logo_url
        FROM bookings b
        JOIN flights f ON b.flight_id = f.id
        JOIN airlines a ON f.airline_id = a.id
        WHERE b.user_id = ? AND b.status = 'Đã thanh toán'
        ORDER BY b.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Vé của tôi - FlyNow</title>
<link rel="stylesheet" href="css/my-tickets.css">
</head>
<body>
<div class="flights-page">
<h1>🎟 Vé máy bay của tôi</h1>

<?php
    if ($result->num_rows > 0):
        $currentDate = ''; 
        while($ticket = $result->fetch_assoc()):
        $final_price = $ticket['total_price']; 
        $promoText = "";

        if (!empty($ticket['promo_code'])) {

            $sqlPromo = "SELECT * FROM promotions WHERE code = ? LIMIT 1";
            $stmtPromo = $conn->prepare($sqlPromo);
            $stmtPromo->bind_param("s", $ticket['promo_code']);
            $stmtPromo->execute();
            $promo = $stmtPromo->get_result()->fetch_assoc();

            if ($promo) {
                $original_price = $ticket['total_price'];

                if ($promo['discount_type'] === 'percent') {
                    $discount_amount = $original_price * ($promo['discount_value'] / 100);
                    $final_price = $original_price - $discount_amount;

                    $promoText = " (đã áp dụng -{$promo['discount_value']}%)";

                } else {
                    $final_price = $original_price - $promo['discount_value'];
                    if ($final_price < 0) $final_price = 0;

                    $promoText = " (đã giảm " . number_format($promo['discount_value'],0,',','.') . "đ)";
                }
            }
        }

            $createdDate = date('d/m/Y', strtotime($ticket['created_at']));
            if ($createdDate !== $currentDate):
                $currentDate = $createdDate;
                echo '<div class="date-divider"> Ngày ' . $currentDate . '</div>';
            endif;

            $qrURL = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($ticket['booking_code']);
            $depart = date('H:i d/m/Y', strtotime($ticket['departure_time']));
            $arrive = date('H:i d/m/Y', strtotime($ticket['arrival_time']));

            if (strtolower($ticket['ticket_type']) === 'cao cấp') {
                $ticketClassName = 'Premium Class';
                $ticketClassCss = 'premium';
            } else {
                $ticketClassName = 'Economy Class';
                $ticketClassCss = 'normal';
            }
        ?>
        
        <div class="ticket">
            <div class="ticket-class-banner <?= $ticketClassCss ?>">
                <?= $ticketClassName ?>
            </div>

            <div class="ticket-left">
                <div class="airline-info">
                    <?php if ($ticket['logo_url']): ?>
                        <img src="<?= $ticket['logo_url'] ?>" alt="Logo Hãng">
                    <?php endif; ?>
                    <span><?= htmlspecialchars($ticket['airline_name']) ?> - <?= htmlspecialchars($ticket['flight_number']) ?></span>
                </div>

                <div class="flight-details">
                    <div class="flight-route"><?= $ticket['departure_airport'] ?> ✈ <?= $ticket['arrival_airport'] ?></div>
                    <div class="flight-time"><?= $depart ?> → <?= $arrive ?></div>
                    <div class="info-group">
                        <span class="info-label">Loại vé:</span>
                        <span class="info-value"><?= $ticketClassName ?></span>
                    </div>
                </div>

                <div class="passenger-info">
                    <div class="info-group"><span class="info-label">Tên hành khách:</span><span class="info-value"><?= htmlspecialchars($ticket['contact_name']) ?></span></div>
                    <div class="info-group"><span class="info-label">SĐT:</span><span class="info-value"><?= htmlspecialchars($ticket['contact_phone']) ?></span></div>
                    <div class="info-group"><span class="info-label">Email:</span><span class="info-value"><?= htmlspecialchars($ticket['contact_email']) ?></span></div>
                    <div class="info-group"><span class="info-label">Số ghế:</span><span class="info-value"><?= htmlspecialchars($ticket['seat_numbers']) ?></span></div>
                    <div class="info-group"><span class="info-label">Số lượng hành khách:</span><span class="info-value"><?= htmlspecialchars($ticket['people_count']) ?></span></div>
                </div>
                        
                <div class="price">
                    Tổng tiền: <?= number_format($final_price, 0, ',', '.') ?> VNĐ 
                    <span style="color:#28a745; font-size:14px;"><?= $promoText ?></span>
                </div>
            </div>

            <div class="ticket-right">
                <div class="qr-code"><img src="<?= $qrURL ?>" alt="QR Code"></div>
                <div class="booking-code"><?= $ticket['booking_code'] ?></div>
                <div class="note">Vui lòng check-in tại quầy trước khi bay ✈</div>

                <div style="margin-top:10px;">
                    <a href="ticket-detail.php?id=<?= $ticket['id'] ?>" 
                    style="padding: 6px 12px; background:#007bff; color:#fff; border-radius:4px; text-decoration:none;">
                    Xem thông tin
                    </a>
                </div>
            </div>
        </div>
<?php
    endwhile;
else:
?>
    <p style="text-align:center; color:#555;">Bạn chưa có vé máy bay nào được thanh toán.</p>
<?php endif; ?>
</div>
</body>
</html>

<?php include __DIR__.'/includes/footer.php'; ?>
