<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
include __DIR__ . '/../db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM flights WHERE id = $id");
    $_SESSION['deleted'] = true;
}
header('Location: admin_flights.php');
exit;
