<?php
// Bootstrap Laravel to use its configuration and helpers
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$servername = config('database.connections.mysql.host');
$username = config('database.connections.mysql.username');
$password = config('database.connections.mysql.password');
$dbname = config('database.connections.mysql.database');
$port = config('database.connections.mysql.port', '3306');

$conn = new mysqli($servername, $username, $password, $dbname, (int) $port);

if ($conn->connect_error) {
    die("Kết nối database thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
