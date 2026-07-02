<?php
class Dashboard {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTotalFlights() {
        $sql = "SELECT COUNT(id) as total FROM flights";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function getTotalBookings() {
        $sql = "SELECT COUNT(id) as total FROM bookings";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function getTotalRevenue() {
        $sql = "SELECT SUM(total_price) as total FROM bookings WHERE status='Đã thanh toán'";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return number_format($row['total'] ?? 0, 0, ',', '.');
    }

    public function getUpcomingFlights($limit = 5) {
        $sql = "SELECT f.*, a.name AS airline_name 
                FROM flights f 
                JOIN airlines a ON f.airline_id = a.id
                WHERE f.departure_time > NOW()
                ORDER BY f.departure_time ASC
                LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function getRecentBookings($limit = 5) {
        $sql = "SELECT b.*, f.flight_number 
                FROM bookings b 
                JOIN flights f ON b.flight_id = f.id
                ORDER BY b.created_at DESC
                LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function getBookingStatusCounts() {
        $sql = "SELECT status, COUNT(id) as count FROM bookings GROUP BY status";
        $result = $this->conn->query($sql);
        $counts = [];
        while($row = $result->fetch_assoc()) {
            $counts[$row['status']] = $row['count'];
        }
        return [
            'Đã thanh toán' => $counts['Đã thanh toán'] ?? 0,
            'Chưa thanh toán' => $counts['Chưa thanh toán'] ?? 0,
        ];
    }
}
