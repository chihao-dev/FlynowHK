<?php

namespace App\Services;

use App\Repositories\FlightSearchRepository;
use App\Repositories\AirportRepository;

class FlightSearchService
{
    protected $flightSearchRepo;
    protected $airportRepo;

    public function __construct(
        FlightSearchRepository $flightSearchRepo,
        AirportRepository $airportRepo
    ) {
        $this->flightSearchRepo = $flightSearchRepo;
        $this->airportRepo = $airportRepo;
    }

    private function extractAirportCode(string $str): string
    {
        if (preg_match('/\((\w+)\)/', $str, $matches)) {
            return $matches[1];
        }
        return $str;
    }

    public function searchFlights(?string $from, ?string $to, ?string $dateGo, ?string $dateReturn): array
    {
        $fromCode = !empty($from) ? $this->extractAirportCode($from) : '';
        $toCode   = !empty($to)   ? $this->extractAirportCode($to)   : '';

        if (empty($dateGo)) {
            $dateGo = date('Y-m-d');
        }
        if (empty($dateReturn)) {
            $dateReturn = date('Y-m-d', strtotime('+30 days'));
        }

        return $this->flightSearchRepo->search($fromCode, $toCode, $dateGo, $dateReturn);
    }

    public function getAirportsMap(): array
    {
        return $this->airportRepo->allAsMap();
    }
}
