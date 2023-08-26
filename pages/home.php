<?php
include '../config/connection.php';
include '../config/session.php';

// Handle email subscription
if (isset($_POST['subscribe-button'])) {
  $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
  $id = unique_id();
  try {
    // Check if email already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM subscribers WHERE email = ?");
    $stmt->execute([$email]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
      // Email already exists
      $warning_msg[] = "Există deja o abonare cu această adresă de email!";
    } else {
      // Email does not exist, insert into database
      $stmt = $conn->prepare("INSERT INTO subscribers (id, email) VALUES (?,?)");
      if ($stmt->execute([$id, $email])) {
        $success_msg[] = "Abonare cu email: " . $email;
      } else {
        $warning_msg[] = "Eroare la abonare: " . $email;
      }
    }
  } catch (PDOException $th) {
    $error_msg = 'Eroare ' . $th->getMessage();
  } catch (Exception $th) {
    $error_msg = 'Eroare' . $th->getMessage();
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
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/slider.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</head>

<body>

  <!--NAVBAR  HEADER SECTION -->
  <section>
    <?php include '../components/header.php'; ?>
  </section>

  <main class="main">

    <!-- SLIDER SECTION -->
    <section>
      <?php include '../components/home/slider.php'; ?>
    </section>


    <!-- //ROTATE ICONS SECTION -->
    <section>
      <?php include '../components/home/rotateIcons.php'; ?>
    </section>

    <!-- MENU SUMMARY SECTION -->
    <section id="menu">
      <?php include '../components/home/menu0.php'; ?>
    </section>

    <!-- ABOUT US SECTION -->
    <section id="about">
      <?php include '../components/home/about.php'; ?>
    </section>

    <!-- newsletter SECTION -->
    <section>
      <?php include '../components/home/newsletter.php'; ?>
    </section>
  </main>

  <!-- FOOTER SECTION -->
  <section>
    <?php include '../components/footer.php'; ?>
  </section>
  <?php include '../components/alert.php'; ?>

  <script src="../js/script.js"></script>
</body>

</html>