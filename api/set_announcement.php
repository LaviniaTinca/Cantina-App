<?php
include '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $announcementId = $_POST['id'];
    $isSet = $_POST['is_set'];

    try {
        // Update the is_set value 
        $stmt = $conn->prepare("UPDATE announcements SET is_set = ? WHERE id = ?");
        $stmt->execute([$isSet, $announcementId]);

        if ($stmt->rowCount() > 0) {
            $stmt = $conn->prepare("UPDATE announcements SET is_set = 0 WHERE id != ?");
            $stmt->execute([$announcementId]);
            $success_msg[] = "Anunțul a fost setat!";

            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('error' => 'Anuntul nu a fost gasit si nu s-au facut modificari'));
            $warning_msg[] = "Nu s-a putut efectua setarea!";
        }
    } catch (PDOException $e) {
        echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
    }
} else {
    echo json_encode(array('error' => 'Invalid request method'));
}
