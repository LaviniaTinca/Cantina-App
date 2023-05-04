<?php
include 'php/connection.php';
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <!-- <link rel="stylesheet" href="css/styleMainAdmin.css"> -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <title>Cantina - admin</title>
</head>

<body>
    <!-- HEADER SECTION -->
    <section>
        <?php include 'components/admin/header.php'; ?>
    </section>

    <div class="main" style="margin-top: 50px">
        <!-- BANNER SECTION -->
        <!-- <section>
            <div class="banner">
                <div class="banner-container">
                    <img class="banner-img" src="public/assets/banner1.png" alt="banner">
                    <div class="title2">
                        <a href="admin.php">admin </a><span>/ dashboard</span>
                    </div>
                </div>
            </div>
        </section> -->

        <!-- SIDEBAR AND PANEL-CONTAINER -->
        <section>
            <div class="admin-container">
                <?php include('components/admin/sidebar.php'); ?>
                <div class="panel-container">
                    <div class="title2">
                        <a href="admin.php" style="color: var(--green);">admin </a><span>/ dashboard</span>
                    </div>
                    <div class="content">
                        <div class="todo" style="color: teal">
                            <h3>PRODUCT SECTION</h3>
                            <div class="todo-list" style="color: red; margin-left: 1rem">
                                <p>de stilizat formularele pt add si edit product</p>
                                <p>de stilizat sidebar + icons</p>
                            </div>
                            <hr>
                            <h3>CUSTOMER SECTION</h3>
                            <div class="todo-list" style="color: red; margin-left: 1rem">
                                <p>REFORMATARE</p>

                            </div>
                            <hr>
                            <h3>AUTH SECTION</h3>
                            <div class="todo-list" style="color: red; margin-left: 1rem">
                                <p>register reformatare</p>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- SCRIPT SECTION -->
    <script src="script.js"></script>

</body>

</html>