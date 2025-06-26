<?php
include 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemkey = $_POST['itemkey'] ?? '';
    $status = $_POST['status'] ?? '';

    if ($itemkey !== '') {
        // Try to update first
        $stmt = $conn->prepare("UPDATE equipment_item_status SET status = ? WHERE itemkey = ?");
        $stmt->bind_param("ss", $status, $itemkey);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            // No row updated, insert new
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO equipment_item_status (itemkey, status) VALUES (?, ?)");
            $stmt->bind_param("ss", $itemkey, $status);
            $success = $stmt->execute();
            $stmt->close();
        } else {
            $success = true;
            $stmt->close();
        }
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid itemkey']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
