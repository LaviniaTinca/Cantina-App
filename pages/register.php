<?php

include("../config/connection.php");
$messages = array();

// Add user to the database
if (isset($_POST['add_user'])) {
  try {
    // Validate input
    if (empty($_POST['add_name'])) {
      $messages[] = "Numele este necesar.";
    } else {
      $add_name = htmlspecialchars($_POST['add_name'], ENT_QUOTES, 'UTF-8');
    }

    if (empty($_POST['add_email'])) {
      $messages[] = "Email-ul este necesar.";
    } else {
      $add_email = filter_var($_POST['add_email'], FILTER_SANITIZE_EMAIL);
      if (!filter_var($add_email, FILTER_VALIDATE_EMAIL)) {
        $messages[] = "Email în format invalid.";
      }
    }

    if (empty($_POST['add_password'])) {
      $messages[] = "Parola este necesară.";
    } elseif (strlen($_POST['add_password']) < 6) {
      $messages[] = "Parola trebuie sa aibă minim 6 caractere, o literă mare, una mică și o cifră.";
    } else {
      $add_password = password_hash($_POST['add_password'], PASSWORD_DEFAULT);
    }

    if (empty($_POST['add_confirm_password'])) {
      $messages[] = "Confirmarea parolei este necesară.";
    } elseif ($_POST['add_confirm_password'] != $_POST['add_password']) {
      $messages[] = "Parolele introduse nu se potrivesc.";
    }

    // Check if the email already exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $add_email]);
    if ($stmt->rowCount() > 0) {
      throw new Exception("Acest email este deja folosit.");
    }
  } catch (PDOException $e) {
    $messages[] = "Eroare: " . $e->getMessage();
  } catch (Exception $e) {
    $messages[] = "Eroare: " . $e->getMessage();
  }

  // Insert user into database
  if (empty($messages)) {
    try {
      $id = unique_id();
      $query = "INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES (?, ?, ?, ?)";
      $stmt = $conn->prepare($query);
      $stmt->execute([$id, $add_name, $add_email, $add_password]);

      header('location: ../admin/admin_users.php');
    } catch (PDOException $e) {
      $messages[] = "Eroare: " . $e->getMessage();
    } catch (Exception $e) {
      $messages[] = "Eroare: " . $e->getMessage();
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="../css/style.css" />
</head>

<body>

  <!-- HEADER SECTION include headerAuth -->
  <section>
    <?php include '../components/headerAuth.php'; ?>
  </section>

  <main class="main" style="margin-top: 100px;">
    <div class="Container">
      <div class="wrapper">

        <h1>ÎNREGISTRARE</h1>
        <?php
        if (isset($messages)) {
          foreach ($messages as $message) {
            echo '
                            <div class="message">
                                <span>' . $message . '</span>
                                <i class = "bi bi-x-circle" onclick="this.parentElement.remove()"></i>
                            </div> 
                        ';
          }
        }
        ?>
        <form class="Form" onsubmit="return validateForm()" action="../pages/register.php" method="post" enctype="multipart/form-data">
          <label for="add-name"><b>Nume:</b></label>
          <input type="text" name="add_name" id="add-name" required>
          <span id="nameError"></span>
          <br>
          <label for="add-email"><b>Email:</b></label>
          <input type="email" name="add_email" id="add-email" required><span id="emailError" class="error"></span>
          <span id="emailError"></span>
          <br>
          <label for="add-password"><b>Parola:</b></label>
          <input type="password" name="add_password" id="add-password" required><span id="passwordError" class="error"></span>
          <br>
          <label for="add-confirm-password"><b>Confirmă Parola:</b></label>
          <input type="password" name="add_confirm_password" id="add-confirm-password" required><span id="confirmPasswordError" class="error"></span>

          <button type="submit" name="add_user" class="auth-button">ÎNREGISTRARE</button>

          <a href="../pages/login.php">
            <h3>Ai deja un cont? Autentifică-te aici</h3>
          </a>
        </form>
      </div>
    </div>
  </main>


  <!-- FOOTER SECTION -->
  <section id="menu">
    <?php include '../components/footer.php'; ?>
  </section>

  <script src="../js/formValidation.js"></script>

</body>

</html>