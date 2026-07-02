<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../../db_connect.php';
require_once __DIR__ . '/../../Models/Flight.php';

$errors = [];
$success = false;

$airlines = $conn->query("SELECT * FROM airlines")->fetch_all(MYSQLI_ASSOC);
$airports = $conn->query("SELECT * FROM airports")->fetch_all(MYSQLI_ASSOC);

$edit_id = $_GET['id'] ?? null;
$flight = null;
if ($edit_id) {
    $stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $flight = $stmt->get_result()->fetch_assoc();
    if (!$flight) {
        echo "<script>alert('Vé không tồn tại'); window.location='list_ticket.php';</script>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $airline_id = $_POST['airline_id'];
    $flight_number = trim($_POST['flight_number']);
    $departure_airport = $_POST['departure_airport'];
    $arrival_airport = $_POST['arrival_airport'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $duration = trim($_POST['duration']);
    $flight_type = $_POST['flight_type'];
    $ticket_type = $_POST['ticket_type'];
    $base_price = (int)$_POST['base_price'];
    $baggage_limit = (int)$_POST['baggage_limit'];

    if (!$airline_id) $errors[] = 'Chọn hãng bay';
    if (!$flight_number) $errors[] = 'Số hiệu chuyến bay bắt buộc';
    if (!$departure_airport || !$arrival_airport) $errors[] = 'Phải chọn sân bay đi và đến';
    if ($departure_airport === $arrival_airport) $errors[] = 'Sân bay đi và sân bay đến không được giống nhau';
    if ($base_price <= 0) $errors[] = 'Giá vé phải > 0';

    if (!empty($departure_time) && !empty($arrival_time)) {
        $dep = new DateTime($departure_time);
        $arr = new DateTime($arrival_time);
        if ($arr <= $dep) $errors[] = 'Giờ đến phải sau giờ khởi hành';
    }

    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM flights WHERE airline_id = ? AND flight_number = ?".($edit_id ? " AND id<>?" : ""));
    if ($edit_id) {
        $checkStmt->bind_param("isi", $airline_id, $flight_number, $edit_id);
    } else {
        $checkStmt->bind_param("is", $airline_id, $flight_number);
    }
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) $errors[] = 'Số hiệu chuyến bay đã tồn tại';

    if (empty($errors)) {
        if ($edit_id) {
            $stmt = $conn->prepare("UPDATE flights SET airline_id=?, flight_number=?, departure_airport=?, arrival_airport=?,
                departure_time=?, arrival_time=?, duration=?, flight_type=?, ticket_type=?, base_price=?, baggage_limit=? WHERE id=?");
            $stmt->bind_param("issssssssiii",
                $airline_id, $flight_number, $departure_airport, $arrival_airport,
                $departure_time, $arrival_time, $duration, $flight_type, $ticket_type,
                $base_price, $baggage_limit, $edit_id
            );
            $stmt->execute();
            $_SESSION['success_edit_flight'] = true;
        } else {
            $one_way = '1 chiều';
            $stmt = $conn->prepare("INSERT INTO flights
                (airline_id, flight_number, departure_airport, arrival_airport, departure_time, arrival_time, duration, flight_type, ticket_type, base_price, baggage_limit)
                VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("issssssssii",
                $airline_id, $flight_number, $departure_airport, $arrival_airport,
                $departure_time, $arrival_time, $duration, $flight_type,
                $one_way, $base_price, $baggage_limit
            );
            $stmt->execute();
            $_SESSION['success_create_flight'] = true;
        }
        header($edit_id ? "Location: edit_ticket.php?id=$edit_id" : 'Location: add_ticket.php');
        exit;
    }
}

$filters = [
    'code'    => $_GET['code'] ?? '',
    'airline' => $_GET['airline'] ?? '',
    'from'    => $_GET['from'] ?? '',
    'to'      => $_GET['to'] ?? '',
    'price'   => $_GET['price'] ?? ''
];

$sql = "SELECT f.*, a.name AS airline_name, a.logo_url,
               dep.name_vn AS dep_name, arr.name_vn AS arr_name
        FROM flights f
        JOIN airlines a ON f.airline_id = a.id
        JOIN airports dep ON f.departure_airport = dep.code
        JOIN airports arr ON f.arrival_airport = arr.code
        WHERE 1 = 1";

if (!empty($filters['code'])) {
    $code = $conn->real_escape_string($filters['code']);
    $sql .= " AND f.flight_number LIKE '%$code%'";
}

if (!empty($filters['airline'])) {
    $airline = intval($filters['airline']);
    $sql .= " AND f.airline_id = $airline";
}

if (!empty($filters['from'])) {
    $from = strtoupper($conn->real_escape_string($filters['from']));
    $sql .= " AND f.departure_airport = '$from'";
}

if (!empty($filters['to'])) {
    $to = strtoupper($conn->real_escape_string($filters['to']));
    $sql .= " AND f.arrival_airport = '$to'";
}

if (!empty($filters['price'])) {
    $price = intval($filters['price']);
    $min = max(0, $price - 300000);
    $max = $price + 300000;
    $sql .= " AND f.base_price BETWEEN $min AND $max";
}

$sql .= " ORDER BY f.created_at DESC";
$flights = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

if (!empty($_SESSION['success_create_flight'])) {
    $success = true;
    unset($_SESSION['success_create_flight']);
}
if (!empty($_SESSION['success_edit_flight'])) {
    $success = true;
    unset($_SESSION['success_edit_flight']);
}


