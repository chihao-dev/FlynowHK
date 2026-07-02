<?php
include '../db_connect.php'; 
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error], JSON_UNESCAPED_UNICODE);
    exit();
}

$action = $_GET['action'] ?? 'airport';

if ($action === 'flights') {
    $from_code = $_GET['from'] ?? '';
    $to_code = $_GET['to'] ?? '';
    $date_go = $_GET['date_go'] ?? '';

    if (empty($from_code) || empty($to_code) || empty($date_go)) {
        echo json_encode([]);
        exit();
    }

    $sql = "SELECT f.*, a.name AS airline_name, a.logo_url 
            FROM flights f
            JOIN airlines a ON f.airline_id = a.id
            WHERE f.departure_airport = ? AND f.arrival_airport = ? AND DATE(f.departure_time) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $from_code, $to_code, $date_go);
    $stmt->execute();
    $result = $stmt->get_result();

    $flights = [];
    while ($row = $result->fetch_assoc()) {
        $depTimeOnly = date('H:i:s', strtotime($row['departure_time']));
        $arrTimeOnly = date('H:i:s', strtotime($row['arrival_time']));
        $row['departure_time'] = $date_go . ' ' . $depTimeOnly;
        $row['arrival_time'] = $date_go . ' ' . $arrTimeOnly;
        $flights[] = $row;
    }
    $stmt->close();
    echo json_encode($flights, JSON_UNESCAPED_UNICODE);

} else {
    $query = trim($_GET['q'] ?? '');
    $airports = [];

    if (strlen($query) >= 2 || empty($query)) {
        $sql = "SELECT id, name_vn, city_vn, code FROM airports
                WHERE name_vn LIKE ? OR city_vn LIKE ? OR code LIKE ?
                ORDER BY city_vn ASC, name_vn ASC";
        $stmt = $conn->prepare($sql);
        $like_query = "%$query%";
        $stmt->bind_param("sss", $like_query, $like_query, $like_query);
        $stmt->execute();
        $result = $stmt->get_result();
        $airports = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    echo json_encode($airports, JSON_UNESCAPED_UNICODE);
}
