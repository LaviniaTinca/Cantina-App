<?php
include 'php/connection.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit(); // Add exit to stop further execution
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: login.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'unknown page';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - not found</title>
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <!--NAVBAR  HEADER SECTION -->
    <section>
        <?php include 'components/header.php'; ?>
    </section>
    <main class="main">
        <!-- NOT FOUND -->
        <div class="Container">
            <div class="not-found">
                <img src="images/not_found.png" alt="dought image">
                <!-- <h1 class="title2">Page "<?php echo $page ?>" is under construction</h1> -->
                <h1 class="title2">Page under construction</h1>
            </div>
        </div>

    </main>
    <!-- FOOTER SECTION -->
    <section>
        <?php include 'components/footer.php'; ?>
    </section>
    <script src="js/script.js"></script>
</body>

</html>