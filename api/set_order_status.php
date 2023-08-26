<?php
include '../config/connection.php';

//for both checkboxes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the announcement ID, status type, and is_set value from the AJAX request
    $orderId = $_POST['id'];
    $statusType = $_POST['status_type']; // 'order_status' or 'payment_status'
    $isSet = $_POST['is_set']; // Value of the checkbox 

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
        echo json_encode(array('error' => 'Eroare in baza de date: ' . $e->getMessage()));
        $error_msg[] = 'Eroare in baza de date: ' . $e->getMessage();
    }
} else {
    echo json_encode(array('error' => 'Metoda ceruta invalida'));
    $error_msg[] = 'Eroare - metoda ceruta invalida';
}
