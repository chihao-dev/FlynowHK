<?php

namespace App\Services;

use App\Repositories\MyTicketRepository;
use App\Repositories\PromotionRepository;

class MyTicketService
{
    protected $myTicketRepo;
    protected $promotionRepo;

    public function __construct(
        MyTicketRepository $myTicketRepo,
        PromotionRepository $promotionRepo
    ) {
        $this->myTicketRepo = $myTicketRepo;
        $this->promotionRepo = $promotionRepo;
    }

    public function getMyTickets(int $userId): array
    {
        return $this->myTicketRepo->getBookingsByUser($userId);
    }

    public function getTicketDetail(int $bookingId, int $userId): ?array
    {
        $ticket = $this->myTicketRepo->getBookingDetail($bookingId, $userId);
        if (!$ticket) {
            return null;
        }

        $ticket = (array)$ticket;

        // Calculate pricing
        $passengers = json_decode($ticket['passengers'], true) ?: [];
        $basePrice = (float)$ticket['base_price'];
        if (strtolower($ticket['ticket_type']) === 'cao cấp') {
            $basePrice *= 1.5;
        }

        $adult = $child = $baby = 0;
        $extraFees = 0;

        foreach ($passengers as $p) {
            $dob = !empty($p['dob']) ? strtotime($p['dob']) : 0;
            $age = $dob ? floor((time() - $dob) / (365 * 24 * 60 * 60)) : 99;
            if ($age >= 12) {
                $adult++;
            } elseif ($age >= 2) {
                $child++;
            } else {
                $baby++;
            }

            $baggage = intval($p['baggage'] ?? 0);
            $limit = intval($ticket['baggage_limit']);
            if ($age >= 2 && $age < 12) $limit = (int)($limit * 0.75);
            elseif ($age < 2) $limit = 0;
            $extraFees += max(0, $baggage - $limit) * 50000;
        }

        $subtotal = $adult * $basePrice + $child * ($basePrice * 0.75) + $baby * ($basePrice * 0.5) + $extraFees;

        // Apply promo
        $discountValue = 0;
        $discountText = '';
        if (!empty($ticket['promo_code'])) {
            $promo = $this->promotionRepo->findValidPromoByCode($ticket['promo_code']);
            if ($promo) {
                if ($promo['discount_type'] === 'percent') {
                    $discountValue = $subtotal * ($promo['discount_value'] / 100);
                    $discountText = "Giảm {$promo['discount_value']}% ({$promo['code']})";
                } else {
                    $discountValue = $promo['discount_value'];
                    $discountText = "Giảm " . number_format($promo['discount_value'], 0, ',', '.') . "đ ({$promo['code']})";
                }
                $subtotal = max(0, $subtotal - $discountValue);
            }
        }

        $ticketClassName = strtolower($ticket['ticket_type']) === 'cao cấp' ? 'Premium Class' : 'Economy Class';
        $ticketClassCss  = strtolower($ticket['ticket_type']) === 'cao cấp' ? 'premium' : 'normal';
        $depart = date('H:i d/m/Y', strtotime($ticket['departure_time']));
        $arrive = date('H:i d/m/Y', strtotime($ticket['arrival_time']));
        $qrURL  = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($ticket['booking_code']);
        $seatNumbers = explode(',', $ticket['seat_numbers']);

        return array_merge($ticket, [
            'passengers'       => $passengers,
            'seatNumbers'      => $seatNumbers,
            'ticketClassName'  => $ticketClassName,
            'ticketClassCss'   => $ticketClassCss,
            'depart'           => $depart,
            'arrive'           => $arrive,
            'qrURL'            => $qrURL,
            'basePrice'        => $basePrice,
            'adult'            => $adult,
            'child'            => $child,
            'baby'             => $baby,
            'adultPrice'       => $adult * $basePrice,
            'childPrice'       => $child * ($basePrice * 0.75),
            'babyPrice'        => $baby  * ($basePrice * 0.5),
            'extraFees'        => $extraFees,
            'subtotal'         => $subtotal,
            'discountValue'    => $discountValue,
            'discountText'     => $discountText,
        ]);
    }
}
