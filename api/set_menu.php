<?php
include '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menuID = $_POST['id'];
    $isSet = $_POST['is_set'];

    try {
        $stmt = $conn->prepare("UPDATE daily_menu SET is_set = ? WHERE id = ?");
        $stmt->execute([$isSet, $menuID]);

        if ($stmt->rowCount() > 0) {
            $stmt = $conn->prepare("UPDATE daily_menu SET is_set = 0 WHERE id != ?");
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
    echo json_encode(array('error' => 'Invalid request method'));
}
