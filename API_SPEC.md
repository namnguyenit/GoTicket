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

### PUT /api/auth/myinfo

```json
{
    "name": "Tên Mới",
    "email": "email.moi@example.com",
    "current_password": "123456",
    "password": "1234567",
    "password_confirmation": "1234567"
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

### GET /api/vendor/Tongquan/stats
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

