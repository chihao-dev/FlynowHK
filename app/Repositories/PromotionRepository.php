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
        $today = now()->toDateString();

        return DB::table('promotions')
            ->join('airlines', 'airlines.id', '=', 'promotions.airline_id')
            ->select('promotions.*', 'airlines.name as airline_name', 'airlines.logo_url')
            ->whereDate('promotions.end_date', '>=', $today)   // Ẩn đã hết hạn
            ->orderBy('promotions.start_date', 'desc')
            ->get()
            ->map(function($item) use ($today) {
                $p = (array)$item;
                if ($today < $p['start_date']) {
                    $p['status']       = 'upcoming';
                    $p['status_label'] = 'Chưa bắt đầu';
                } else {
                    $p['status']       = 'active';
                    $p['status_label'] = 'Còn hạn';
                }
                return $p;
            })
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
