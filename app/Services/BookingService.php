<?php

namespace App\Services;

use App\Repositories\BookingRepository;
use App\Repositories\FlightRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class BookingService
{
    protected $bookingRepo;
    protected $flightRepo;
    protected $userRepo;

    public function __construct(
        BookingRepository $bookingRepo,
        FlightRepository $flightRepo,
        UserRepository $userRepo
    ) {
        $this->bookingRepo = $bookingRepo;
        $this->flightRepo = $flightRepo;
        $this->userRepo = $userRepo;
    }

    public function getCheckoutData($flightId, $userId)
    {
        $flight = $this->flightRepo->getById($flightId);
        if (!$flight) {
            throw new Exception("Chuyến bay không tồn tại");
        }
        $bookedSeats = $this->flightRepo->getBookedSeats($flightId);
        $defaultName = $this->userRepo->getFullname($userId);

        return [
            'flight' => (array)$flight,
            'bookedSeats' => $bookedSeats,
            'defaultName' => $defaultName,
            'user_id' => $userId
        ];
    }

    public function createBooking(array $data)
    {
        $flightId = intval($data['flight_id'] ?? 0);
        $userId   = intval($data['user_id'] ?? 0);
        $ticketType = ($data['ticketType'] ?? 'normal') === 'premium' ? 'Cao cấp' : 'Thường';
        $adult  = intval($data['adult'] ?? 1);
        $child  = intval($data['child'] ?? 0);
        $baby   = intval($data['baby'] ?? 0);
        $peopleCount = $adult + $child + $baby;

        $selectedSeats = array_values(array_filter($data['selectedSeats'] ?? [], fn($s) => strlen(trim((string)$s)) > 0));
        
        // Check seat availability
        $bookedSeats = $this->flightRepo->getBookedSeats($flightId);
        foreach ($selectedSeats as $seat) {
            if (in_array($seat, $bookedSeats)) {
                throw new Exception("Ghế $seat đã được đặt. Vui lòng chọn ghế khác.");
            }
        }

        $seatNumbers = implode(',', $selectedSeats);

        $flightRow = $this->flightRepo->getById($flightId);
        if (!$flightRow) {
            throw new Exception("Chuyến bay không tồn tại");
        }
        $flightRow = (array)$flightRow;
        $basePrice = intval($flightRow['base_price'] ?? 0);
        $baggageLimit = intval($flightRow['baggage_limit'] ?? 20);
        $feePerKg = 50000;

        $childLimit = floor($baggageLimit * 0.75);
        $baggageExtraKg = 0;
        foreach ($data['passengers'] ?? [] as $p) {
            $type = $p['type'] ?? 'adult';
            $baggage = intval($p['baggage'] ?? 0);
            $limit = $type === 'child' ? $childLimit : ($type === 'baby' ? 0 : $baggageLimit);
            $baggageExtraKg += max(0, $baggage - $limit);
        }

        $mult = $ticketType === 'Cao cấp' ? 1.5 : 1.0;
        $ticketsSum = $adult * $basePrice + $child * ($basePrice * 0.75) + $baby * ($basePrice * 0.5);
        $totalPrice = round($mult * $ticketsSum) + ($baggageExtraKg * $feePerKg);

        $bookingData = [
            'flight_id' => $flightId,
            'user_id' => $userId,
            'booking_code' => 'FN-' . date('Ymd-His') . '-' . rand(1000, 9999),
            'ticket_type' => $ticketType,
            'people_count' => $peopleCount,
            'baggage_extra' => $baggageExtraKg,
            'total_price' => $totalPrice,
            'seat_numbers' => $seatNumbers,
            'contact_name' => $data['contactName'] ?? '',
            'contact_phone' => $data['contactPhone'] ?? '',
            'contact_email' => $data['contactEmail'] ?? '',
            'promo_code' => $data['promoCode'] ?? '',
            'passengers' => json_encode($data['passengers'] ?? []),
            'status' => 'Đã thanh toán',
            'created_at' => now()
        ];

        return DB::transaction(function () use ($bookingData, $flightId, $peopleCount, $ticketType, $totalPrice, $baggageExtraKg) {
            $bookingId = $this->bookingRepo->create($bookingData);
            if ($bookingId) {
                $this->flightRepo->updateFlightSeats($flightId, $peopleCount, $ticketType);
                return [
                    'success' => true,
                    'booking_id' => $bookingId,
                    'server_total' => $totalPrice,
                    'baggage_extra_kg' => $baggageExtraKg
                ];
            }
            throw new Exception("Không thể lưu đặt vé");
        });
    }
}
