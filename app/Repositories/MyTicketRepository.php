<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class MyTicketRepository
{
    public function getBookingsByUser(int $userId)
    {
        return DB::table('bookings')
            ->join('flights', 'bookings.flight_id', '=', 'flights.id')
            ->join('airlines', 'flights.airline_id', '=', 'airlines.id')
            ->select(
                'bookings.*',
                'flights.flight_number',
                'flights.departure_airport',
                'flights.arrival_airport',
                'flights.departure_time',
                'flights.arrival_time',
                'flights.base_price',
                'flights.ticket_type as flight_ticket_type',
                'airlines.name as airline_name',
                'airlines.logo_url'
            )
            ->where('bookings.user_id', $userId)
            ->where('bookings.status', 'Đã thanh toán')
            ->orderBy('bookings.created_at', 'desc')
            ->get()
            ->map(fn($r) => (array)$r)
            ->toArray();
    }

    public function getBookingDetail(int $bookingId, int $userId)
    {
        return DB::table('bookings')
            ->join('flights', 'bookings.flight_id', '=', 'flights.id')
            ->join('airlines', 'flights.airline_id', '=', 'airlines.id')
            ->select(
                'bookings.*',
                'flights.flight_number',
                'flights.departure_airport',
                'flights.arrival_airport',
                'flights.departure_time',
                'flights.arrival_time',
                'flights.duration',
                'flights.flight_type as flight_type_desc',
                'flights.ticket_type as flight_ticket_type',
                'flights.base_price',
                'flights.baggage_limit',
                'flights.airline_id',
                'airlines.name as airline_name',
                'airlines.logo_url'
            )
            ->where('bookings.id', $bookingId)
            ->where('bookings.user_id', $userId)
            ->first();
    }
}
