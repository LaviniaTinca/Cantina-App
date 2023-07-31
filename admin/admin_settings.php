<?php
include '../php/connection.php';
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (!isset($_SESSION['user_id'])) {
    header('location:../login.php');
}

if ($_SESSION['user_type'] === 'user') {
    header('location:../home.php');
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: ../login.php");
}
$current_page = basename($_SERVER['PHP_SELF']);



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    >
</head>

<body>
    <!-- HEADER SECTION -->
    <section>
        <?php include('../components/admin/header.php'); ?>

    </section>

    <main class="main" style="margin-top: 50px">

        <!-- SIDEBAR AND PANEL-CONTAINER -->
        <section>
            <div class="admin-container">
                <?php include('../components/admin/sidebar.php'); ?>

                <div class="panel-container">

                    <div class=" content">
                        <!-- WIDGETS -->
                        <section class="widgets">
                            <div class="widget settings-widget">
                                <div class="flex">
                                    <div class="small-widget">
                                        <i class='bx bx-cog'></i>
                                    </div>
                                    <h3 style="color: var(--cart);"> Settings</h3>
                                </div>

                                <div class="widget user-widget jump">
                                    <div class="flex">
                                        <div class="small-widget">
                                            <i class='bx bx-news'></i>
                                        </div>
                                        <h4>manage pages</h4>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <!--Add New Announcement Modal box -->


                        <!-- Edit Announcement Modal Box -->



                    </div>

                </div>
            </div>
            </div>
        </section>
        <!-- //END MAIN -->
    </main>

    <!-- SCRIPT SECTION -->

    <script src="../script.js"></script>
    <script src="../js/searchCard.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <?php include '../components/alert.php'; ?>

</body>

</html>