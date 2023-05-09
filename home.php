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
if (isset($_POST['email'])) {
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

    <div class="banner">
      <!-- MENU SUMMARY SECTION -->
      <section id="menu">
        <?php include 'components/menu0.php'; ?>
      </section>
    </div>

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

  <script>
    //SLIDER
    let slideIndex = 1;
    showSlides(slideIndex);

    function plusSlides(n) {
      showSlides(slideIndex += n);
    }

    function currentSlide(n) {
      showSlides(slideIndex = n);
    }

    function showSlides(n) {
      let i;
      let slides = document.getElementsByClassName("mySlides");
      let dots = document.getElementsByClassName("dot");
      if (n > slides.length) {
        slideIndex = 1
      }
      if (n < 1) {
        slideIndex = slides.length
      }
      for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
      }
      for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
      }
      slides[slideIndex - 1].style.display = "block";
      dots[slideIndex - 1].className += " active";
    }
  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
  <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

  <script src="script.js"></script>
  <?php include 'components/alert.php'; ?>


</body>

</html>