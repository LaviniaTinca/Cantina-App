<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (!isset($_SESSION['user_id'])) {
    header('location:../pages/login.php');
    exit(); // Add exit to stop further execution
}

if ($_SESSION['user_type'] === 'user') {
    header('location:../pages/home.php');
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: ../pages/login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
$messages = array();
