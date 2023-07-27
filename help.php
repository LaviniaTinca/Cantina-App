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
$current_page = basename($_SERVER['PHP_SELF']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina -admin</title>
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <!-- HEADER SECTION -->
    <section>
        <?php include 'components/admin/header.php'; ?>
    </section>

    <main class="main" style="margin-top: 50px">

        <!-- SIDEBAR AND PANEL-CONTAINER -->
        <section>
            <div class="admin-container">
                <?php include('components/admin/sidebar.php'); ?>

                <div class="panel-container">
                    <div class="banner" style=" height: 100px; color: var(--olive); background: rgba(255, 255, 255, 0.9) url('https://thumbs.dreamstime.com/z/cooking-banner-background-spices-vegetables-top-view-cooking-banner-background-spices-vegetables-top-view-free-168096882.jpg') ; background-size:cover">
                        <h1 style="color:var(--green)">help</h1>
                    </div>
                    <div class="title2">
                        <a href="admin.php">admin </a><span>/ help</span>
                    </div>
                    <div class=" content">
                        <div class="todo" style="color: teal">
                            <h3>GENERAL</h3>
                            <div class="todo-list" style="color: red; margin-left: 1rem">
                                <p> add user -> email already exist</p>
                                <p>page under construction, about us, contact, delete from wishlist -> add red button</p>
                                <p>la add folosesc aceleasi clase peste to: add-product, add-products pt toggle. Nu stiu de ce nu imi ia daca schimb numele, voiam element/s</p>
                                <p>de stilizat sidebar + icons</p>
                                <br>
                                <p>mesaj la subscribe success</p>
                                <p>add product - SELECT CATEGORY / ADD CATEGORY</p>
                                <p>data meniului - la alegerea datei din picker se actualizeaza in link dar la refresh dispare</p>

                            </div>
                            <hr>
                            <h3>PRODUCT SECTION</h3>
                            <div class="todo-list" style="color: red; margin-left: 1rem">
                            </div>
                            <hr>
                            <h3>CUSTOMER SECTION</h3>
                            <div class="todo-list" style="color: red; margin-left: 1rem">

                                <p>toggle reviews nu merge dinamic dupa ce filtrez, desi mergea , dupa filtrare le deschide pe toate</p>
                                <p>you should replace unique_id() with a function that generates a unique identifier for each user. This can be achieved using functions like uniqid() or uuid_create(), depending on your requirements.</p>
                            </div>
                            <hr>
                            <h3>AUTH SECTION</h3>
                            <div class="todo-list" style="color: red; margin-left: 1rem">
                                <p>HASH PASSWORD -> la edit trebuie sa schimb si parola + confirmare ca sa editez</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- //END MAIN -->
    </main>
    <!-- SCRIPT SECTION -->
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script src="script.js"></script>
</body>

</html>