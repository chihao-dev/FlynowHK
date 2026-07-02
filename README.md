# FlyNowHK - Hệ Thống Đặt Vé Máy Bay Trực Tuyến

FlyNowHK là một ứng dụng web cho phép người dùng tìm kiếm, đặt vé máy bay và quản lý thông tin chuyến bay một cách tiện lợi. Dự án được xây dựng dựa trên sự kết hợp giữa framework Laravel cho phía khách hàng và PHP thuần cho phía quản trị (Admin).

## Tính năng chính

### Dành cho Khách hàng
*   **Tìm kiếm chuyến bay:** Tìm kiếm linh hoạt theo điểm đi, điểm đến, ngày khởi hành.
*   **Đặt vé trực tuyến:** Hỗ trợ đặt vé một chiều và khứ hồi với nhiều hạng ghế (Thường, Cao cấp).
*   **Quản lý tài khoản:** Đăng ký, đăng nhập và cập nhật thông tin cá nhân.
*   **Quản lý vé đã đặt:** Xem lại lịch sử đặt vé và chi tiết từng vé (Mã đặt chỗ, số ghế, thông tin chuyến bay).
*   **Khuyến mãi:** Xem danh sách các chương trình khuyến mãi hiện hành.

### Dành cho Quản trị viên (Admin)
*   **Bảng điều khiển (Dashboard):** Thống kê nhanh tình hình kinh doanh.
*   **Quản lý chuyến bay/vé:** Thêm, sửa, xóa và liệt kê các chuyến bay.
*   **Quản lý người dùng:** Quản lý thông tin và phân quyền người dùng.
*   **Quản lý khuyến mãi:** Tạo và điều chỉnh các chương trình ưu đãi.

## Công nghệ sử dụng

*   **Backend:** Laravel 9 (Customer side) & Plain PHP (Admin side).
*   **Frontend:** Blade Template Engine, CSS, JavaScript, Bootstrap.
*   **Database:** MySQL.
*   **Công cụ:** Composer, Vite.

## Yêu cầu hệ thống

*   PHP >= 8.1
*   Composer
*   MySQL Server
*   Node.js & NPM (để build assets)

## Hướng dẫn cài đặt

1.  **Clone dự án:**
    ```bash
    git clone https://github.com/your-username/FlynowHK.git
    cd FlynowHK
    ```

2.  **Cài đặt dependencies:**
    ```bash
    composer install
    npm install
    ```

3.  **Cấu hình môi trường:**
    *   Sao chép file `.env.example` thành `.env`.
    *   Cấu hình thông tin kết nối database trong file `.env`.
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=flynowhk_db
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4.  **Tạo cơ sở dữ liệu:**
    *   Tạo một database mới trong MySQL (ví dụ: `flynowhk_db`).
    *   Import file `sqlFlyNow.sql` vào database vừa tạo.

5.  **Khởi tạo ứng dụng:**
    ```bash
    php artisan key:generate
    php artisan storage:link
    ```

6.  **Chạy ứng dụng:**
    ```bash
    php artisan serve
    ```
    Truy cập: `http://localhost:8000`

## Cấu trúc dự án

*   `app/`: Chứa các Model, Controller và logic xử lý chính của Laravel.
*   `public/admin/`: Chứa mã nguồn của trang quản trị (PHP thuần).
*   `resources/views/`: Chứa các giao diện Blade cho phía khách hàng.
*   `routes/`: Định nghĩa các đường dẫn (web.php, api.php).
*   `sqlFlyNow.sql`: File dump cơ sở dữ liệu.

---
© 2024 FlyNowHK Project.
