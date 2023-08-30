<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
$messages = array();


// List of pages that exist
$pages_list = array(
    'cart.php',
    'contact.php',
    'home.php',
    'login.php',
    'logout.php',
    'register.php',
    'view_item.php',
    'view_menu.php',
    'view_order.php',
    'view_orders.php'
);
$requested_page = $current_page;

if (!in_array($requested_page, $pages_list)) {
    // Page is not found in the list, redirect to not_found.php
    header('Location: not_found.php');
    exit();
}
