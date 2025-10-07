-- Xóa database cũ nếu tồn tại để làm lại từ đầu
DROP DATABASE IF EXISTS vexe;

-- Tạo database mới với bộ ký tự chuẩn
CREATE DATABASE IF NOT EXISTS vexe CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Sử dụng database vừa tạo
USE vexe;

-- 1. Bảng `users`: Lưu thông tin người dùng (khách hàng, nhà xe, admin)
CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone_number` VARCHAR(20) NULL,
    `role` ENUM('customer', 'vendor', 'admin') NOT NULL DEFAULT 'customer',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Bảng `locations`: Bảng "từ điển" chứa các tỉnh/thành phố
CREATE TABLE `locations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE
);

-- 3. Bảng `routes`: Lưu các tuyến đường GỐC, tham chiếu đến `locations`
CREATE TABLE `routes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `origin_location_id` INT UNSIGNED NOT NULL,
    `destination_location_id` INT UNSIGNED NOT NULL,
    FOREIGN KEY (`origin_location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`destination_location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
    UNIQUE KEY `routes_origin_destination_unique` (`origin_location_id`, `destination_location_id`)
);

-- 4. Bảng `stops`: Lưu thông tin các điểm dừng, bến xe, văn phòng
CREATE TABLE `stops` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255) NOT NULL
);

-- 5. Bảng `vendors`: Lưu thông tin nhà xe
CREATE TABLE `vendors` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL UNIQUE,
    `company_name` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255) NULL,
    `status` ENUM('active', 'pending', 'suspended') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- 6. Bảng `vendor_routes`: Tuyến đường CỤ THỂ của từng nhà xe
CREATE TABLE `vendor_routes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `vendor_id` INT UNSIGNED NOT NULL,
    `route_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE
);

-- 7. Bảng `vendor_route_stops`: Template các điểm dừng cho `vendor_routes`
CREATE TABLE `vendor_route_stops` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `vendor_route_id` INT UNSIGNED NOT NULL,
    `stop_id` INT UNSIGNED NOT NULL,
    `stop_type` ENUM('pickup', 'dropoff') NOT NULL,
    `stop_order` INT NOT NULL,
    `offset_minutes_from_departure` INT NOT NULL DEFAULT 0,
    FOREIGN KEY (`vendor_route_id`) REFERENCES `vendor_routes` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`stop_id`) REFERENCES `stops` (`id`) ON DELETE CASCADE,
    UNIQUE KEY `vendor_route_stops_unique` (`vendor_route_id`, `stop_id`, `stop_type`)
);

-- 8. Bảng `vehicles`: Xe bus hoặc đoàn tàu của nhà xe
CREATE TABLE `vehicles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `vendor_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `vehicle_type` ENUM('bus', 'train') NOT NULL,
    `license_plate` VARCHAR(50) NULL UNIQUE,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE
);

-- 9. Bảng `coaches`: Toa tàu hoặc chính chiếc xe bus
CREATE TABLE `coaches` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `vehicle_id` INT UNSIGNED NOT NULL,
    `identifier` VARCHAR(50) NOT NULL,
    `coach_type` ENUM('sleeper_vip', 'sleeper_regular', 'seat_soft', 'seat_hard', 'limousine') NOT NULL,
    `total_seats` INT NOT NULL,
    FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
    UNIQUE KEY `coaches_vehicle_identifier_unique` (`vehicle_id`, `identifier`)
);

-- 10. Bảng `seats`: Từng ghế vật lý của một `coach`
CREATE TABLE `seats` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `coach_id` INT UNSIGNED NOT NULL,
    `seat_number` VARCHAR(10) NOT NULL,
    FOREIGN KEY (`coach_id`) REFERENCES `coaches` (`id`) ON DELETE CASCADE,
    UNIQUE KEY `seats_coach_seat_number_unique` (`coach_id`, `seat_number`)
);

-- 11. Bảng `trips`: Một chuyến đi cụ thể
CREATE TABLE `trips` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `vendor_route_id` INT UNSIGNED NOT NULL,
    `departure_datetime` DATETIME NOT NULL,
    `arrival_datetime` DATETIME NOT NULL,
    `base_price` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('scheduled', 'ongoing', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`vendor_route_id`) REFERENCES `vendor_routes` (`id`) ON DELETE CASCADE
);

-- 12. Bảng `trip_coaches`: Gán các toa/xe cụ thể vào một chuyến đi
CREATE TABLE `trip_coaches` (
    `trip_id` INT UNSIGNED NOT NULL,
    `coach_id` INT UNSIGNED NOT NULL,
    `coach_order` INT NOT NULL DEFAULT 1,
    PRIMARY KEY (`trip_id`, `coach_id`),
    FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`coach_id`) REFERENCES `coaches` (`id`) ON DELETE CASCADE
);

-- 13. Bảng `trip_seats`: Kho vé cho mỗi chuyến đi
CREATE TABLE `trip_seats` (
    `trip_id` INT UNSIGNED NOT NULL,
    `seat_id` INT UNSIGNED NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('available', 'booked', 'locked', 'disabled') NOT NULL DEFAULT 'available',
    PRIMARY KEY (`trip_id`, `seat_id`),
    FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE CASCADE
);

-- 14. Bảng `trip_stops`: Các điểm dừng thực tế cho một chuyến đi
CREATE TABLE `trip_stops` (
    `trip_id` INT UNSIGNED NOT NULL,
    `stop_id` INT UNSIGNED NOT NULL,
    `stop_type` ENUM('pickup', 'dropoff') NOT NULL,
    `scheduled_time` TIME NOT NULL,
    PRIMARY KEY (`trip_id`, `stop_id`, `stop_type`),
    FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`stop_id`) REFERENCES `stops` (`id`) ON DELETE CASCADE
);

-- 15. Bảng `bookings`: Thông tin một lần đặt vé
CREATE TABLE `bookings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `booking_code` VARCHAR(50) NOT NULL UNIQUE,
    `total_price` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('pending', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- 16. Bảng `booking_details`: Chi tiết các vé trong một booking
CREATE TABLE `booking_details` (
    `booking_id` INT UNSIGNED NOT NULL,
    `trip_id` INT UNSIGNED NOT NULL,
    `seat_id` INT UNSIGNED NOT NULL,
    `pickup_stop_id` INT UNSIGNED NOT NULL,
    `dropoff_stop_id` INT UNSIGNED NOT NULL,
    `price_at_booking` DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (`booking_id`, `trip_id`, `seat_id`),
    FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`trip_id`, `seat_id`) REFERENCES `trip_seats` (`trip_id`, `seat_id`) ON DELETE CASCADE,
    FOREIGN KEY (`pickup_stop_id`) REFERENCES `stops` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`dropoff_stop_id`) REFERENCES `stops` (`id`) ON DELETE CASCADE
);

-- 17. Bảng `payments`: Thông tin thanh toán
CREATE TABLE `payments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `booking_id` INT UNSIGNED NOT NULL UNIQUE,
    `transaction_id` VARCHAR(255) NULL UNIQUE,
    `amount` DECIMAL(10, 2) NOT NULL,
    `payment_method` VARCHAR(50) NOT NULL,
    `status` ENUM('success', 'failed', 'pending') NOT NULL DEFAULT 'pending',
    `paid_at` TIMESTAMP NULL,
    FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
);

-- 18. Bảng `reviews`: Đánh giá của người dùng
CREATE TABLE `reviews` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `trip_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `booking_id` INT UNSIGNED NOT NULL UNIQUE,
    `rating` TINYINT UNSIGNED NOT NULL,
    `comment` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
);

-- 19. Bảng `blogs`: Các bài blog, tin tức
CREATE TABLE `blogs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NULL,
    `author_id` INT UNSIGNED NULL,
    `published_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
);

-- 20. Bảng `blog_trip`: Gắn các chuyến đi vào blog
CREATE TABLE `blog_trip` (
    `blog_id` INT UNSIGNED NOT NULL,
    `trip_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`blog_id`, `trip_id`),
    FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE
);

-- 21. Bảng `personal_access_tokens`: Bảng tiêu chuẩn của Laravel Sanctum
CREATE TABLE `personal_access_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `tokenable_type` VARCHAR(255) NOT NULL,
    `tokenable_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `token` VARCHAR(64) NOT NULL UNIQUE,
    `abilities` TEXT NULL,
    `last_used_at` TIMESTAMP NULL,
    `expires_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    INDEX `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
);