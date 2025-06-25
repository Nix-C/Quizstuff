<?php
include 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE equipment_registration SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $success = $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
