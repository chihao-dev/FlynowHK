<?php

class Flight
{
    public static function search($conn, $from, $to, $date_go, $date_return)
    {
        $sql = "
        SELECT f.*, a.name AS airline_name, a.logo_url,
            da.city_vn AS departure_city,
            aa.city_vn AS arrival_city
        FROM flights f
        JOIN airlines a ON f.airline_id = a.id
        JOIN airports da ON f.departure_airport = da.code
        JOIN airports aa ON f.arrival_airport = aa.code
        WHERE 1=1
        ";
        
        $params = [];
        $types = '';

        if ($from !== '') {
            $sql .= " AND f.departure_airport LIKE ?";
            $params[] = "%$from%";
            $types .= 's';
        }

        if ($to !== '') {
            $sql .= " AND f.arrival_airport LIKE ?";
            $params[] = "%$to%";
            $types .= 's';
        }

        if ($date_go !== '') {
            $sql .= " AND DATE(f.departure_time) >= ?";
            $params[] = $date_go;
            $types .= 's';
        }

        if ($date_return !== '') {
            $sql .= " AND DATE(f.departure_time) <= ?";
            $params[] = $date_return;
            $types .= 's';
        }

        $sql .= " ORDER BY f.departure_time ASC";

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $flights = [];
        while ($row = $result->fetch_assoc()) {
            $row['seats_normal_remaining'] =
                ($row['seats_normal'] ?? 0) - ($row['booked_normal'] ?? 0);
            $row['seats_premium_remaining'] =
                ($row['seats_premium'] ?? 0) - ($row['booked_premium'] ?? 0);

            $flights[] = $row;
        }

        return $flights;
    }

    public static function getById($conn, $flight_id)
    {
        $sql = "SELECT f.*, a.name AS airline_name, a.logo_url 
                FROM flights f 
                JOIN airlines a ON f.airline_id = a.id 
                WHERE f.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $flight_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getBookedSeats($conn, $flight_id)
    {
        $seats = [];
        $sql = "SELECT seat_numbers FROM bookings WHERE flight_id = ? AND status='Đã thanh toán'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $flight_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            foreach (explode(",", $row['seat_numbers']) as $s) {
                $seats[] = trim($s);
            }
        }
        return $seats;
    }
}

