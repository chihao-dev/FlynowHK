<?php
namespace App\Http\Controllers;

use App\Services\FlightSearchService;
use Illuminate\Http\Request;

class CheapTicketController extends Controller
{
    protected $flightSearchService;

    public function __construct(FlightSearchService $flightSearchService)
    {
        $this->flightSearchService = $flightSearchService;
    }

    public function index(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            return redirect('/admin/add_ticket.php');
        }

        $from       = $request->input('from');
        $to         = $request->input('to');
        $dateGo     = $request->input('date_go');
        $dateReturn = $request->input('date_return');

        $flights  = $this->flightSearchService->searchFlights($from, $to, $dateGo, $dateReturn);
        $airports = $this->flightSearchService->getAirportsMap();

        return view('cheap-tickets', [
            'flights'     => $flights,
            'airports'    => $airports,
            'from'        => $from,
            'to'          => $to,
            'date_go'     => $dateGo,
            'date_return' => $dateReturn,
        ]);
    }
}
