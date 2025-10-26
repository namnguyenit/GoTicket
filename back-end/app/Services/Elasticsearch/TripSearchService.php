<?php

namespace App\Services\Elasticsearch;

use Elastic\Elasticsearch\Client;

class TripSearchService
{
    public function __construct(private Client $es) {}

    public function search(array $criteria): array
    {
        $query = TripSearchQueryBuilder::build($criteria);
        $resp = $this->es->search($query);
        $hits = $resp['hits']['hits'] ?? [];
        $total = is_array($resp['hits']['total'] ?? null) ? ($resp['hits']['total']['value'] ?? 0) : 0;
        $items = array_map(fn($h) => $h['_source'], $hits);

        $perPage = (int)($criteria['per_page'] ?? ($query['body']['size'] ?? 12));
        $page = (int)($criteria['page'] ?? (isset($query['body']['from']) ? ((int)$query['body']['from'] / max(1, $perPage)) + 1 : 1));
        if ($page < 1) { $page = 1; }
        if ($perPage < 1) { $perPage = 12; }

        return [
            'items' => $items,
            'total' => (int)$total,
            'per_page' => $perPage,
            'current_page' => $page,
        ];
    }
}
