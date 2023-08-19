<?php

include("php/connection.php");
$messages = array();

//add user //!!!!! good for register but without IMAGE
// Add user to the database
if (isset($_POST['add_user'])) {
  try {
    //code...

    // Validate input
    if (empty($_POST['add_name'])) {
      $messages[] = "Name is required.";
    } else {
      $add_name = htmlspecialchars($_POST['add_name'], ENT_QUOTES, 'UTF-8');
    }

    if (empty($_POST['add_email'])) {
      $messages[] = "Email is required.";
    } else {
      $add_email = filter_var($_POST['add_email'], FILTER_SANITIZE_EMAIL);
      if (!filter_var($add_email, FILTER_VALIDATE_EMAIL)) {
        $messages[] = "Invalid email format.";
      }
    }

    if (empty($_POST['add_password'])) {
      $messages[] = "Password is required.";
    } elseif (strlen($_POST['add_password']) < 6) {
      $messages[] = "Password must be at least 6 characters long.";
    } else {
      $add_password = password_hash($_POST['add_password'], PASSWORD_DEFAULT);
    }

    if (empty($_POST['add_confirm_password'])) {
      $messages[] = "Confirm password is required.";
    } elseif ($_POST['add_confirm_password'] != $_POST['add_password']) {
      $messages[] = "Passwords do not match.";
    }

    // Check if the email already exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $add_email]);
    if ($stmt->rowCount() > 0) {
      // $messages[] = "Email-ul exista deja";

      throw new Exception("Email is already taken.");
    }
  } catch (PDOException $e) {
    echo $e;
  } catch (Exception $e) {

    // Display the error message
    echo "Error at register: " . $e->getMessage();
    $messages[] = "Error at register: " . $e->getMessage();
  }

  // Insert user into database
  if (empty($messages)) {
    try {
      $conn->beginTransaction();

      $id = unique_id();
      $query = "INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES (?, ?, ?, ?)";
      $stmt = $conn->prepare($query);
      $stmt->execute([$id, $add_name, $add_email, $add_password]);

      $conn->commit();
      header('location: admin/admin_users.php');
    } catch (PDOException $e) {
      $conn->rollback();
      echo "Error adding user: " . $e->getMessage();
      $messages[] = "Error: " . $e->getMessage();
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


  <link rel="stylesheet" href="css/style.css" />
</head>

<body>

  <!-- HEADER SECTION include headerAuth -->
  <section>
    <?php include 'components/headerAuth.php'; ?>
  </section>

  <main class="main" style="margin-top: 100px;">
    <div class="Container">
      <div class="wrapper">

        <h1>REGISTER</h1>
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
        <form class="Form" onsubmit="return validateForm()" action="register.php" method="post" enctype="multipart/form-data">
          <label for="add-name"><b>Name:</b></label>
          <input type="text" name="add_name" id="add-name" required>
          <span id="nameError"></span>
          <br>
          <label for="add-email"><b>Email:</b></label>
          <input type="email" name="add_email" id="add-email" required><span id="emailError" class="error"></span>
          <span id="emailError"></span>
          <br>
          <label for="add-password"><b>Password:</b></label>
          <input type="password" name="add_password" id="add-password" required><span id="passwordError" class="error"></span>
          <br>
          <label for="add-confirm-password"><b>Confirm Password:</b></label>
          <input type="password" name="add_confirm_password" id="add-confirm-password" required><span id="confirmPasswordError" class="error"></span>

          <button type="submit" name="add_user" class="auth-button">REGISTER</button>

          <a href="login.php">
            <h3>Already have an account? Sign In here</h3>
          </a>
        </form>
      </div>
    </div>
  </main>


  <!-- FOOTER SECTION -->
  <section id="menu">
    <?php include 'components/footer.php'; ?>
  </section>

  <script src="js/formValidation.js"></script>

</body>

</html>