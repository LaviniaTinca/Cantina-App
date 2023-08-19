<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (!isset($_SESSION['user_id'])) {
    header('location:../login.php');
    exit(); // Add exit to stop further execution
}

if ($_SESSION['user_type'] === 'user') {
    header('location:../home.php');
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: ../login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
$messages = array();


// List of pages that don't exist yet
$not_found_pages = array(
    '/wishlist.php',
);

// Get the requested page from the URL
$request_uri = $_SERVER['REQUEST_URI'];

// Check if the requested page is in the "not found" pages array
if (in_array($request_uri, $not_found_pages)) {
    // Redirect to the custom "not found" page
    header('Location: ../not_found.php');
    exit;
}
// // Get the requested page from the URL
// $request_uri = $_SERVER['REQUEST_URI'];

// // Check if the requested page or resource exists
// if (!file_exists($request_uri)) {
// 	// Redirect to the custom "not found" page
// 	header('Location: not_found.php');
// 	exit;
// }


// List of pages that don't exist yet
$not_found_pages = array(
    '/wishlist.php',
);

// Get the requested page from the URL
$request_uri = $_SERVER['REQUEST_URI'];

// Check if the requested page is in the "not found" pages array
if (in_array($request_uri, $not_found_pages)) {
    // Redirect to the custom "not found" page
    header('Location: not_found.php');
    exit;
}
