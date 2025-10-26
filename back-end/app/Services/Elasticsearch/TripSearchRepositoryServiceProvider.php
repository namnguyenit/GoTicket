<?php

namespace App\Services\Elasticsearch;

use Illuminate\Support\ServiceProvider;
use App\Repositories\TripRepositoryInterface;
use App\Repositories\TripRepository;
use App\Repositories\TripSearchRepository;

class TripSearchRepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TripRepositoryInterface::class, function(){
            $driver = env('SEARCH_DRIVER', 'db');
            if ($driver === 'elasticsearch') {
                return app(TripSearchRepository::class);
            }
            return app(TripRepository::class);
        });
    }
}
