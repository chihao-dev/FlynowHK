<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class FlightRepository
{
    public function getById($flightId)
    {
        return DB::table('flights')
            ->join('airlines', 'flights.airline_id', '=', 'airlines.id')
            ->select('flights.*', 'airlines.name as airline_name', 'airlines.logo_url')
            ->where('flights.id', $flightId)
            ->first();
    }

    public function getBookedSeats($flightId)
    {
        $rows = DB::table('bookings')
            ->where('flight_id', $flightId)
            ->where('status', 'Đã thanh toán')
            ->pluck('seat_numbers');

        $seats = [];
        foreach ($rows as $seatNumbers) {
            if ($seatNumbers) {
                foreach (explode(',', $seatNumbers) as $seat) {
                    $seats[] = trim($seat);
                }
            }
        }
        return $seats;
    }

    public function updateFlightSeats($flightId, $peopleCount, $ticketType)
    {
        $seatColumn = $ticketType === 'Cao cấp' ? 'seats_premium' : 'seats_normal';
        
        return DB::table('flights')
            ->where('id', $flightId)
            ->update([
                $seatColumn => DB::raw("GREATEST($seatColumn - $peopleCount, 0)")
            ]);
    }

    public function search($from, $to, $date)
    {
        return DB::table('flights')
            ->join('airlines', 'flights.airline_id', '=', 'airlines.id')
            ->select('flights.*', 'airlines.name as airline_name', 'airlines.logo_url')
            ->where('flights.departure_airport', $from)
            ->where('flights.arrival_airport', $to)
            ->where(DB::raw('DATE(flights.departure_time)'), $date)
            ->where('flights.departure_time', '>', now())
            ->get()
            ->map(function($flight) use ($date) {
                $f = (array)$flight;
                $depTimeOnly = date('H:i:s', strtotime($f['departure_time']));
                $arrTimeOnly = date('H:i:s', strtotime($f['arrival_time']));
                $f['departure_time'] = $date . ' ' . $depTimeOnly;
                $f['arrival_time'] = $date . ' ' . $arrTimeOnly;
                return $f;
            })
            ->toArray();
    }
}
