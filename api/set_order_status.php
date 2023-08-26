<?php

// Include the database connection file
include '../config/connection.php';

//for both checkboxes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the announcement ID, status type, and is_set value from the AJAX request
    $orderId = $_POST['id'];
    $statusType = $_POST['status_type']; // 'order_status' or 'payment_status'
    $isSet = $_POST['is_set']; // Value of the checkbox (e.g., 'completed', 'pending', etc.)

    try {
        // Update the status value in the database for the specified order ID
        $stmt = $conn->prepare("UPDATE orders SET $statusType = ? WHERE id = ?");
        $stmt->execute([$isSet, $orderId]);

        if ($statusType === 'order_status') {
            $success_msg[] = "Statusul comenzii a fost modificat!";
        } elseif ($statusType === 'payment_status') {
            $success_msg[] = "Statusul plății comenzii a fost modificat!";
        }

        echo json_encode(array('success' => true));
    } catch (PDOException $e) {
        // Return a JSON response indicating a database error
        echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
        $error_msg[] = 'Database error: ' . $e->getMessage();
    }
} else {
    // Return a JSON response indicating an error for invalid request method
    echo json_encode(array('error' => 'Invalid request method'));
    $error_msg[] = 'Error  Invalid request method';
}
