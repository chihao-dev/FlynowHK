<?php

namespace App\Http\Controllers;

use App\Repositories\AirportRepository;
use App\Repositories\FlightRepository;
use Illuminate\Http\Request;

class AirportController extends Controller
{
    protected $airportRepo;
    protected $flightRepo;

    public function __construct(AirportRepository $airportRepo, FlightRepository $flightRepo)
    {
        $this->airportRepo = $airportRepo;
        $this->flightRepo = $flightRepo;
    }

    public function search(Request $request)
    {
        $action = $request->query('action', 'airport');

        if ($action === 'flights') {
            $from = $request->query('from', '');
            $to = $request->query('to', '');
            $date = $request->query('date_go', '');

            if (empty($from) || empty($to) || empty($date)) {
                return response()->json([]);
            }

            $flights = $this->flightRepo->search($from, $to, $date);
            return response()->json($flights);
        }

        $query = $request->query('q', '');
        $airports = $this->airportRepo->search($query);
        return response()->json($airports);
    }
}
