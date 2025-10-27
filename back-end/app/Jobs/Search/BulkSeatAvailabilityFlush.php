<?php

namespace App\Jobs\Search;

use App\Models\Trips;
use Elastic\Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BulkSeatAvailabilityFlush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    public function __construct(private array $tripIds) {}

    public function handle(Client $es): void
    {
        $tripIds = array_values(array_unique(array_map('intval', $this->tripIds)));
        if (!$tripIds) return;

        $trips = Trips::query()
            ->whereIn('id', $tripIds)
            ->withCount(['seats as seats_available' => function($q){ $q->where('trip_seats.status','available'); }])
            ->get(['id']);

        $ops = [];
        foreach ($trips as $t) {
            $ops[] = ['update' => ['_index' => 'trips-write', '_id' => (string)$t->id]];
            $ops[] = ['doc' => ['seats_available' => (int)$t->seats_available], 'doc_as_upsert' => true];
        }
        if ($ops) {
            $resp = $es->bulk(['body' => $ops, 'refresh' => false]);
            if (($resp['errors'] ?? false) === true) {
                // Optionally log per-item errors
                \Log::warning('ES bulk seat availability partial errors', ['resp' => $resp]);
            }
        }
    }
}
