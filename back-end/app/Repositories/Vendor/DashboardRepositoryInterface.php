<?php

namespace App\Repositories\Vendor;
use Illuminate\Support\Collection;
interface DashboardRepositoryInterface
{
    
    
    public function getRevenueGroupedByPeriod(int $vendorId, \Carbon\Carbon $startDate, \Carbon\Carbon $endDate, string $period): Collection;
}