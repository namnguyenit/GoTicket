-- Xóa database cũ nếu tồn tại để làm lại từ đầu
DROP DATABASE IF EXISTS VEXE;

-- Tạo database mới
CREATE DATABASE IF NOT EXISTS VEXE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Sử dụng database vừa tạo
USE VEXE;

-- 1. Bảng `users`: Lưu thông tin người dùng (khách hàng, nhà xe, admin)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20),
    role ENUM('customer', 'vendor', 'admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Bảng `vendors`: Lưu thông tin nhà xe, mở rộng từ `users`
CREATE TABLE vendors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    company_name VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    status ENUM('active', 'pending', 'suspended') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. Bảng `routes`: Lưu các tuyến đường GỐC, chung chung (VD: Hà Nội -> Sài Gòn)
CREATE TABLE routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origin_location VARCHAR(255) NOT NULL,
    destination_location VARCHAR(255) NOT NULL
);

-- 4. Bảng `vehicles`: Đại diện cho đoàn tàu hoặc một chiếc xe bus của nhà xe
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    vehicle_type ENUM('bus', 'train') NOT NULL,
    license_plate VARCHAR(50) UNIQUE, -- Có thể null cho tàu
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE CASCADE
);

-- 5. Bảng `coaches`: Đại diện cho 1 toa tàu hoặc chính chiếc xe bus đó
CREATE TABLE coaches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    identifier VARCHAR(50) NOT NULL, -- Mã định danh cho toa/xe (vd: "Toa 1", "Biển số 29B-12345")
    coach_type ENUM('sleeper_vip', 'sleeper_regular', 'seat_soft', 'seat_hard', 'limousine') NOT NULL,
    total_seats INT NOT NULL,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

-- 6. Bảng `seats`: Định nghĩa từng ghế vật lý của một `coach`
CREATE TABLE seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coach_id INT NOT NULL,
    seat_number VARCHAR(10) NOT NULL, -- vd: A1, B12, G15
    UNIQUE KEY (coach_id, seat_number),
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE
);

-- 7. Bảng `stops`: Lưu thông tin các điểm dừng, bến xe, văn phòng
CREATE TABLE stops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL
);

-- 8. Bảng `vendor_routes`: Định nghĩa tuyến đường CỤ THỂ của từng nhà xe
CREATE TABLE vendor_routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    route_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE CASCADE,
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE CASCADE
);

-- 9. Bảng `vendor_route_stops`: Template các điểm dừng cho `vendor_routes`
CREATE TABLE vendor_route_stops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_route_id INT NOT NULL,
    stop_id INT NOT NULL,
    stop_type ENUM('pickup', 'dropoff') NOT NULL,
    stop_order INT NOT NULL,
    offset_minutes_from_departure INT DEFAULT 0,
    FOREIGN KEY (vendor_route_id) REFERENCES vendor_routes(id) ON DELETE CASCADE,
    FOREIGN KEY (stop_id) REFERENCES stops(id) ON DELETE CASCADE,
    UNIQUE KEY (vendor_route_id, stop_id, stop_type)
);

-- 10. Bảng `trips`: Một chuyến đi cụ thể, là một lần thực thi của `vendor_routes`
CREATE TABLE trips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_route_id INT NOT NULL,
    departure_datetime DATETIME NOT NULL,
    arrival_datetime DATETIME NOT NULL,
    base_price DECIMAL(10, 2) NOT NULL,
    status ENUM('scheduled', 'ongoing', 'completed', 'cancelled') DEFAULT 'scheduled',
    FOREIGN KEY (vendor_route_id) REFERENCES vendor_routes(id) ON DELETE CASCADE
);

-- 11. Bảng `trip_coaches`: Gán các toa/xe cụ thể vào một chuyến đi
CREATE TABLE trip_coaches (
    trip_id INT NOT NULL,
    coach_id INT NOT NULL,
    coach_order INT DEFAULT 1,
    PRIMARY KEY (trip_id, coach_id),
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE
);

-- 12. Bảng `trip_seats`: Kho vé cho mỗi chuyến đi
CREATE TABLE trip_seats (
    trip_id INT NOT NULL,
    seat_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    status ENUM('available', 'booked', 'locked', 'disabled') DEFAULT 'available',
    PRIMARY KEY (trip_id, seat_id),
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE CASCADE
);

-- 13. Bảng `trip_stops`: Các điểm dừng thực tế với thời gian cụ thể cho một chuyến đi
CREATE TABLE trip_stops (
    trip_id INT NOT NULL,
    stop_id INT NOT NULL,
    stop_type ENUM('pickup', 'dropoff') NOT NULL,
    scheduled_time TIME NOT NULL,
    PRIMARY KEY (trip_id, stop_id, stop_type),
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (stop_id) REFERENCES stops(id) ON DELETE CASCADE
);

-- 14. Bảng `bookings`: Lưu thông tin về một lần đặt vé của người dùng
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_code VARCHAR(50) UNIQUE NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 15. Bảng `booking_details` (ĐÃ SỬA ĐỔI): Chi tiết các vé và lựa chọn điểm đón/trả
CREATE TABLE booking_details (
    booking_id INT NOT NULL,
    trip_id INT NOT NULL,
    seat_id INT NOT NULL,
    price_at_booking DECIMAL(10, 2) NOT NULL,
    
    -- CÁC CỘT MỚI ĐỂ LƯU LỰA CHỌN CỦA KHÁCH HÀNG --
    pickup_stop_id INT, -- ID của điểm đón mà khách đã chọn cho vé này
    dropoff_stop_id INT, -- ID của điểm trả mà khách đã chọn cho vé này
    
    PRIMARY KEY (booking_id, trip_id, seat_id),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (trip_id, seat_id) REFERENCES trip_seats(trip_id, seat_id) ON DELETE CASCADE,
    
    -- Khóa ngoại đến bảng `stops`
    FOREIGN KEY (pickup_stop_id) REFERENCES stops(id) ON DELETE SET NULL,
    FOREIGN KEY (dropoff_stop_id) REFERENCES stops(id) ON DELETE SET NULL
);
-- KẾT THÚC PHẦN SỬA ĐỔI --

-- 16. Bảng `payments`: Lưu thông tin thanh toán cho một booking
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL UNIQUE,
    transaction_id VARCHAR(255) UNIQUE,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50),
    status ENUM('success', 'failed', 'pending') DEFAULT 'pending',
    paid_at TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- 17. Bảng `reviews`: Đánh giá của người dùng cho một chuyến đi
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_id INT NOT NULL UNIQUE,
    rating TINYINT UNSIGNED NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- 18. Bảng `blogs`: Các bài blog, tin tức
CREATE TABLE blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    author_id INT,
    published_at TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 19. Bảng `blog_trip`: Gắn các chuyến đi liên quan vào một bài blog
CREATE TABLE blog_trip (
    blog_id INT NOT NULL,
    trip_id INT NOT NULL,
    PRIMARY KEY (blog_id, trip_id),
    FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE
);