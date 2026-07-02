<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

include __DIR__ . '/../db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>
        alert('Thiếu ID khuyến mãi!');
        window.location.href = 'list_promotion.php';
    </script>";
    exit;
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("DELETE FROM promotions WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>
        alert('Đã xóa khuyến mãi thành công!');
        window.location.href = 'list_promotion.php';
    </script>";
} else {
    echo "<script>
        alert('Lỗi khi xóa khuyến mãi!');
        window.location.href = 'list_promotion.php';
    </script>";
}

$stmt->close();
$conn->close();
?>
