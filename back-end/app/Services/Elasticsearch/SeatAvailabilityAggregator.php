<?php

namespace App\Services\Elasticsearch;

use Illuminate\Support\Facades\Cache;
use App\Jobs\Search\BulkSeatAvailabilityFlush;

class SeatAvailabilityAggregator
{
    private string $key = 'seat_avail_agg';
    private int $debounceSeconds = 3;

    public function push(int $tripId): void
    {
        $now = time();
        $ids = Cache::get($this->key, []);
        $ids[$tripId] = $now; // override timestamp
        Cache::put($this->key, $ids, $this->debounceSeconds + 5);
        // schedule flush after debounceSeconds using queue with delay
        BulkSeatAvailabilityFlush::dispatch(array_keys($ids))->delay(now()->addSeconds($this->debounceSeconds));
    }
}
