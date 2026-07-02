<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class FlightSearchRepository
{
    public function search($fromCode, $toCode, $dateGo, $dateReturn)
    {
        $query = DB::table('flights')
            ->join('airlines', 'flights.airline_id', '=', 'airlines.id')
            ->join('airports as da', 'flights.departure_airport', '=', 'da.code')
            ->join('airports as aa', 'flights.arrival_airport', '=', 'aa.code')
            ->select(
                'flights.*',
                'airlines.name as airline_name',
                'airlines.logo_url',
                'da.city_vn as departure_city',
                'aa.city_vn as arrival_city'
            );

        if (!empty($fromCode)) {
            $query->where('flights.departure_airport', 'like', "%{$fromCode}%");
        }

        if (!empty($toCode)) {
            $query->where('flights.arrival_airport', 'like', "%{$toCode}%");
        }

        if (!empty($dateGo)) {
            $query->whereDate('flights.departure_time', '>=', $dateGo);
        }

        if (!empty($dateReturn)) {
            $query->whereDate('flights.departure_time', '<=', $dateReturn);
        }

        return $query
            ->orderBy('flights.departure_time', 'asc')
            ->get()
            ->map(fn($r) => (array)$r)
            ->toArray();
    }
}
