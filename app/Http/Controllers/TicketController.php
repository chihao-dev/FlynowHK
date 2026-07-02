<?php
require_once __DIR__ . '/../../Models/Ticket.php';

class TicketController {
    private $ticketModel;

    public function __construct($db) {
        $this->ticketModel = new Ticket($db);
    }

    public function show($ticket_id, $user_id) {
        $ticket = $this->ticketModel->getTicketById($ticket_id, $user_id);
        if (!$ticket) {
            echo "Vé không tồn tại hoặc bạn không có quyền xem!";
            exit;
        }

        $pricing = $this->ticketModel->calculatePricing($ticket);

        $discountValue = 0;
        $discountText = '';
        if (!empty($ticket['promo_code'])) {
            $promo = $this->ticketModel->getValidPromo($ticket['promo_code'], $ticket);
            if ($promo) {
                list($pricing['subtotal'], $discountValue, $discountText) = $this->ticketModel->applyDiscount($pricing['subtotal'], $promo);
            }
        }

        $ticketClassName = strtolower($ticket['ticket_type']) === 'cao cấp' ? 'Premium Class' : 'Economy Class';
        $ticketClassCss  = strtolower($ticket['ticket_type']) === 'cao cấp' ? 'premium' : 'normal';
        $depart          = date('H:i d/m/Y', strtotime($ticket['departure_time']));
        $arrive          = date('H:i d/m/Y', strtotime($ticket['arrival_time']));
        $qrURL           = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($ticket['booking_code']);
        $seatNumbers     = explode(',', $ticket['seat_numbers']);
        $passengers      = json_decode($ticket['passengers'], true) ?: [];

        $basePrice   = $ticket['base_price'];
        if (strtolower($ticket['ticket_type']) === 'cao cấp') $basePrice *= 1.5;

        $adultPrice  = $pricing['adult'] * $basePrice;
        $childPrice  = $pricing['child'] * ($basePrice * 0.75);
        $babyPrice   = $pricing['baby'] * ($basePrice * 0.5);
        $extraFees   = $pricing['extraFees'];
        $subtotal    = $pricing['subtotal'];
        $adult       = $pricing['adult'];
        $child       = $pricing['child'];
        $baby        = $pricing['baby'];

        return compact(
            'ticket','ticketClassName','ticketClassCss','depart','arrive','qrURL',
            'seatNumbers','passengers','basePrice','adultPrice','childPrice','babyPrice',
            'extraFees','subtotal','adult','child','baby','discountValue','discountText'
        );
    }
}
