<?php 
require __DIR__ . '/../app/Http/Controllers/GuideController.php';
include __DIR__.'/includes/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hướng dẫn đặt vé - Flynow</title>
    <link rel="stylesheet" href="css/guide.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
</head>
<body>


<section class="hero-guide">
    <div class="hero-content">
        <h1>Hướng dẫn đặt vé máy bay tại Flynow</h1>
        <p>Chỉ với vài bước đơn giản, bạn có thể sở hữu ngay tấm vé bay đến điểm đến mơ ước!</p>
    </div>
</section>

<section class="steps">
    <div class="step-container">
        <div class="step">
            <img src="img/buoc1.png" alt="Tìm chuyến bay">
            <div class="text">
                <h3>Bước 1: Tìm chuyến bay</h3>
                <p>Truy cập vào trang chủ Flynow, sau đó nhập điểm khởi hành, điểm đến, ngày bay mong muốn và số lượng hành khách. Bạn có thể lựa chọn chuyến bay một chiều hoặc khứ hồi. Ngay sau khi hoàn tất thông tin, hệ thống sẽ tự động tìm kiếm và hiển thị danh sách các chuyến bay phù hợp nhất theo thời gian, giá vé và hãng bay để bạn dễ dàng lựa chọn.</p>
            </div>
        </div>

        <div class="step">
            <img src="img/buoc2.png" alt="Chọn chuyến bay">
            <div class="text">
                <h3>Bước 2: Chọn chuyến bay</h3>
                <p>Xem chi tiết từng chuyến bay, bao gồm thời gian cất cánh – hạ cánh, thời lượng bay, hành lý đi kèm và giá vé của từng hãng hàng không. Bạn có thể so sánh các mức giá hoặc sử dụng bộ lọc để tìm chuyến bay phù hợp nhất. Khi đã chọn được chuyến bay ưng ý, nhấn “Chọn chuyến” để chuyển sang bước tiếp theo.</p>
            </div>
        </div>

        <div class="step">
            <img src="img/buoc3.png" alt="Nhập thông tin">
            <div class="text">
                <h3>Bước 3: Nhập thông tin hành khách</h3>
                <p>Điền đầy đủ và chính xác thông tin của từng hành khách như họ tên, giới tính, ngày sinh, số CMND/CCCD hoặc hộ chiếu. Đây là thông tin quan trọng để đảm bảo quá trình đặt vé và làm thủ tục tại sân bay diễn ra thuận lợi. Bạn cũng có thể lựa chọn thêm dịch vụ bổ sung như hành lý ký gửi, suất ăn hoặc chọn chỗ ngồi nếu có nhu cầu.</p>
            </div>
        </div>

        <div class="step">
            <img src="img/buoc4.png" alt="Thanh toán">
            <div class="text">
                <h3>Bước 4: Thanh toán</h3>
                <p>Chọn phương thức thanh toán phù hợp như chuyển khoản ngân hàng, ví điện tử (Momo, ZaloPay...) hoặc thanh toán qua thẻ quốc tế. Tất cả giao dịch đều được xử lý an toàn và bảo mật. Sau khi thanh toán thành công, hệ thống sẽ tự động xác nhận và gửi thông tin đặt vé, hóa đơn, cũng như vé điện tử đến email bạn đã cung cấp.</p>
            </div>
        </div>

        <div class="step">
            <img src="img/buoc5.png" alt="Nhận vé điện tử">
            <div class="text">
                <h3>Bước 5: Nhận vé và chuẩn bị khởi hành</h3>
                <p>Kiểm tra email để nhận mã đặt chỗ (PNR) và vé điện tử (E-ticket). Trước ngày bay, bạn có thể chủ động check-in online nếu hãng bay hỗ trợ. Khi đến sân bay, chỉ cần mang theo giấy tờ tùy thân và mã vé là có thể làm thủ tục nhanh chóng. Hãy đến sớm 1–2 giờ để đảm bảo hành trình của bạn diễn ra thuận lợi.</p>
            </div>
        </div>
    </div>
</section>


</body>
</html>
<?php include __DIR__.'/includes/footer.php'; ?>
