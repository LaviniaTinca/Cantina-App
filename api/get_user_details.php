<?php
include '../config/connection.php';
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}
try {
    if (isset($_POST['id'])) {
        $userId = $_POST['id'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the announcement details as JSON response
        echo json_encode($user);
    } else {
        // If ID is not provided, return an empty response or an error message
        echo json_encode(array('error' => 'ID not provided'));
    }
} catch (PDOException $e) {
    echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
}
