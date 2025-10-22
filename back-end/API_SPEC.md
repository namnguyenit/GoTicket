# GoTicket API Spec (Backend ↔ Frontend)

This document summarizes the main API endpoints, expected request payloads from the frontend, and sample JSON responses from the backend, based on the current codebase.

All responses use a common envelope via ResponseHelper:
- success: boolean
- status: number (HTTP code)
- message: string
- data: any (optional)

All date/times returned are formatted in ISO 8601 with microseconds in UTC, e.g. `2025-10-09T12:48:07.000000Z`.

---

## Auth

### POST /api/auth/register
Body (JSON):
```json
{
  "name": "Nguyen Van A",
  "phone": "0901234567",
  "email": "a@example.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

Sample success response (201):
```json
{
  "success": true,
  "status": 201,
  "message": "Tạo mới thành công",
  "data": {
    "user": {
      "id": 1,
      "name": "Nguyen Van A",
      "phone": "0901234567",
      "email": "a@example.com",
      "role": "customer",
      "created_at": "2025-10-09T12:48:07.000000Z"
    }
  }
}
```

### POST /api/auth/login
Body (JSON):
```json
{
  "email": "a@example.com",
  "password": "secret123"
}
```

Sample success response (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Thao tác thành công",
  "data": {
    "authorisation": {
      "token": "<JWT>",
      "type": "bearer"
    }
  }
}
```

### GET /api/auth/myinfo (auth:api)
Headers: Authorization: Bearer <JWT>

Sample success response (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": {
    "id": 1,
    "name": "Nguyen Van A",
    "phone": "0901234567",
    "email": "a@example.com",
    "role": "customer",
    "created_at": "2025-10-09T12:48:07.000000Z"
  }
}
```

---

## Public Trips & Locations

### GET /api/trips/search
Query params:
- origin_location: string (required)
- destination_location: string (required, different from origin)
- date: YYYY-MM-DD (required)
- vehicle_type: bus|train (required)
- price_min: number (optional)
- price_max: number (optional, > price_min)
- time_of_day: sang|chieu|toi (optional)
- page: number (optional)

Sample success response (200): Paginator with items shaped by TripResource
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": {
    "data": [
      {
        "id": 123,
        "trip": "Hà Nội - TP. Hồ Chí Minh",
        "imageLink": null,
        "pickTake": null,
        "departureDate": "2025-10-06T08:00:00.000000Z",
        "emptyNumber": 12,
        "vendorName": "Nhà xe ABC",
        "vendorType": "bus",
        "price": 300000
      }
    ],
    "links": {
      "first": "...",
      "last": "...",
      "prev": null,
      "next": "..."
    },
    "meta": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 12,
      "total": 60
    }
  }
}
```

Notes:
- imageLink, pickTake are placeholders for now.
- emptyNumber depends on trip_seats.status = 'available'.

### GET /api/routes/location
Sample success response (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": [
    {
      "id": 1,
      "name": "Hà Nội"
    },
    {
      "id": 2,
      "name": "TP. Hồ Chí Minh"
    }
  ]
}
```

### GET /api/trips/{id}
Sample success response (200): TripDetailResource
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": {
    "id": 123,
    "departure_datetime": "2025-10-06T08:00:00.000000Z",
    "arrival_datetime": "2025-10-06T15:00:00.000000Z",
    "vendor_name": "Nhà xe ABC",
    "coaches": [
      {
        "id": 10,
        "identifier": "Coach-01",
        "coach_type": "sleeper",
        "total_seats": 40,
        "seats": [
          {
            "id": 101,
            "seat_number": "A1",
            "status": "available",
            "price": 150000
          }
        ]
      }
    ]
  }
}
```

### GET /api/trips/{id}/stops (auth:api)
Sample success response (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": {
    "pickup_points": [
      {
        "id": 5,
        "name": "Bến A",
        "address": "..."
      }
    ],
    "dropoff_points": [
      {
        "id": 12,
        "name": "Bến B",
        "address": "..."
      }
    ]
  }
}
```

---

## Bookings (auth:api)

### POST /api/bookings/initiate
Body (JSON):
```json
{
  "trip_id": 123,
  "seat_ids": [
    101,
    102
  ]
}
```

Sample success response (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": {
    "user_info": {
      "name": "Nguyen Van A",
      "email": "a@example.com",
      "phone_number": "090..."
    },
    "trip_info": {
      "id": 123,
      "vendor_name": "Nhà xe ABC",
      "departure_datetime": "2025-10-06T08:00:00.000000Z",
      "arrival_datetime": "2025-10-06T15:00:00.000000Z"
    },
    "booking_details": {
      "selected_seats": [
        {
          "id": 101,
          "seat_number": "A1",
          "price": 150000
        },
        {
          "id": 102,
          "seat_number": "A2",
          "price": 150000
        }
      ],
      "total_price": 300000
    }
  }
}
```

### POST /api/bookings/confirm
Body (JSON):
```json
{
  "trip_id": 123,
  "seat_ids": [
    101,
    102
  ],
  "pickup_stop_id": 5,
  "dropoff_stop_id": 12
}
```

Sample success response (201):
```json
{
  "success": true,
  "status": 201,
  "message": "Tạo mới thành công",
  "data": {
    "booking_code": "BK20251009123456",
    "message": "Đặt vé thành công!"
  }
}
```

---

## Vendor (auth:api + role:vendor)

### GET /api/vendor/dashboard/stats
Sample success response (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": {
    "weekly_revenue_by_day": {
      "Mon": 0,
      "Tue": 0,
      "Wed": 0,
      "Thu": 0,
      "Fri": 0,
      "Sat": 0,
      "Sun": 0
    },
    "yearly_revenue_by_month": {
      "Jan": 0,
      "Feb": 0,
      "Mar": 0,
      "Apr": 0,
      "May": 0,
      "Jun": 0,
      "Jul": 0,
      "Aug": 0,
      "Sep": 0,
      "Oct": 0,
      "Nov": 0,
      "Dec": 0
    }
  }
}
```

### Vendor Stops (điểm dừng)

Tất cả endpoint đều yêu cầu Header: `Authorization: Bearer <JWT>` và role `vendor`.

#### POST /api/vendor/stops
Body (JSON):
```json
{
  "name": "Bến xe Miền Đông",
  "address": "292 Đinh Bộ Lĩnh, Bình Thạnh, TP.HCM",
  "location_id": 1
}
```

Sample success (201):
```json
{
  "success": true,
  "status": 201,
  "message": "Tạo mới thành công",
  "data": {
    "id": 10,
    "name": "Bến xe Miền Đông",
    "address": "292 Đinh Bộ Lĩnh, Bình Thạnh, TP.HCM",
    "location_id": 1,
    "vendor_id": 3
  }
}
```

Validation error (422):
```json
{
  "success": false,
  "status": 422,
  "error_code": "VALIDATION_FAILED",
  "message": "Yêu cầu không hợp lệ",
  "errors": {
    "name": ["Tên điểm dừng là bắt buộc."],
    "address": ["Địa chỉ là bắt buộc."],
    "location_id": ["Vui lòng chọn tỉnh/thành phố."]
  }
}
```

#### GET /api/vendor/stops
Query (tùy chọn): `page`, `per_page`, `keyword`

Sample success (200) — có thể trả về danh sách dạng mảng hoặc phân trang tùy implementation:
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": [
    { "id": 10, "name": "Bến xe Miền Đông", "address": "...", "location_id": 1, "vendor_id": 3 }
  ]
}
```

#### GET /api/vendor/stops/{id}
Sample success (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": {
    "id": 10,
    "name": "Bến xe Miền Đông",
    "address": "292 Đinh Bộ Lĩnh, Bình Thạnh, TP.HCM",
    "location_id": 1,
    "vendor_id": 3
  }
}
```

#### GET /api/vendor/stops/location/{location}
Trả về toàn bộ điểm dừng của nhà xe theo một location cụ thể (không phân trang).

Sample success (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": [
    { "id": 10, "name": "Bến A", "address": "...", "location_id": 1, "vendor_id": 3 },
    { "id": 12, "name": "Bến B", "address": "...", "location_id": 1, "vendor_id": 3 }
  ]
}
```

#### PUT /api/vendor/stops/{id}
Body (JSON) — cập nhật một phần hoặc toàn bộ:
```json
{
  "name": "Bến xe Miền Đông (mới)",
  "address": "...",
  "location_id": 2
}
```

Sample success (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Cập nhật thành công",
  "data": {
    "id": 10,
    "name": "Bến xe Miền Đông (mới)",
    "address": "...",
    "location_id": 2,
    "vendor_id": 3
  }
}
```

#### DELETE /api/vendor/stops/{id}
Sample success (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Xóa điểm dừng thành công"
}
```

### Vendor Vehicles (phương tiện, coach/seat tự sinh theo loại)

Tất cả endpoint đều yêu cầu Header: `Authorization: Bearer <JWT>` và role `vendor`.

#### POST /api/vendor/vehicles
Body (JSON) — tùy theo `vehicle_type`:

Xe bus:
```json
{
  "name": "Xe ABC",
  "vehicle_type": "bus",
  "license_plate": "51A-123.45",
  "coach": {
    "coach_type": "sleeper_vip",
    "total_seats": 34
  }
}
```

Tàu hỏa:
```json
{
  "name": "Tàu SE01",
  "vehicle_type": "train",
  "license_plate": null,
  "coaches": [
    { "coach_type": "seat_soft", "total_seats": 56, "quantity": 2 },
    { "coach_type": "seat_VIP",  "total_seats": 40, "quantity": 1 }
  ]
}
```

Sample success (201):
```json
{
  "success": true,
  "status": 201,
  "message": "Tạo mới thành công",
  "data": {
    "id": 7,
    "name": "Xe ABC",
    "license_plate": "51A-123.45",
    "vehicle_type": "bus",
    "capacity": 34,
    "created_at": "2025-10-22T07:15:00.000000Z"
  }
}
```

Validation error (422) ví dụ:
```json
{
  "success": false,
  "status": 422,
  "error_code": "VALIDATION_FAILED",
  "message": "Yêu cầu không hợp lệ",
  "errors": {
    "name": ["Tên phương tiện là bắt buộc."],
    "vehicle_type": ["Loại phương tiện là bắt buộc."],
    "coach.total_seats": ["Trường này là bắt buộc khi vehicle_type=bus."],
    "coaches": ["Trường này là bắt buộc khi vehicle_type=train."]
  }
}
```

#### GET /api/vendor/vehicles
Query (tùy chọn): `page` (mặc định 1)

Sample success (200) — phân trang:
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": {
    "data": [
      { "id": 7, "name": "Xe ABC", "license_plate": "51A-123.45", "vehicle_type": "bus", "capacity": 34, "created_at": "2025-10-22T07:15:00.000000Z" }
    ],
    "links": { "first": "...", "last": "...", "prev": null, "next": null },
    "meta": { "current_page": 1, "last_page": 1, "per_page": 10, "total": 1 }
  }
}
```

#### GET /api/vendor/vehicles/{id}
Sample success (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": {
    "id": 7,
    "name": "Xe ABC",
    "license_plate": "51A-123.45",
    "vehicle_type": "bus",
    "capacity": 34,
    "created_at": "2025-10-22T07:15:00.000000Z"
  }
}
```

#### PUT /api/vendor/vehicles/{id}
Body (JSON):
```json
{
  "name": "Xe ABC (mới)",
  "vehicle_type": "bus",
  "license_plate": "51A-999.99"
}
```

Sample success (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Cập nhật thành công",
  "data": {
    "id": 7,
    "name": "Xe ABC (mới)",
    "license_plate": "51A-999.99",
    "vehicle_type": "bus",
    "capacity": 34,
    "created_at": "2025-10-22T07:15:00.000000Z"
  }
}
```

#### DELETE /api/vendor/vehicles/{id}
Sample success (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Xóa phương tiện thành công"
}
```

### Vendor Trips (CRUD chuyến đi)

Tất cả endpoint yêu cầu Header: `Authorization: Bearer <JWT>` và role `vendor`. Mọi thao tác đều kiểm tra quyền sở hữu (trip thuộc `vendor_route` của vendor hiện tại). Khi gửi `stops`, mỗi phần tử cần đủ `stop_id`, `stop_type` (pickup|dropoff), `scheduled_time` (ISO time).

#### POST /api/vendor/trips
Body (JSON):
```json
{
  "vendor_route_id": 5,
  "departure_datetime": "2025-11-01T08:00:00Z",
  "arrival_datetime": "2025-11-01T14:00:00Z",
  "base_price": 350000,
  "status": "scheduled",
  "stops": [
    { "stop_id": 10, "stop_type": "pickup", "scheduled_time": "2025-11-01T07:30:00Z" },
    { "stop_id": 12, "stop_type": "dropoff", "scheduled_time": "2025-11-01T14:15:00Z" }
  ]
}
```

Sample success (201):
```json
{
  "success": true,
  "status": 201,
  "message": "Tạo mới thành công",
  "data": {
    "id": 123,
    "vendor_route_id": 5,
    "departure_datetime": "2025-11-01T08:00:00.000Z",
    "arrival_datetime": "2025-11-01T14:00:00.000Z",
    "base_price": 350000,
    "status": "scheduled",
    "stops": [
      {
        "id": 10,
        "name": "Bến A",
        "address": "...",
        "location_id": 1,
        "pivot": {
          "stop_type": "pickup",
          "scheduled_time": "2025-11-01T07:30:00.000Z"
        }
      },
      {
        "id": 12,
        "name": "Bến B",
        "address": "...",
        "location_id": 2,
        "pivot": {
          "stop_type": "dropoff",
          "scheduled_time": "2025-11-01T14:15:00.000Z"
        }
      }
    ]
  }
}
```

Validation error (422) ví dụ:
```json
{
  "success": false,
  "status": 422,
  "error_code": "VALIDATION_FAILED",
  "message": "Yêu cầu không hợp lệ",
  "errors": {
    "vendor_route_id": ["Trường này là bắt buộc."],
    "departure_datetime": ["Trường này là bắt buộc."],
    "arrival_datetime": ["Phải lớn hơn departure_datetime."],
    "base_price": ["Giá không hợp lệ."],
    "stops.0.stop_id": ["Điểm dừng không hợp lệ."]
  }
}
```

#### GET /api/vendor/trips
Query: `per_page` (mặc định 10)

Sample success (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": {
    "data": [
      {
        "id": 123,
        "vendor_route_id": 5,
        "departure_datetime": "2025-11-01T08:00:00.000Z",
        "arrival_datetime": "2025-11-01T14:00:00.000Z",
        "base_price": 350000,
        "status": "scheduled"
      }
    ],
    "meta": { "current_page": 1, "last_page": 1, "per_page": 10, "total": 1 }
  }
}
```

#### GET /api/vendor/trips/{id}
Sample success (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Lấy dữ liệu thành công",
  "data": {
    "id": 123,
    "vendor_route_id": 5,
    "departure_datetime": "2025-11-01T08:00:00.000Z",
    "arrival_datetime": "2025-11-01T14:00:00.000Z",
    "base_price": 350000,
    "status": "scheduled",
    "stops": [
      {
        "id": 10,
        "name": "Bến A",
        "address": "...",
        "location_id": 1,
        "pivot": {
          "stop_type": "pickup",
          "scheduled_time": "2025-11-01T07:30:00.000Z"
        }
      }
    ]
  }
}
```

#### PUT /api/vendor/trips/{id}
Body (JSON) — cập nhật một phần hoặc toàn bộ:
```json
{
  "departure_datetime": "2025-11-01T09:00:00Z",
  "base_price": 380000,
  "stops": [
    { "stop_id": 10, "stop_type": "pickup", "scheduled_time": "2025-11-01T08:30:00Z" },
    { "stop_id": 12, "stop_type": "dropoff", "scheduled_time": "2025-11-01T14:30:00Z" }
  ]
}
```

Sample success (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Cập nhật thành công",
  "data": {
    "id": 123,
    "vendor_route_id": 5,
    "departure_datetime": "2025-11-01T09:00:00.000Z",
    "arrival_datetime": "2025-11-01T14:00:00.000Z",
    "base_price": 380000,
    "status": "scheduled",
    "stops": [
      {
        "id": 10,
        "name": "Bến A",
        "address": "...",
        "location_id": 1,
        "pivot": {
          "stop_type": "pickup",
          "scheduled_time": "2025-11-01T08:30:00.000Z"
        }
      },
      {
        "id": 12,
        "name": "Bến B",
        "address": "...",
        "location_id": 2,
        "pivot": {
          "stop_type": "dropoff",
          "scheduled_time": "2025-11-01T14:30:00.000Z"
        }
      }
    ]
  }
}
```

#### DELETE /api/vendor/trips/{id}
Sample success (200):
```json
{
  "success": true,
  "status": 200,
  "message": "Huỷ chuyến đi thành công."
}
```

---

## Envelope error samples

Validation error:
```json
{
  "success": false,
  "status": 422,
  "error_code": "VALIDATION_FAILED",
  "message": "Yêu cầu không hợp lệ",
  "errors": {
    "field_name": [
      "Error message..."
    ]
  }
}
```

Not found:
```json
{
  "success": false,
  "status": 404,
  "error_code": "NOT_FOUND",
  "message": "Không tìm thấy tài nguyên"
}
```

Unauthorized:
```json
{
  "success": false,
  "status": 401,
  "error_code": "UNAUTHORIZED",
  "message": "Chưa xác thực"
}
```
