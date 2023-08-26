<?php
include '../config/connection.php';

try {
    if (isset($_POST['id'])) {
        $announcementId = $_POST['id'];
        // Prepare the query
        $stmt = $conn->prepare("SELECT * FROM announcements WHERE id = :id");
        $stmt->bindParam(':id', $announcementId);
        $stmt->execute();

        $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the announcement details as JSON response
        echo json_encode($announcement);
    } else {
        // If ID is not provided, return an empty response or an error message
        echo json_encode(array('error' => 'ID not provided'));
    }
} catch (PDOException $e) {
    echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
}
