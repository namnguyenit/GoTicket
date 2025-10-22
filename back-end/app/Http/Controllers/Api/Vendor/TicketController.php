<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiSuccess;
use App\Enums\ApiError;
use App\Http\Requests\Api\Vendor\CreateTicketRequest;
use App\Services\Vendor\TicketService;
use App\Http\Resources\Vendor\TripResource;

class TicketController extends Controller
{
    use ResponseHelper;

    public function __construct(private TicketService $service)
    {
    }

    public function store(CreateTicketRequest $request)
    {
        $validated = $request->validated();
        try {
            $trip = $this->service->createTicket($validated);
            return $this->success(new TripResource($trip), ApiSuccess::TRIP_CREATED);
        } catch (\RuntimeException $e) {
            $code = $e->getMessage();
            return match ($code) {
                'VENDOR_NOT_ASSOCIATED' => $this->error(ApiError::VENDOR_NOT_ASSOCIATED),
                'VEHICLE_NOT_FOUND' => $this->error(ApiError::VEHICLE_NOT_FOUND),
                'ROUTE_NOT_FOUND' => $this->error(ApiError::NOT_FOUND, ['route' => 'Không tìm thấy tuyến cho cặp thành phố.']),
                'VENDOR_ROUTE_NOT_FOUND' => $this->error(ApiError::VENDOR_ROUTE_NOT_FOUND),
                default => $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null),
            };
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }
}
