<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class AirportRepository
{
    public function allAsMap()
    {
        $rows = DB::table('airports')->get();
        $map = [];
        foreach ($rows as $row) {
            $map[$row->code] = $row->city_vn . ' - ' . $row->name_vn . ' (' . $row->code . ')';
        }
        return $map;
    }

    public function all()
    {
        return DB::table('airports')
            ->orderBy('city_vn')
            ->get()
            ->map(fn($r) => (array)$r)
            ->toArray();
    }

    public function search($query)
    {
        if (empty($query)) {
            return $this->all();
        }

        return DB::table('airports')
            ->where('code', 'LIKE', "%{$query}%")
            ->orWhere('city_vn', 'LIKE', "%{$query}%")
            ->orWhere('name_vn', 'LIKE', "%{$query}%")
            ->orderBy('city_vn')
            ->get()
            ->map(fn($r) => (array)$r)
            ->toArray();
    }
}
