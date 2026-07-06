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

    /**
     * Compatibility method for public/cheap-tickets.php
     */
    public function getSearchData()
    {
        $from       = $_POST['from'] ?? $_GET['from'] ?? null;
        $to         = $_POST['to'] ?? $_GET['to'] ?? null;
        $dateGo     = $_POST['date_go'] ?? $_GET['date_go'] ?? null;
        $dateReturn = $_POST['date_return'] ?? $_GET['date_return'] ?? null;

        $flights  = $this->flightSearchService->searchFlights($from, $to, $dateGo, $dateReturn);
        $airports = $this->flightSearchService->getAirportsMap();

        return [
            'flights'     => $flights,
            'airports'    => $airports,
            'from'        => $from,
            'to'          => $to,
            'date_go'     => $dateGo,
            'date_return' => $dateReturn,
        ];
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
