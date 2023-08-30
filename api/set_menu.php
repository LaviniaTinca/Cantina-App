<?php
include '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menuID = $_POST['id'];
    $isSet = $_POST['is_set'];

    try {
        $stmt = $conn->prepare("UPDATE daily_menu SET special_note = ? WHERE id = ?");
        $stmt->execute([$isSet, $menuID]);

        if ($stmt->rowCount() > 0) {
            $stmt = $conn->prepare("UPDATE daily_menu SET special_note = 0 WHERE id != ?");
            $stmt->execute([$menuID]);
            $success_msg[] = "Meniul zilei a fost setat!";

            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('error' => 'Meniul nu a fost gasit si nu s-au facut modificari'));
            $warning_msg[] = "Nu s-a putut efectua setarea!";
        }
    } catch (PDOException $e) {
        echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
    }
} else {
    // Return a JSON response indicating an error for invalid request method
    echo json_encode(array('error' => 'Invalid request method'));
}
