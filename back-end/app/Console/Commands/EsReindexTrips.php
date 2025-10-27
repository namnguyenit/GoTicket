<?php

namespace App\Console\Commands;

use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;

class EsReindexTrips extends Command
{
    protected $signature = 'es:reindex-trips {--to-version=2}';
    protected $description = 'Reindex trips to a new versioned index with alias swap';

    public function __construct(private Client $es) { parent::__construct(); }

    public function handle(): int
    {
        $base = 'trips';
        $toVersion = (int)$this->option('to-version');
        $toIndex = sprintf('%s-v%d', $base, $toVersion);
        $this->info("Creating index {$toIndex}...");
        if (!$this->es->indices()->exists(['index' => $toIndex])->asBool()) {
            $this->es->indices()->create(['index' => $toIndex]);
        }

        $this->info('Reindex from write alias to new index...');
        $this->es->reindex([
            'body' => [
                'source' => ['index' => $base.'-write'],
                'dest' => ['index' => $toIndex],
                'conflicts' => 'proceed',
            ]
        ]);

        $this->info('Swapping aliases...');
        $actions = [
            ['remove' => ['index' => $base.'-*', 'alias' => $base.'-read']],
            ['remove' => ['index' => $base.'-*', 'alias' => $base.'-write']],
            ['add' => ['index' => $toIndex, 'alias' => $base.'-read']],
            ['add' => ['index' => $toIndex, 'alias' => $base.'-write']],
        ];
        $this->es->indices()->updateAliases(['body' => ['actions' => $actions]]);

        $this->info('Done.');
        return self::SUCCESS;
    }
}
