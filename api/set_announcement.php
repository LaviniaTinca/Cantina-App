<?php
include '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $announcementId = $_POST['id'];
    $isSet = $_POST['is_set'];

    try {
        // Update the is_set value in the database for the specified announcement ID
        $stmt = $conn->prepare("UPDATE announcements SET is_set = ? WHERE id = ?");
        $stmt->execute([$isSet, $announcementId]);

        if ($stmt->rowCount() > 0) {
            $stmt = $conn->prepare("UPDATE announcements SET is_set = 0 WHERE id != ?");
            $stmt->execute([$announcementId]);
            $success_msg[] = "Anunțul a fost setat!";

            // Return a JSON response indicating success
            echo json_encode(array('success' => true));
        } else {
            // Return a JSON response indicating failure
            echo json_encode(array('error' => 'Anuntul nu a fost gasit si nu s-au facut modificari'));
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
