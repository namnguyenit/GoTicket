<?php

namespace App\Console\Commands;

use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;

class InitElasticsearch extends Command
{
    protected $signature = 'es:init {--recreate : Drop and recreate indices}';
    protected $description = 'Initialize Elasticsearch templates and indices for GoTicket';

    public function __construct(private Client $es)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Initializing Elasticsearch...');

        $template = [
            'index_patterns' => ['trips-*'],
            'template' => [
                'settings' => [
                    'number_of_shards' => 2,
                    'number_of_replicas' => 1,
                    'refresh_interval' => '5s',
                    'analysis' => [
                        'analyzer' => [
                            'vi_normalized' => [
                                'tokenizer' => 'standard',
                                'filter' => ['lowercase','asciifolding']
                            ],
                        ],
                        'normalizer' => [
                            'lowercase_normalizer' => [
                                'type' => 'custom',
                                'filter' => ['lowercase']
                            ]
                        ],
                    ],
                ],
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'keyword'],
                        'route' => ['type' => 'text', 'analyzer' => 'vi_normalized', 'fields' => ['keyword' => ['type' => 'keyword']]],
                        'from' => ['type' => 'text', 'analyzer' => 'vi_normalized', 'fields' => ['keyword' => ['type' => 'keyword']]],
                        'to' => ['type' => 'text', 'analyzer' => 'vi_normalized', 'fields' => ['keyword' => ['type' => 'keyword']]],
                        'origin_id' => ['type' => 'keyword'],
                        'destination_id' => ['type' => 'keyword'],
                        'vehicle_type' => ['type' => 'keyword'],
                        'departure_time' => ['type' => 'date'],
                        'departure_date' => ['type' => 'date'],
                        'departure_hour' => ['type' => 'short'],
                        'arrival_time' => ['type' => 'date'],
                        'price' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                        'vendor_id' => ['type' => 'keyword'],
                        'seats_available' => ['type' => 'integer'],
                        'bus_coach_types' => ['type' => 'keyword'],
                    ],
                ],
            ],
        ];

        // Create or update index template using official client
        $this->es->indices()->putIndexTemplate([
            'name' => 'trips_template',
            'body' => $template,
        ]);
        $this->info('Template trips_template created/updated');

        // Create versioned index and aliases
        $base = 'trips';
        $version = 1;
        $index = sprintf('%s-v%d', $base, $version);

        if ($this->option('recreate')) {
            try { $this->es->indices()->delete(['index' => $index]); } catch (\Throwable $e) {}
            try { $this->es->indices()->deleteAlias(['index' => $base.'-*', 'name' => $base.'-read']); } catch (\Throwable $e) {}
            try { $this->es->indices()->deleteAlias(['index' => $base.'-*', 'name' => $base.'-write']); } catch (\Throwable $e) {}
        }
        try {
            if (!$this->es->indices()->exists(['index' => $index])->asBool()) {
                $this->es->indices()->create(['index' => $index]);
                $this->info("Index {$index} created");
            } else {
                $this->info("Index {$index} already exists");
            }

            // Ensure aliases exist and point to current version
            $this->es->indices()->putAlias(['index' => $index, 'name' => $base.'-read']);
            $this->es->indices()->putAlias(['index' => $index, 'name' => $base.'-write']);
        } catch (\Throwable $e) {
            $this->error('Error creating index/aliases: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->info('Elasticsearch initialization completed.');
        return self::SUCCESS;
    }
}
