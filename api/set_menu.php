<?php
include '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menuID = $_POST['id'];
    $isSet = $_POST['is_set'];

    try {
        // Update the is_set value in the database for the specified  ID
        $stmt = $conn->prepare("UPDATE daily_menu SET special_note = ? WHERE id = ?");
        $stmt->execute([$isSet, $menuID]);

        if ($stmt->rowCount() > 0) {
            $stmt = $conn->prepare("UPDATE daily_menu SET special_note = 0 WHERE id != ?");
            $stmt->execute([$menuID]);
            $success_msg[] = "Meniul zilei a fost setat!";

            // Return a JSON response indicating success
            echo json_encode(array('success' => true));
        } else {
            // Return a JSON response indicating failure
            echo json_encode(array('error' => 'Meniul nu a fost gasit si nu s-au facut modificari'));
            $warning_msg[] = "Nu s-a putut efectua setarea!";
        }
    } catch (PDOException $e) {
        // Return a JSON response indicating a database error
        echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
    }
} else {
    // Return a JSON response indicating an error for invalid request method
    echo json_encode(array('error' => 'Invalid request method'));
}
