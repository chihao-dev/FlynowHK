<?php
namespace App\Http\Controllers;

use App\Services\PromotionService;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    protected $promotionService;
    protected $airlineRepo;

    public function __construct(PromotionService $promotionService, \App\Repositories\AirlineRepository $airlineRepo)
    {
        $this->promotionService = $promotionService;
        $this->airlineRepo = $airlineRepo;
    }

    /**
     * Compatibility method for public/promotions.php
     */
    public function getPromotionsData()
    {
        return [
            'full_promotions' => $this->promotionService->getAllPromotionsWithAirlines(),
            'airlines' => $this->airlineRepo->all(),
        ];
    }

    public function index(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            return redirect('/admin/dashboard.php');
        }

        $promotions = $this->promotionService->getAllPromotionsWithAirlines();
        $airlines = $this->airlineRepo->all();

        return view('promotions', [
            'full_promotions' => $promotions,
            'airlines' => $airlines,
        ]);
    }
}
