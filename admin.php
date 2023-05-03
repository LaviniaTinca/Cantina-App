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
                        <p class="description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam pretium, leo non eleifend sollicitudin, justo nibh pulvinar eros, nec dapibus nunc enim nec nulla. Aenean vitae varius ex. Quisque at vestibulum elit, ut aliquet ipsum. Duis efficitur suscipit felis, sed pellentesque est fringilla vel. Ut eleifend ligula sit amet nibh tempor aliquet. Proin lacinia, quam vel commodo bibendum, sapien sapien bibendum ante, sed semper tellus arcu id ipsum. Aliquam dignissim, nisl a tempus tristique, velit augue elementum nunc, vel pretium orci sapien eget lacus. Vestibulum ac mauris lectus. Nullam tristique vel augue quis elementum. Donec euismod purus nec libero pretium, in tempor felis pretium. Ut euismod varius aliquet. Duis ut pulvinar velit.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- SCRIPT SECTION -->
    <script src="script.js"></script>

</body>

</html>