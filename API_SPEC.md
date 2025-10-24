# GoTicket API Specification

This document consolidates the backend HTTP API exposed under the `/api` prefix, including endpoint paths, HTTP methods, required headers, request bodies, and sample JSON responses. It reflects the current code in `back-end/routes/api.php` and controllers under `back-end/app/Http/Controllers`.

All responses return a consistent envelope:
- `success`: boolean
- `status`: number (HTTP status code)
- `message`: string (localized)
- `data`: any (optional)

Date/times use ISO 8601 in UTC with microseconds, e.g. `2025-10-09T12:48:07.000000Z`.

Auth for protected endpoints: `Authorization: Bearer <JWT>` with guard `auth:api` and, where required, role middleware `role:admin` or `role:vendor`.

Pagination standard: when listing, the `data` field contains an object
- `data`: array of resources
- `links`: `{ first, last, prev, next }`
- `meta`: `{ current_page, last_page, per_page, total }`

---

## Auth

POST /api/auth/register
- Body (JSON):
  {
    "name": "Nguyen Van A",
    "phone": "0901234567",
    "email": "a@example.com",
    "password": "secret123",
    "password_confirmation": "secret123"
  }
- Sample 201:
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

POST /api/auth/login
- Body (JSON):
  { "email": "a@example.com", "password": "secret123" }
- Sample 200:
  {
    "success": true,
    "status": 200,
    "message": "Thao tác thành công",
    "data": { "authorisation": { "token": "<JWT>", "type": "bearer" } }
  }

GET /api/auth/myinfo (auth:api)
- Headers: Authorization: Bearer <JWT>
- Sample 200:
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

PUT /api/auth/myinfo (auth:api)
- Body (JSON):
  { "name": "Tên mới", "phone_number": "0912345678", "current_password": "123456", "password": "123456", "password_confirmation": "123456" }
- Sample 200: same envelope, `data` as updated user resource

POST /api/auth/logout (auth:api)
- Sample 200: standard envelope with `success=true`, message indicates logout

---

## Public Trips & Locations

GET /api/trips/search
- Query params:
  - `origin_location`: string (required)
  - `destination_location`: string (required; different from origin)
  - `date`: `YYYY-MM-DD` (required)
  - `vehicle_type`: `bus|train` (required)
  - `price_min`: number (optional)
  - `price_max`: number (optional; > price_min)
  - `time_of_day`: `sang|chieu|toi` (optional)
  - `per_page`: integer [1..50] (optional; default 12)
  - `page`: number (optional; default 1)
- Notes:
  - Always returns a paginator object in `data` containing `data`, `links`, `meta`.
  - When `vehicle_type=train`, each trip includes `coaches` with per-coach seats (seat list is preloaded in search results).
- Sample 200 (bus):
  {
    "success": true,
    "status": 200,
    "message": "Lấy dữ liệu thành công",
    "data": {
      "data": [
        {
          "id": 123,
          "trip": "Hà Nội - Nghệ An",
          "imageLink": null,
          "pickTake": null,
          "departureDate": "2025-10-30T01:00:00.000000Z",
          "emptyNumber": 12,
          "vendorName": "Nhà xe ABC",
          "vendorType": "bus",
          "price": 300000
        }
      ],
      "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
      "meta": { "current_page": 1, "last_page": 5, "per_page": 12, "total": 60 }
    }
  }
- Sample 200 (train with coaches):
  {
    "success": true,
    "status": 200,
    "message": "Lấy dữ liệu thành công",
    "data": {
      "data": [
        {
          "id": 456,
          "trip": "Hà Nội - Nghệ An",
          "imageLink": null,
          "pickTake": null,
          "departureDate": "2025-10-30T03:30:00.000000Z",
          "emptyNumber": 40,
          "vendorName": "Đường sắt XYZ",
          "vendorType": "train",
          "coaches": [
            { "id": 10, "identifier": "VIP", "coach_type": "seat_VIP", "total_seats": 24, "seats": [ { "id": 101, "seat_number": "V1" } ] },
            { "id": 11, "identifier": "SOFT", "coach_type": "seat_soft", "total_seats": 40, "seats": [ { "id": 201, "seat_number": "S1" } ] }
          ],
          "price": 420000
        }
      ],
      "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
      "meta": { "current_page": 1, "last_page": 5, "per_page": 12, "total": 60 }
    }
  }

GET /api/routes/location
- Sample 200:
  {
    "success": true,
    "status": 200,
    "message": "Lấy dữ liệu thành công",
    "data": [ { "id": 1, "name": "Hà Nội" }, { "id": 2, "name": "TP. Hồ Chí Minh" } ]
  }

GET /api/trips/{id}
- Sample 200 (TripDetailResource):
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
          "seats": [ { "id": 101, "seat_number": "A1", "status": "available", "price": 150000 } ]
        }
      ]
    }
  }

GET /api/trips/{id}/stops
- Notes: also exposed inside auth group; returns available pickup/dropoff points (falls back to vendor route template when needed)
- Sample 200:
  {
    "success": true,
    "status": 200,
    "message": "Lấy dữ liệu thành công",
    "data": {
      "pickup_points": [ { "id": 5, "name": "Bến A", "address": "..." } ],
      "dropoff_points": [ { "id": 12, "name": "Bến B", "address": "..." } ]
    }
  }

---

## Bookings (auth:api)

POST /api/bookings/initiate
- Body (JSON): { "trip_id": 123, "seat_ids": [101, 102] }
- Sample 200:
  {
    "success": true,
    "status": 200,
    "message": "Lấy dữ liệu thành công",
    "data": {
      "user_info": { "name": "Nguyen Van A", "email": "a@example.com", "phone_number": "090..." },
      "trip_info": { "id": 123, "vendor_name": "Nhà xe ABC", "departure_datetime": "2025-10-06T08:00:00.000000Z", "arrival_datetime": "2025-10-06T15:00:00.000000Z" },
      "booking_details": {
        "selected_seats": [ { "id": 101, "seat_number": "A1", "price": 150000 }, { "id": 102, "seat_number": "A2", "price": 150000 } ],
        "total_price": 300000
      }
    }
  }

POST /api/bookings/confirm
- Body (JSON): { "trip_id": 123, "seat_ids": [101, 102], "pickup_stop_id": 5, "dropoff_stop_id": 12 }
- Sample 201: { "success": true, "status": 201, "message": "Tạo mới thành công", "data": { "booking_code": "BK20251009123456", "message": "Đặt vé thành công!" } }

---

## Admin APIs (auth:api + role:admin)

GET /api/admin/dashboard/top-vendors
- Query: `limit` (optional, 1–100; default 5)
- Sample 200: standard envelope; `data` is list per DashboardAdminService

GET /api/admin/dashboard/stats
- Sample 200: standard envelope; `data` object from DashboardAdminService

User management
- Pattern A (by email)
  - GET /api/admin/users — list users, optional `role=customer|vendor|admin`
  - GET /api/admin/users/search?name=...&role=customer|vendor
  - GET /api/admin/users/{email}
  - PUT /api/admin/users/{email}
    - Body (JSON): { "name": string, "phone_number": string(10), "role": "customer|vendor|admin", "company_name"?: string, "address"?: string }
    - Sample 200: updated `UserResource` in `data`
  - DELETE /api/admin/users/{email}

- Pattern B (grouped, by numeric id)
  - GET /api/admin/users/
  - GET /api/admin/users/search?name=...
  - GET /api/admin/users/{user}
  - PUT /api/admin/users/{user}
  - DELETE /api/admin/users/{user}

Vendor management
- POST /api/admin/vendors
  - Body: per `CreateVendorRequest` (company/user fields)
  - Sample 201: { success, status:201, message:"Tạo mới thành công", data: { user_id, vendor_id, message } }
- GET /api/admin/vendors/{vendor:user_id}
  - Sample 200: `VendorDetailResource` in `data`
- PUT /api/admin/vendors/{vendor:user_id}
  - Body: { company_name: string, address?: string, user_name: string, phone_number: string(10), status: active|pending|suspended, role: customer|vendor|admin }
  - Sample 200: success envelope
- PUT /api/admin/vendors/{vendor:user_id}/status
  - Body: { status: active|pending|suspended }
  - Sample 200: success envelope

---

## Vendor APIs (auth:api + role:vendor)

Dashboard
- GET /api/vendor/dashboard/stats — statistics object
- GET /api/vendor/dashboard/info — returns `VendorResource`
- POST /api/vendor/dashboard/logo — multipart/form-data upload vendor logo

Vehicles
- POST /api/vendor/vehicles — create a vehicle (bus/train)
- GET /api/vendor/vehicles — paginator (query: `page`, optional `per_page` default 10)
- GET /api/vendor/vehicles/{vehicle} — `VehicleResource` (with `coaches` when present)
- PUT /api/vendor/vehicles/{vehicle} — update fields
- DELETE /api/vendor/vehicles/{vehicle}
- POST /api/vendor/vehicles/{vehicle}/coaches — add coaches (train)
- DELETE /api/vendor/vehicles/{vehicle}/coaches/{coach}

Stops
- POST /api/vendor/stops — create stop
- GET /api/vendor/stops — paginator (query: `page`, optional `per_page` default 10, `keyword`)
- GET /api/vendor/stops/by-location — grouped by location (optional `keyword`)
- GET /api/vendor/stops/location/{location} — list stops for a location
- GET /api/vendor/stops/{stop}
- PUT /api/vendor/stops/{stop}
- DELETE /api/vendor/stops/{stop}

Trips
- POST /api/vendor/trips — create trip with stops
- GET /api/vendor/trips — paginator (query: `per_page` default 10)
- GET /api/vendor/trips/{trip}
- PUT /api/vendor/trips/{trip}
- DELETE /api/vendor/trips/{trip}

Tickets
- POST /api/vendor/tickets — create tickets for a trip
- DELETE /api/vendor/tickets/{trip} — hard delete trip and tickets

Vendor Bookings
- GET /api/vendor/bookings — paginator
- GET /api/vendor/bookings/{booking} — booking detail

---

## Error Envelopes

Validation error (422):
{
  "success": false,
  "status": 422,
  "error_code": "VALIDATION_FAILED",
  "message": "Yêu cầu không hợp lệ",
  "errors": { "field_name": ["Error message..."] }
}

Not found (404):
{
  "success": false,
  "status": 404,
  "error_code": "NOT_FOUND",
  "message": "Không tìm thấy tài nguyên"
}

Unauthorized (401):
{
  "success": false,
  "status": 401,
  "error_code": "UNAUTHORIZED",
  "message": "Chưa xác thực"
}

Forbidden (403):
{
  "success": false,
  "status": 403,
  "error_code": "FORBIDDEN",
  "message": "Không có quyền truy cập"
}

Server error (500):
{
  "success": false,
  "status": 500,
  "error_code": "SERVER_ERROR",
  "message": "Đã xảy ra lỗi hệ thống"
}

---

## Notes
- Public search now accepts `per_page` and always returns paginator object in `data`.
- When `vehicle_type=train`, search includes `coaches` and per-coach `seats` in each trip item.
- Pagination responses commonly include `{ data: [...], meta: { current_page, last_page, per_page, total }, links: {...} }`.
- Several vendor endpoints validate ownership and return `FORBIDDEN` if the resource does not belong to the authenticated vendor.
- All examples reflect current implementation and may evolve with services/resources.
