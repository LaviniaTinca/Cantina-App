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

// Handle email subscription
if (isset($_POST['subscribe-button'])) {
  $email = $_POST['email'];
  $id = unique_id();

  // Check if email already exists
  $stmt = $conn->prepare("SELECT COUNT(*) FROM subscribers WHERE email = ?");
  $stmt->execute([$email]);
  $count = $stmt->fetchColumn();

  if ($count > 0) {
    // Email already exists
    $warning_msg[] = "Email address is already subscribed.";
  } else {
    // Email does not exist, insert into database
    $stmt = $conn->prepare("INSERT INTO subscribers (id, email) VALUES (?,?)");
    if ($stmt->execute([$id, $email])) {
      $success_msg[] = "Subscribed with email: " . $email;
    } else {
      $warning_msg[] = "Error subscribing with email: " . $email;
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cantina</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/slider.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

  <style>
    /* Stilizare generală a containerului galeriei */
    .gallery {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin: 20px;
    }

    /* Stilizare slider mic */
    .small-slider {
      width: 100%;
      max-width: 800px;
      /* Ajustează dimensiunea maximă */
      margin: 0 auto;
    }

    /* Stilizare imagini din slider */
    .small-slider img {
      width: 100%;
      height: auto;
      cursor: pointer;
    }

    /* Stilizare fundal lumină de fundal pentru mărire */
    .lightbox {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.9);
      justify-content: center;
      align-items: center;
      z-index: 999;
    }

    /* Stilizare imagine mărită */
    .lightbox img {
      max-width: 60%;
      max-height: auto;
      display: block;
      border: 2px solid #fff;
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.7);
      margin: 4rem auto;
      /* Center the image horizontally */

    }
  </style>
</head>

<body>

  <!--NAVBAR  HEADER SECTION -->
  <section>
    <?php include 'components/header.php'; ?>
  </section>

  <main class="main">

    <!-- SLIDER SECTION -->
    <section>
      <?php include 'components/slider.php'; ?>
    </section>


    <!-- //ROTATE ICONS SECTION -->
    <section>
      <?php include 'components/rotateIcons.php'; ?>
    </section>

    <!-- <div class="banner"> -->
    <!-- MENU SUMMARY SECTION -->
    <section id="menu">
      <?php include 'components/menu0.php'; ?>
    </section>
    <!-- </div> -->

    <!-- ABOUT US SECTION -->
    <section id="about">
      <?php include 'components/about.php'; ?>
    </section>

    <!-- newsletter SECTION -->
    <section>
      <?php include 'components/newsletter.php'; ?>
    </section>
  </main>

  <!-- FOOTER SECTION -->
  <section>
    <?php include 'components/footer.php'; ?>
  </section>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
  <!-- <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script> -->

  <script src="script.js"></script>
  <?php include 'components/alert.php'; ?>


</body>

</html>