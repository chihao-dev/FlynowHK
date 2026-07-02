<?php

namespace App\Http\Controllers;

use App\Services\MyTicketService;
use Illuminate\Http\Request;

class MyTicketController extends Controller
{
    protected $myTicketService;

    public function __construct(MyTicketService $myTicketService)
    {
        $this->myTicketService = $myTicketService;
    }

    public function index(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            return redirect('/login.php');
        }

        $userId  = $_SESSION['user_id'];
        $tickets = $this->myTicketService->getMyTickets($userId);

        return view('my-tickets', [
            'tickets' => $tickets,
        ]);
    }

    public function show(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            return redirect('/login.php');
        }

        $userId    = $_SESSION['user_id'];
        $bookingId = intval($request->query('id', 0));

        $ticket = $this->myTicketService->getTicketDetail($bookingId, $userId);

        if (!$ticket) {
            abort(404, 'Vé không tồn tại hoặc bạn không có quyền xem!');
        }

        return view('ticket-detail', $ticket);
    }
}
