<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class AirlineRepository
{
    public function all()
    {
        return DB::table('airlines')
            ->select('id', 'name', 'logo_url')
            ->get()
            ->map(fn($r) => (array)$r)
            ->toArray();
    }
}
