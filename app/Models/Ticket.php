<?php
class Ticket 
{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTicketById($ticket_id, $user_id) {
        $sql = "SELECT 
                    b.*, 
                    f.flight_number, 
                    f.departure_airport, 
                    f.arrival_airport, 
                    f.departure_time, 
                    f.arrival_time, 
                    f.duration, 
                    f.flight_type AS flight_type_desc, 
                    f.ticket_type AS flight_ticket_type, 
                    f.base_price, 
                    f.baggage_limit,
                    f.airline_id,     
                    a.name AS airline_name, 
                    a.logo_url
                FROM bookings b
                JOIN flights f ON b.flight_id = f.id
                JOIN airlines a ON f.airline_id = a.id
                WHERE b.id = ? AND b.user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $ticket_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function calculatePricing($ticket) {
        $passengers = json_decode($ticket['passengers'], true) ?: [];
        $basePrice = $ticket['base_price'];
        if (strtolower($ticket['ticket_type']) === 'cao cấp') $basePrice *= 1.5;

        $adult = $child = $baby = 0;
        $extraFees = 0;

        foreach ($passengers as $p) {
            $dob = !empty($p['dob']) ? strtotime($p['dob']) : 0;
            $age = $dob ? floor((time() - $dob) / (365*24*60*60)) : 0;
            if ($age >= 12) $adult++;
            elseif ($age >= 2) $child++;
            else $baby++;

            $baggage = intval($p['baggage'] ?? 0);
            $limit = intval($ticket['baggage_limit']);
            if ($age >= 2 && $age < 12) $limit *= 0.75;
            elseif ($age < 2) $limit = 0;

            $overWeight = max(0, $baggage - $limit);
            $p['overWeight'] = $overWeight;
            $extraFees += $overWeight * 50000;
        }

        $adultPrice = $adult * $basePrice;
        $childPrice = $child * ($basePrice * 0.75);
        $babyPrice = $baby * ($basePrice * 0.5);
        $subtotal = $adultPrice + $childPrice + $babyPrice + $extraFees;

        return [
            'adult' => $adult,
            'child' => $child,
            'baby' => $baby,
            'extraFees' => $extraFees,
            'subtotal' => $subtotal
        ];
    }

    public function getValidPromo($promoCode, $ticket) {
    $stmt = $this->conn->prepare("
        SELECT * FROM promotions 
        WHERE code = ? 
          AND start_date <= CURDATE()
          AND end_date >= CURDATE()
        LIMIT 1
    ");

    $promoCodeUpper = strtoupper($promoCode); 
    $stmt->bind_param("s", $promoCodeUpper);
    $stmt->execute();
    $promo = $stmt->get_result()->fetch_assoc();
    if (!$promo) return null;

    $valid = true;
        if ($promo['airline_id'] != 0 && $promo['airline_id'] != $ticket['airline_id']) $valid = false;
        if ($promo['route_from'] !== 'ALL' && $promo['route_from'] !== $ticket['departure_airport']) $valid = false;
        if ($promo['route_to'] !== 'ALL' && $promo['route_to'] !== $ticket['arrival_airport']) $valid = false;
        if ($ticket['people_count'] < $promo['min_tickets']) $valid = false;

        return $valid ? $promo : null;
    }


    public function applyDiscount($subtotal, $promo) {
        if ($promo['discount_type'] === 'percent') {
            $discountValue = $subtotal * ($promo['discount_value'] / 100);
            $discountText = "Giảm {$promo['discount_value']}% ({$promo['code']})";
        } else {
            $discountValue = $promo['discount_value'];
            $discountText = "Giảm " . number_format($promo['discount_value'],0,",",".") . "đ ({$promo['code']})";
        }
        $subtotal -= $discountValue;
        if ($subtotal < 0) $subtotal = 0;
        return [$subtotal, $discountValue, $discountText];
    }
}
