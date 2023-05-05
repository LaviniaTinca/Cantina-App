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

  <title>Cantina</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/slider.css">
  <link rel="stylesheet" href="css/header.css">

  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>

  <!-- HEADER SECTION -->
  <section>
    <?php include 'components/header.php'; ?>
  </section>

  <!-- <div class="main" style=" margin-top: 80px;"> -->
  <div class="main" style=" margin-top: 100px;">

    <!-- SLIDER SECTION -->
    <section>
      <?php include 'components/slider.php'; ?>
    </section>


    <!-- ICONS SECTION -->
    <section id="icons" style="margin-top: 30px;">
      <?php include 'components/icons.php'; ?>
    </section>

    <div class="banner" style="background: rgba(255, 255, 255, 0.9) url('https://thumbs.dreamstime.com/z/cooking-banner-background-spices-vegetables-top-view-cooking-banner-background-spices-vegetables-top-view-free-168096882.jpg') ; background-size:cover">
      <!-- MENU SUMMARY SECTION -->
      <section id="menu">
        <?php include 'components/menu0.php'; ?>
      </section>
    </div>

    <!-- ABOUT US SECTION -->
    <section id="about" style="margin-top: 30px;">
      <?php include 'components/about.php'; ?>
    </section>

    <!-- newsletter SECTION -->
    <section>
      <?php include 'components/newsletter.php'; ?>
    </section>
  </div>

  <!-- FOOTER SECTION -->
  <section id="menu">
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
  <script>
    // //RUN TO SECTION
    // document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    //   anchor.addEventListener('click', function(e) {
    //     e.preventDefault();
    //     document.querySelector(this.getAttribute('href')).scrollIntoView({
    //       behavior: 'smooth'
    //     });
    //   });
    // });
  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
  <script src="script.js"></script>
  <?php include 'components/alert.php'; ?>


</body>

</html>