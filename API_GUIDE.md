# GoTicket API Guide

Bản hướng dẫn ngắn gọn dành cho developer khi làm việc với API của GoTicket. Tài liệu này chuẩn hoá cách gọi, tham số truy vấn, cấu trúc phản hồi, và các tuyến (endpoints) thường dùng cho Public, Vendor (nhà xe), và Admin.

## Tổng quan
- Base URL (mặc định local): `http://127.0.0.1:8000/api`
- Xác thực: Bearer Token (`Authorization: Bearer <token>`)
  - Nhóm Public không yêu cầu token cho một số endpoint public (trips, locations).
  - Nhóm Vendor/Admin yêu cầu `auth:api` và vai trò (role) tương ứng.
- Định dạng phản hồi chuẩn:
  - Thành công: `{ data: any }` hoặc `{ data: any, meta: {...} }` với danh sách phân trang
  - Lỗi: mã lỗi chuẩn hoá theo `ApiError` (vd: `TRIP_TIME_CONFLICT`, `FORBIDDEN`, ...)

## Phân trang & Lọc
- Phân trang (áp dụng cho danh sách):
  - Query: `per_page` (mặc định 10), `page` (mặc định 1)
  - Phản hồi: `meta.current_page`, `meta.last_page`, `meta.per_page`, `meta.total`
- Lọc phổ biến:
  - Trips (Vendor): `vehicle_type=bus|train`
  - Stops (Vendor): `transport_type=bus|train`

## Public API
- GET `/routes/location`
  - Lấy danh sách địa điểm/thành phố.
- GET `/trips/search`
  - Tìm kiếm chuyến (tham số theo đặc tả nghiệp vụ, tuỳ triển khai).
- GET `/trips/{trip}`
  - Lấy chi tiết chuyến công khai.
- GET `/trips/{trip}/stops`
  - Lấy danh sách điểm dừng của chuyến.

## Vendor API (cần `Authorization: Bearer <token>`, role vendor)

### Dashboard
- GET `/vendor/dashboard/stats`
- GET `/vendor/dashboard/info`
- POST `/vendor/dashboard/logo`

### Vehicles (Quản lý phương tiện)
- GET `/vendor/vehicles`
- GET `/vendor/vehicles/{vehicle}`
- POST `/vendor/vehicles`
- PUT `/vendor/vehicles/{vehicle}`
- DELETE `/vendor/vehicles/{vehicle}`
- POST `/vendor/vehicles/{vehicle}/coaches`
- DELETE `/vendor/vehicles/{vehicle}/coaches/{coach}`

### Stops (Điểm trung chuyển)
- GET `/vendor/stops` — danh sách (phân trang)
  - Query: `per_page`, `page`, `keyword`, `transport_type=bus|train`
- GET `/vendor/stops/by-location` — nhóm theo location
  - Query: `keyword`, `transport_type=bus|train`
- GET `/vendor/stops/location/{location}` — theo location cụ thể
  - Query: `keyword`
- GET `/vendor/stops/{stop}` — chi tiết một điểm trung chuyển
- POST `/vendor/stops`
  - Body: `name`, `address`, `location_id`, `transport_type=bus|train`
- PUT `/vendor/stops/{stop}`
  - Body: có thể cập nhật `name`, `address`, `location_id`, `transport_type`
- DELETE `/vendor/stops/{stop}`

### Trips (Chuyến của nhà xe)
- GET `/vendor/trips`
  - Query: `per_page`, `page`, `vehicle_type=bus|train`
  - Phản hồi mỗi item thường gồm: `id`, `departure_datetime`, `arrival_datetime`, `base_price`, `status`, thông tin `vehicle`, `route`, `coaches` và `empty_number` (số ghế trống).
- GET `/vendor/trips/{trip}` — chi tiết
- POST `/vendor/trips` — tạo chuyến (sử dụng khi cần tạo trực tiếp chuyến)
- PUT `/vendor/trips/{trip}` — cập nhật
- DELETE `/vendor/trips/{trip}` — huỷ chuyến (mặc định),
  - Có thể xoá cứng khi thêm `?hard=1`

Lưu ý tạo vé/chuyến: Hệ thống kiểm tra trùng thời gian theo vehicle (qua quan hệ coaches). Nếu thời gian mới chồng lấn chuyến đang tồn tại (không bị huỷ), API trả lỗi `TRIP_TIME_CONFLICT` (HTTP 409).

### Tickets (Khởi tạo vé từ vendor UI)
- POST `/vendor/tickets`
  - Body (tuỳ phương tiện):
    - Chung: `vehicle_id`, `start_time` (định dạng `HH:mm-HH:mm`), `start_date` (`YYYY-MM-DD`), `from_city`, `to_city`
    - Bus: `price`
    - Train: `regular_price`, `vip_price`
  - Ràng buộc: kiểm tra chồng lấn thời gian theo vehicle. Lỗi: `TRIP_TIME_CONFLICT`.
- DELETE `/vendor/tickets/{trip}`

### Bookings (Quản lý đơn đặt vé)
- GET `/vendor/bookings`
  - Query: `per_page`, `page`
- GET `/vendor/bookings/{booking}`
- PUT `/vendor/bookings/{booking}`
- DELETE `/vendor/bookings/{booking}`

## Admin API (tóm tắt)
- Người dùng, vendors, thống kê admin
- Ví dụ: `/api/admin/users`, `/api/admin/vendors/{vendor:user_id}`, `/api/admin/dashboard/stats`
- Yêu cầu role admin

## Mẫu phản hồi phân trang
```json
{
  "data": [ /* items */ ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

## Ví dụ cURL
- Lấy trips của vendor, lọc train, trang 2, 20 mục:
```bash
curl -H "Authorization: Bearer <TOKEN>" \
  "http://127.0.0.1:8000/api/vendor/trips?vehicle_type=train&page=2&per_page=20"
```
- Lấy stops by location cho bus:
```bash
curl -H "Authorization: Bearer <TOKEN>" \
  "http://127.0.0.1:8000/api/vendor/stops/by-location?transport_type=bus"
```
- Tạo ticket bus (vé/chuyến):
```bash
curl -X POST -H "Authorization: Bearer <TOKEN>" -H "Content-Type: application/json" \
  -d '{
    "vehicle_id": 12,
    "start_time": "08:00-10:30",
    "start_date": "2025-10-26",
    "from_city": "Hà Nội",
    "to_city": "Hải Phòng",
    "price": 120000
  }' \
  http://127.0.0.1:8000/api/vendor/tickets
```

## Quy ước khác
- Tiền tệ: VND, hiển thị phía client dùng `toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })`.
- Trạng thái ghế: `available`/khác (đặt, bán, giữ...). `empty_number` là tổng ghế còn trống.
- Xoá cứng chuyến: `DELETE /vendor/trips/{id}?hard=1` (thận trọng).

---
Gợi ý mở rộng: bổ sung bảng mã lỗi `ApiError` đầy đủ, sample schemas cho từng resource (Trip/Stop/Vehicle/Booking) và quy ước versioning (nếu có).