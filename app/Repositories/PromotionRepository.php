<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class PromotionRepository
{
    public function all()
    {
        return DB::table('promotions')
            ->get()
            ->map(fn($item) => (array)$item)
            ->toArray();
    }

    public function allWithAirlines()
    {
        return DB::table('promotions')
            ->join('airlines', 'airlines.id', '=', 'promotions.airline_id')
            ->select('promotions.*', 'airlines.name as airline_name', 'airlines.logo_url')
            ->orderBy('promotions.start_date', 'desc')
            ->get()
            ->map(fn($item) => (array)$item)
            ->toArray();
    }

    public function findValidPromoByCode(string $code): ?array
    {
        $promo = DB::table('promotions')
            ->where('code', strtoupper($code))
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        return $promo ? (array)$promo : null;
    }
}
