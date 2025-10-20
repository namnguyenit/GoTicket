<?php

namespace App\Repositories\Admin;
use Illuminate\Support\Collection;

interface DashboardAdminRepositoryInterface
{
    public function getTopVendorsByRevenue(int $limit): Collection;
    
    public function getOverallStats(): array;
}