<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class BookingRepository
{
    public function create(array $bookingData)
    {
        return DB::table('bookings')->insertGetId($bookingData);
    }
}
