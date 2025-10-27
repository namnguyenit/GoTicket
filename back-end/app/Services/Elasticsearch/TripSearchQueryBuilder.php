<?php

namespace App\Services\Elasticsearch;

class TripSearchQueryBuilder
{
    public static function build(array $criteria): array
    {
        $originId = (string)($criteria['origin_id'] ?? '');
        $destinationId = (string)($criteria['destination_id'] ?? '');
        $vehicleType = $criteria['vehicle_type'] ?? null; // 'bus' | 'train'
        $coachTypes = $criteria['coach_types'] ?? null; // array of strings, only for bus
        $date = $criteria['date'] ?? null; // Y-m-d
        $priceMin = $criteria['price_min'] ?? null;
        $priceMax = $criteria['price_max'] ?? null;
        $timeOfDay = $criteria['time_of_day'] ?? null;
        $perPage = (int)($criteria['per_page'] ?? 12);
        $page = max(1, (int)($criteria['page'] ?? 1));
        $from = ($page - 1) * $perPage;

        $filters = [];
        if ($originId !== '') { $filters[] = ['term' => ['origin_id' => $originId]]; }
        if ($destinationId !== '') { $filters[] = ['term' => ['destination_id' => $destinationId]]; }
        if ($vehicleType) { $filters[] = ['term' => ['vehicle_type' => $vehicleType]]; }
        if ($date) {
            $filters[] = ['range' => ['departure_time' => [
                'gte' => $date.'T00:00:00Z', 'lte' => $date.'T23:59:59Z',
            ]]];
        }
        if ($priceMin !== null || $priceMax !== null) {
            $range = [];
            if ($priceMin !== null) $range['gte'] = (float)$priceMin;
            if ($priceMax !== null) $range['lte'] = (float)$priceMax;
            $filters[] = ['range' => ['price' => $range]];
        }
        if ($timeOfDay) {
            if ($timeOfDay === 'sang') { $filters[] = ['range' => ['departure_hour' => ['gte' => 5, 'lt' => 12]]]; }
            elseif ($timeOfDay === 'chieu') { $filters[] = ['range' => ['departure_hour' => ['gte' => 12, 'lt' => 18]]]; }
            elseif ($timeOfDay === 'toi') { $filters[] = ['range' => ['departure_hour' => ['gte' => 18, 'lte' => 23]]]; }
        }
        if ($vehicleType === 'bus' && is_array($coachTypes) && count($coachTypes)) {
            $filters[] = ['terms' => ['bus_coach_types' => array_values(array_filter(array_map('strval', $coachTypes)))] ];
        }

        return [
            'index' => 'trips-read',
            'body' => [
                'track_total_hits' => 10000,
                '_source' => ['id','route','from','to','vehicle_type','departure_time','arrival_time','price','vendor_id','seats_available'],
                'query' => ['bool' => ['filter' => $filters]],
                'sort' => [ ['departure_time' => 'asc'] ],
                'from' => $from,
                'size' => $perPage,
            ],
        ];
    }
}
