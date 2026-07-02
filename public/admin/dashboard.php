<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

include __DIR__ . '/../../db_connect.php';
include 'layout/header.php';

require __DIR__ . '/../../app/Http/Controllers/DashboardController.php';

$controller = new DashboardController($conn);
$data = $controller->getDashboardData();

$conn->close();

$totalFlights = $data['totalFlights'];
$totalBookings = $data['totalBookings'];
$totalRevenue = $data['totalRevenue'];
$upcomingFlights = $data['upcomingFlights'];
$recentBookings = $data['recentBookings'];
$bookingStatusCounts = $data['bookingStatusCounts'];
$bookingStatusJson = json_encode($bookingStatusCounts);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Quản Lý Vé Máy Bay</title>
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>


<div class="container py-5">
    <div class="admin-header">
        <div>
            <h2>Dashboard Tổng quan</h2>
            <p class="text-muted mb-0">Chào mừng Admin, đây là cái nhìn tổng quát về hệ thống.</p>
        </div>
    </div>

    <div class="kpi-row">
        <div class="kpi-cards">
            <div class="kpi-card card-flights">
                <span class="icon" style="font-size: 30px;"><i class="fas fa-plane-departure"></i></span>
                <p><?php echo $totalFlights; ?></p>
                <h3>Tổng số Chuyến bay</h3>
            </div>
            <div class="kpi-card card-bookings">
                <span class="icon" style="font-size: 30px;"><i class="fas fa-ticket-alt"></i></span>
                <p><?php echo $totalBookings; ?></p>
                <h3>Tổng số Đơn đặt vé</h3>
            </div>
            <div class="kpi-card card-revenue">
                <span class="icon" style="font-size: 30px;"><i class="fas fa-money-bill-wave"></i></span>
                <p><?php echo $totalRevenue; ?> VNĐ</p>
                <h3>Tổng Doanh thu (Đã TT)</h3>
            </div>
        </div>
        
        <div class="chart-panel">
            <h2>Tỷ lệ Trạng thái Booking</h2>
            <div class="chart-container">
                <canvas id="bookingStatusChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="recent-data">
        
        <div class="data-panel">
            <h2><i class="fas fa-calendar-alt me-2"></i> Chuyến bay Sắp tới (Upcoming)</h2>
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th>Mã CB</th>
                        <th>Hãng</th>
                        <th>Khởi hành</th>
                        <th>Giờ đi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($upcomingFlights)): ?>
                        <tr><td colspan="4" class="text-center py-3">Không có chuyến bay sắp tới.</td></tr>
                    <?php else: ?>
                        <?php foreach ($upcomingFlights as $flight): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($flight['flight_number']); ?></td>
                                <td><?php echo htmlspecialchars($flight['airline_name']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($flight['departure_time'])); ?></td>
                                <td><?php echo date('H:i', strtotime($flight['departure_time'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="data-panel">
            <h2><i class="fas fa-receipt me-2"></i> Đơn đặt vé Gần đây</h2>
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th>Mã Booking</th>
                        <th>Mã CB</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentBookings)): ?>
                        <tr><td colspan="4" class="text-center py-3">Không có đơn đặt vé gần đây.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentBookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['booking_code']); ?></td>
                                <td><?php echo htmlspecialchars($booking['flight_number']); ?></td>
                                <td><?php echo number_format($booking['total_price'], 0, ',', '.'); ?>đ</td>
                                <td><span class="badge <?php echo ($booking['status'] == 'Đã thanh toán' ? 'bg-success' : 'bg-warning text-dark'); ?>"><?php echo htmlspecialchars($booking['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    const bookingStatusData = <?php echo $bookingStatusJson; ?>;

    const dataValues = [
        bookingStatusData['Đã thanh toán'],
        bookingStatusData['Chưa thanh toán']
    ];

    const ctx = document.getElementById('bookingStatusChart').getContext('2d');
    
    const labelsVietnamese = ['Đã thanh toán', 'Chưa thanh toán'];
    
    const backgroundColors = [
        '#2ecc71', 
        '#f39c12'  
    ];

    const bookingStatusChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labelsVietnamese,
            datasets: [{
                data: dataValues,
                backgroundColor: backgroundColors,
                hoverOffset: 8,
                borderWidth: 1.5,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            family: 'Poppins',
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                const total = dataValues.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) + '%' : '0%';
                                label += context.parsed + ' đơn (' + percentage + ')';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>