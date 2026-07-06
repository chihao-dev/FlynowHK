<?php
namespace App\Http\Controllers;

use App\Services\BookingService;
use App\Services\PromotionService;
use Illuminate\Http\Request;
use Exception;

class CheckoutController extends Controller
{
    protected $bookingService;
    protected $promotionService;

    public function __construct(BookingService $bookingService, PromotionService $promotionService)
    {
        $this->bookingService = $bookingService;
        $this->promotionService = $promotionService;
    }

    /**
     * Compatibility method for standalone public/checkout.php
     */
    public function getCheckoutData($flightId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['user_id'] ?? null;

        $checkoutData = $this->bookingService->getCheckoutData($flightId, $userId);
        $promotions = $this->promotionService->getAllPromotions();

        return [
            'flight' => $checkoutData['flight'],
            'bookedSeats' => $checkoutData['bookedSeats'],
            'promotions' => $promotions,
            'defaultName' => $checkoutData['defaultName'],
            'user_id' => $userId
        ];
    }

    public function showCheckout(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            return redirect('/login.php')->with('error', 'Bạn cần đăng nhập để đặt vé!');
        }

        $flightId = $request->query('flight_id');
        if (!$flightId) {
            return view('checkout_js_redirect');
        }

        $userId = $_SESSION['user_id'];

        try {
            $checkoutData = $this->bookingService->getCheckoutData($flightId, $userId);
            $promotions = $this->promotionService->getAllPromotions();

            return view('checkout', [
                'flight' => $checkoutData['flight'],
                'flightBookedSeats' => $checkoutData['bookedSeats'],
                'promotionsData' => $promotions,
                'defaultAdultName' => $checkoutData['defaultName'],
                'user_id' => $userId,
                'flight_id' => $flightId
            ]);
        } catch (Exception $e) {
            return redirect('/cheap-tickets.php')->with('error', $e->getMessage());
        }
    }

    public function handlePost(Request $request)
    {
        try {
            $data = $request->json()->all();
            if (empty($data)) {
                return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            }

            $result = $this->bookingService->createBooking($data);
            return response()->json([
                'success' => true,
                'message' => 'Đặt vé thành công',
                'server_total' => $result['server_total'],
                'baggage_extra_kg' => $result['baggage_extra_kg']
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
