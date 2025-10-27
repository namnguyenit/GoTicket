<?php

namespace App\Services\Elasticsearch;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client;

class ElasticsearchConfig
{
    public static function buildClient(array $hosts): Client
    {
        return ClientBuilder::create()
            ->setHosts($hosts)
            ->setRetries((int) env('ES_RETRIES', 2))
            ->setSSLVerification(false)
            ->build();
    }
}
