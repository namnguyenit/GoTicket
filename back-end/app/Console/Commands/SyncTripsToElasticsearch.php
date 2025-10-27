<?php

namespace App\Console\Commands;

use App\Models\Trips;
use App\Services\Elasticsearch\TripIndexer;
use Illuminate\Console\Command;

class SyncTripsToElasticsearch extends Command
{
    protected $signature = 'es:sync-trips {--chunk=500} {--recreate}';
    protected $description = 'Sync trips data from MySQL to Elasticsearch';

    public function __construct(private TripIndexer $indexer)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $chunk = (int)$this->option('chunk');
        $recreate = (bool)$this->option('recreate');

        if ($recreate) {
            $this->call('es:init', ["--recreate" => true]);
        } else {
            $this->call('es:init');
        }

        $this->info('Syncing trips to Elasticsearch...');

        $query = Trips::query()
            ->with([
                'vendorRoute.route.origin:id,name',
                'vendorRoute.route.destination:id,name',
                'coaches:id,vehicle_id,coach_type,total_seats',
                'coaches.vehicle:id,vehicle_type',
            ])
            ->orderBy('id');

        $bar = $this->output->createProgressBar($query->count());
        $bar->start();

        $lastId = 0;
        do {
            $batch = (clone $query)->where('id', '>', $lastId)->limit($chunk)->get();
            if ($batch->isEmpty()) break;

            $ops = [];
            foreach ($batch as $trip) {
                $doc = $this->indexer->toDocument($trip);
                $ops[] = ['index' => ['_index' => 'trips-write', '_id' => (string)$trip->id]];
                $ops[] = $doc;
                $lastId = $trip->id;
            }
            if ($ops) {
                app('Elastic\\Elasticsearch\\Client')->bulk(['body' => $ops, 'refresh' => false]);
            }
            $bar->advance($batch->count());
        } while (true);

        $bar->finish();
        $this->newLine();
        $this->info('Sync completed.');
        return self::SUCCESS;
    }
}
