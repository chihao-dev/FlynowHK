
<?php
require __DIR__ . '/../app/Http/Controllers/AboutController.php';
include __DIR__.'/includes/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu Flynow - Vibe Máy Bay</title>
    <link rel="stylesheet" href="css/about.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Montserrat:wght@600;800&display=swap" rel="stylesheet">
</head>
<body>
    <section class="hero-about">
        <div class="hero-content">
            <h1>Chào mừng đến với Flynow</h1>
            <p>
                Chúng tôi không chỉ đưa bạn đến những điểm đến mơ ước trên bản đồ thế giới, mà còn mang đến trải nghiệm bay sang trọng, tiện nghi và an toàn tuyệt đối. 
                Từ khoang hành khách rộng rãi, ghế ngồi êm ái, đến dịch vụ chăm sóc tận tâm từng chi tiết, Flynow cam kết mỗi chuyến bay là một hành trình đáng nhớ. 
                Bay cùng Flynow, bạn sẽ cảm nhận đẳng cấp và sự khác biệt từ từng khoảnh khắc, từ khi đặt vé, làm thủ tục, cho đến khi hạ cánh an toàn.
            </p>
            <p>
                Với đội bay hiện đại, phi công giàu kinh nghiệm, hệ thống đặt vé trực tuyến thông minh và dịch vụ khách hàng 24/7, Flynow không chỉ là phương tiện di chuyển, 
                mà là trải nghiệm du lịch và công việc đẳng cấp, tiện nghi và đầy cảm hứng.
            </p>
        </div>
    </section>

    <section class="history">
        <h2>Lịch sử hình thành</h2>
        <p>
            Flynow được thành lập vào năm 2010 với sứ mệnh trở thành hãng hàng không uy tín, mang lại dịch vụ chất lượng cao nhưng vẫn đảm bảo giá cả hợp lý cho mọi khách hàng. 
            Chúng tôi bắt đầu từ những chuyến bay nội địa đầu tiên, với chỉ vài đường bay và đội ngũ khiêm tốn, nhưng với niềm tin vào tầm nhìn dài hạn, Flynow nhanh chóng mở rộng mạng lưới, 
            cải tiến quy trình phục vụ và nâng cấp đội bay hiện đại.
        </p>
        <p>
            Trong hơn một thập kỷ hoạt động, Flynow đã phục vụ hàng triệu hành khách, từ những chuyến công tác ngắn ngày đến những hành trình du lịch xa xôi. 
            Chúng tôi luôn ưu tiên sự an toàn, tiện nghi và trải nghiệm khách hàng, áp dụng công nghệ tiên tiến từ hệ thống đặt vé trực tuyến, quản lý hành lý, đến các tiện ích trên chuyến bay. 
            Mỗi chuyến bay là minh chứng cho cam kết chất lượng và sự tận tâm mà Flynow dành cho khách hàng.
        </p>
        <img src="./img/gioithieumaybay.jpg" alt="Lịch sử Flynow" class="history-img">
    </section>


    <section class="mission">
        <div class="mission-text">
            <h2>Sứ mệnh & Tầm nhìn</h2>
            <p>
                <strong>Sứ mệnh:</strong> Flynow mang đến cho khách hàng trải nghiệm bay hoàn hảo, an toàn tuyệt đối, tiện nghi vượt trội và dịch vụ tận tâm. 
                Chúng tôi coi mỗi chuyến bay là cơ hội để tạo ra những khoảnh khắc đáng nhớ, giúp hành khách cảm nhận sự khác biệt trong từng dịch vụ, từ việc hỗ trợ đặt vé, check-in nhanh chóng, 
                đến chăm sóc tận nơi trên khoang.
            </p>
            <p>
                <strong>Tầm nhìn:</strong> Trở thành hãng vé máy bay hàng đầu Việt Nam, dẫn đầu về chất lượng dịch vụ, trải nghiệm khách hàng và đổi mới công nghệ. 
                Flynow hướng đến mở rộng mạng bay quốc tế, hợp tác với các đối tác hàng không hàng đầu thế giới, và thiết lập tiêu chuẩn mới cho ngành hàng không Việt Nam về an toàn, tiện nghi và đẳng cấp.
            </p>
        </div>
        <div class="mission-img">
            <img src="./img/tamnhinsumenh.jpg" alt="Sứ mệnh Flynow">
        </div>
    </section>


<section class="team">
    <h2>Đội ngũ Flynow</h2>
    <p>Chúng tôi tự hào về đội ngũ nhân viên giàu kinh nghiệm, chuyên nghiệp và tận tâm.</p>
    <div class="team-cards">
        <div class="card">
            <img src="img/nam.jpg" alt="Nguyen Van A">
            <div class="card-info">
                <h3>Nguyễn Văn A</h3>
                <p>Trưởng phi hành đoàn</p>
            </div>
        </div>
        <div class="card">
            <img src="img/nu.jpg" alt="Tran Thi B">
            <div class="card-info">
                <h3>Trần Thị B</h3>
                <p>Chuyên viên dịch vụ khách hàng</p>
            </div>
        </div>
</section>

<section class="partners">
    <h2>Đối tác hàng không</h2>
    <p>Flynow hợp tác với các hãng hàng không uy tín trong và ngoài nước để mang đến giá vé tốt nhất cho hành khách.</p>
    <div class="partner-logos">
        <div class="partner-card">
            <img src="img/vietjet.jpg" alt="Vietjet Air">
            <span>Vietjet Air</span>
        </div>
        <div class="partner-card">
            <img src="img/vietnam.png" alt="Vietnam Airlines">
            <span>Vietnam Airlines</span>
        </div>
        <div class="partner-card">
            <img src="img/bamboo.jpg" alt="Bamboo Airways">
            <span>Bamboo Airways</span>
        </div>
        <div class="partner-card">
            <img src="img/viettravel.jpg" alt="Vietravel Airlines">
            <span>Vietravel Airlines</span>
        </div>
    </div>
</section>

</body>
</html>

<?php include __DIR__.'/includes/footer.php'; ?>