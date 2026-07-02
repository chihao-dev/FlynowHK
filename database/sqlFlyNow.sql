CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_active TIMESTAMP NULL DEFAULT NULL
);

UPDATE users
SET role = 'admin'
WHERE id = 1; 

CREATE TABLE user_info (
    user_id INT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    birthdate DATE,
    address VARCHAR(255),
    phone VARCHAR(20),
    avatar VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE airlines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,       
    code VARCHAR(5) UNIQUE NOT NULL,  
    logo_url VARCHAR(255) NULL        
);

INSERT INTO airlines (name, code, logo_url) VALUES 
('Vietnam Airlines', 'VN', 'assets/logos/vn.png'),   
('VietJet Air', 'VJ', 'assets/logos/vj.png'),        
('Bamboo Airways', 'QH', 'assets/logos/qh.png'),     
('Viettravel Airlines', 'VT', 'assets/logos/vt.png'); 

CREATE TABLE airports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_vn VARCHAR(100) NOT NULL,
    city_vn VARCHAR(100) NOT NULL,
    code CHAR(3) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO airports (name_vn, city_vn, code) VALUES
('Nội Bài', 'Hà Nội', 'HAN'),
('Tân Sơn Nhất', 'Hồ Chí Minh', 'SGN'),
('Đà Nẵng', 'Đà Nẵng', 'DAD'),
('Cam Ranh', 'Nha Trang', 'CXR'),
('Phú Quốc', 'Kiên Giang', 'PQC'),
('Cát Bi', 'Hải Phòng', 'HPH'),
('Vinh', 'Nghệ An', 'VII'),
('Phú Bài', 'Huế', 'HUI'),
('Cần Thơ', 'Cần Thơ', 'VCA'),
('Liên Khương', 'Đà Lạt', 'DLI'),
('Vân Đồn', 'Quảng Ninh', 'VDO'),
('Buôn Ma Thuột', 'Đắk Lắk', 'BMV'),
('Pleiku', 'Gia Lai', 'PXU'),
('Đồng Hới', 'Quảng Bình', 'VDH'),
('Chu Lai', 'Quảng Nam', 'CUI'),
('Thọ Xuân', 'Thanh Hóa', 'THD'),
('Tuy Hòa', 'Phú Yên', 'TBB'),
('Phú Cát', 'Quy Nhơn', 'UIH'),
('Rạch Giá', 'Kiên Giang', 'VKG'),
('Côn Đảo', 'Bà Rịa – Vũng Tàu', 'VCS'),
('Cà Mau', 'Cà Mau', 'CAH'),
('Điện Biên Phủ', 'Điện Biên', 'DIN');

CREATE TABLE flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    airline_id INT NOT NULL,
    flight_number VARCHAR(10) NOT NULL,
    departure_airport CHAR(3) NOT NULL,
    arrival_airport CHAR(3) NOT NULL,
    departure_time DATETIME NOT NULL,
    arrival_time DATETIME NOT NULL,
    duration VARCHAR(10) NOT NULL,
    flight_type ENUM('Bay thẳng','1 điểm dừng','2 điểm dừng') DEFAULT 'Bay thẳng',
    ticket_type ENUM('1 chiều','Khứ hồi') DEFAULT '1 chiều',
    base_price INT NOT NULL,
    baggage_limit INT DEFAULT 20,
    seats_normal INT DEFAULT 60,
    seats_premium INT DEFAULT 40,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (airline_id) REFERENCES airlines(id),
    FOREIGN KEY (departure_airport) REFERENCES airports(code),
    FOREIGN KEY (arrival_airport) REFERENCES airports(code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  flight_id INT NOT NULL,
  user_id INT NOT NULL,
  booking_code VARCHAR(30) NOT NULL UNIQUE,
  ticket_type ENUM('Thường','Cao cấp') NOT NULL,
  people_count INT NOT NULL,
  baggage_extra INT DEFAULT 0,
  total_price DECIMAL(10,2) NOT NULL,
  seat_numbers VARCHAR(255),       
  status ENUM('Chưa thanh toán','Đã thanh toán') DEFAULT 'Chưa thanh toán',
  contact_name VARCHAR(255), 
  contact_phone VARCHAR(50),
  contact_email VARCHAR(255),
  promo_code VARCHAR(50),
  passengers JSON,     
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (flight_id) REFERENCES flights(id)
);

CREATE TABLE promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    airline_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    discount_type ENUM('percent', 'fixed') DEFAULT 'fixed',
    discount_value DECIMAL(10,2) NOT NULL,
    min_tickets INT DEFAULT 1,
    route_from CHAR(3),
    route_to CHAR(3),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (airline_id) REFERENCES airlines(id)
);