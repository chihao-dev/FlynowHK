# FlynowHK - Full Source (PHP + Admin)

FlynowHK là project demo đặt vé máy bay bằng PHP, kèm admin panel để quản lý dữ liệu.

---

## **Yêu cầu**

- PHP >= 8.0
- Composer (nếu có package dùng)
- MySQL / MariaDB
- Web server (PHP built-in server hoặc XAMPP, Laragon…)

---

## **Hướng dẫn cài đặt và chạy**

1. **Tải project về**
   - Giải nén vào thư mục mong muốn.

2. **Cài đặt các package (nếu có)**
   ```bash
   composer install

3. **Cấu hình database

   Tạo database mới (ví dụ: flynow_db)
   Cập nhật thông tin database trong file cấu hình của project (ví dụ db_connect.php):
   $servername = "localhost";
   $username = "root";
   $dbname = "flynow_db";

4. **Tài khoản admin mặc định**
   - Email: `admin@gmail.com`
   - Mật khẩu: `admin123`
   - Tài khoản sẽ tự động được tạo khi project chạy lần đầu nếu chưa tồn tại.

5. **Chạy project

   php artisan serve
   Mở trình duyệt:
   http://127.0.0.1:8000

6. **Đăng nhập admin

   Trang admin panel: http://127.0.0.1:8000/login
   Email: admin@gmail.com
   Mật khẩu: admin123
