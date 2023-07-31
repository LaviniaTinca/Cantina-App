<?php
include '../php/connection.php';
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}
try {
    // Check if the ID is provided through POST request
    if (isset($_POST['id'])) {
        $userId = $_POST['id'];
        // Prepare the query
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        // Fetch the announcement details
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the announcement details as JSON response
        echo json_encode($user);
    } else {
        // If ID is not provided, return an empty response or an error message
        echo json_encode(array('error' => 'ID not provided'));
    }
} catch (PDOException $e) {
    // Handle any errors that may occur during the database query
    echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
}
