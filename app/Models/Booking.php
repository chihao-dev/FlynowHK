<?php
class Booking
{
    public static function create($conn, $data)
    {
        $stmt = $conn->prepare("
            INSERT INTO bookings
            (flight_id, user_id, booking_code, ticket_type, people_count, baggage_extra, total_price, seat_numbers, contact_name, contact_phone, contact_email, promo_code, passengers, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Đã thanh toán')
        ");

        $stmt->bind_param(
            "iissiddssssss",
            $data['flight_id'],
            $data['user_id'],
            $data['booking_code'],
            $data['ticket_type'],
            $data['people_count'],
            $data['baggage_extra'],
            $data['total_price'],
            $data['seat_numbers'],
            $data['contact_name'],
            $data['contact_phone'],
            $data['contact_email'],
            $data['promo_code'],
            $data['passengers']
        );

        return $stmt->execute() && $stmt->affected_rows > 0;
    }

    public static function updateFlightSeats($conn, $flight_id, $people_count, $ticketType)
    {
        $seatColumn = $ticketType === 'Cao cấp' ? 'seats_premium' : 'seats_normal';
        $stmt = $conn->prepare("
            UPDATE flights
            SET $seatColumn = GREATEST($seatColumn - ?, 0)
            WHERE id = ?
        ");
        $stmt->bind_param("ii", $people_count, $flight_id);
        $stmt->execute();
    }
}
