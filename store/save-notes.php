<?php
include 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemkey = $_POST['itemkey'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if ($itemkey !== '') {
        // Try to update first
        $stmt = $conn->prepare("UPDATE equipment_item_status SET notes = ? WHERE itemkey = ?");
        $stmt->bind_param("ss", $notes, $itemkey);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            // No row updated, insert new (with blank status)
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO equipment_item_status (itemkey, notes) VALUES (?, ?)");
            $stmt->bind_param("ss", $itemkey, $notes);
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
