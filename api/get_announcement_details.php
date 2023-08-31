<?php
include '../config/connection.php';

try {
    if (isset($_POST['id'])) {
        $announcementId = $_POST['id'];
        $stmt = $conn->prepare("SELECT * FROM announcements WHERE id = :id");
        $stmt->bindParam(':id', $announcementId);
        $stmt->execute();
        $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($announcement);
    } else {
        echo json_encode(array('error' => 'ID not provided'));
    }
} catch (PDOException $e) {
    echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
}
