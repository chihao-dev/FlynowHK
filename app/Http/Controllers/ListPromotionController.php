<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../../../db_connect.php';

$errors = [];
$success = '';
$airlines = $conn->query("SELECT id, name, logo_url FROM airlines")->fetch_all(MYSQLI_ASSOC);
$airports = $conn->query("SELECT code, name_vn, city_vn FROM airports ORDER BY city_vn ASC")->fetch_all(MYSQLI_ASSOC);

$edit_id = $_GET['id'] ?? null;
$promo = null;
if ($edit_id && is_numeric($edit_id)) {
    $stmt = $conn->prepare("SELECT * FROM promotions WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $promo = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null; 
    $airline_id = $_POST['airline_id'];
    $title = trim($_POST['title']);
    $code = strtoupper(trim($_POST['code']));
    $description = trim($_POST['description']);
    $discount_type = $_POST['discount_type'];
    $discount_value = $_POST['discount_value'];
    $min_tickets = $_POST['min_tickets'] ?: 1;
    $route_from = trim($_POST['route_from']);
    $route_to = trim($_POST['route_to']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $image_url = trim($_POST['image_url']);

    if (!$airline_id) $errors[] = "Vui lòng chọn hãng hàng không.";
    if (!$title) $errors[] = "Tiêu đề không được để trống.";
    if (!$code) $errors[] = "Mã khuyến mãi không được để trống.";
    if (!$discount_value || $discount_value <= 0) $errors[] = "Giá trị giảm phải lớn hơn 0.";
    if ($route_from !== "ALL" && $route_to !== "ALL" && $route_from === $route_to) $errors[] = "Sân bay đi và đến không được giống nhau.";
    if (!$start_date || !$end_date) $errors[] = "Chọn đủ thời gian áp dụng.";

    if (empty($errors)) {
        if ($id) {
            $stmt = $conn->prepare("UPDATE promotions SET airline_id=?, title=?, code=?, description=?, discount_type=?, discount_value=?, min_tickets=?, route_from=?, route_to=?, start_date=?, end_date=?, image_url=? WHERE id=?");
            $stmt->bind_param("issssdisssssi", $airline_id, $title, $code, $description, $discount_type, $discount_value, $min_tickets, $route_from, $route_to, $start_date, $end_date, $image_url, $id);
            $stmt->execute();
            $success = "Khuyến mãi đã được cập nhật!";
            $edit_id = $id;
        } else {
            $stmt = $conn->prepare("INSERT INTO promotions (airline_id, title, code, description, discount_type, discount_value, min_tickets, route_from, route_to, start_date, end_date, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssdisssss", $airline_id, $title, $code, $description, $discount_type, $discount_value, $min_tickets, $route_from, $route_to, $start_date, $end_date, $image_url);
            $stmt->execute();
            $success = "Khuyến mãi đã được tạo!";
        }
    }
}

$filters = [
    'code' => $_GET['code'] ?? '',
    'airline' => $_GET['airline'] ?? '',
    'discount' => $_GET['discount'] ?? '',
    'start' => $_GET['start'] ?? '',
    'end' => $_GET['end'] ?? ''
];

$sql = "SELECT p.*, a.name AS airline_name, a.logo_url FROM promotions p JOIN airlines a ON p.airline_id=a.id WHERE 1=1";
if (!empty($filters['code'])) $sql .= " AND p.code LIKE '%".$conn->real_escape_string($filters['code'])."%'";
if (!empty($filters['airline'])) $sql .= " AND p.airline_id=".intval($filters['airline']);
if (!empty($filters['discount'])) $sql .= " AND p.discount_type='".$conn->real_escape_string($filters['discount'])."'";
if (!empty($filters['start'])) $sql .= " AND p.start_date>='".$conn->real_escape_string($filters['start'])."'";
if (!empty($filters['end'])) $sql .= " AND p.end_date<='".$conn->real_escape_string($filters['end'])."'";
$sql .= " ORDER BY p.created_at DESC";

$promos = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
