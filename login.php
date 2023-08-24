<?php
include 'php/connection.php';
session_start();

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  $user_id = '';
}
$messages = array();

//login user HASH PASSWORD
if (isset($_POST['submit'])) {
  $email = $_POST['email'];
  $email = filter_var($email, FILTER_SANITIZE_EMAIL);
  $pass = $_POST['password'];
  $pass = htmlspecialchars($pass, ENT_QUOTES, 'UTF-8');
  try {
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE  email = ?");
    $select_user->execute([$email]);
    $user_data = $select_user->fetch(PDO::FETCH_ASSOC);
    if ($select_user->rowCount() > 0 && password_verify($pass, $user_data['password'])) {
      $_SESSION['user_id'] = $user_data['id'];
      $_SESSION['user_name'] = $user_data['name'];
      $_SESSION['user_email'] = $user_data['email'];
      $_SESSION['user_type'] = $user_data['user_type'];

      $query = "SELECT * FROM `daily_menu` WHERE `date` = CURDATE()";
      $stmt = $conn->prepare($query);
      if ($stmt->execute()) {
        $daily_menu = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the row as an associative array

        if (!$daily_menu) {
          // Meniul zilnic nu este setat, deci coșul de cumpărături trebuie golit
          $query_clear_cart = "DELETE FROM cart WHERE user_id = ?";
          $stmt_clear_cart = $conn->prepare($query_clear_cart);
          if ($stmt_clear_cart->execute([$user_data['id']])) {
          } else {
            // Handle the error
            $error_info = $stmt_clear_cart->errorInfo();
            $error_msg[] = "Eroare : " . $error_info[2];
          }
        }
      } else {
        // Handle the error
        $error_info = $stmt->errorInfo();
        $error_msg = 'Eroare' . $error_info[2];
      }

      header('location: home.php');
    } else {
      $messages[] = 'Email sau parola incorectă!';
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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

  <!-- HEADER SECTION -->
  <section>
    <?php include 'components/headerAuth.php'; ?>
  </section>


  <main class="main">
    <div class="Container">
      <div class="wrapper">
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
        <h1>AUTENTIFICARE</h1>
        <form class="Form" onsubmit="return validateForm()" method="post">
          <label for="email"><b>Email:</b></label><br>
          <input type="email" id="email" name="email"><span id="emailError" class="error"></span><br><br>
          <label for="password"><b>Parola:</b></label><br>
          <input type="password" id="password" name="password">
          <span id="passwordError" class="error"></span><br><br>
          <button type="submit" name="submit" class="auth-button">AUTENTIFICARE</button>
          <a href="register.php">
            <h3>Nu ai cont? Înregistrează-te aici!</h3>
          </a>
        </form>
      </div>
    </div>
  </main>

  <!-- FOOTER SECTION -->
  <section id="menu">
    <?php include 'components/footer.php'; ?>
  </section>

  <script>
    function validateForm() {
      var email = document.getElementById("email").value;
      var password = document.getElementById("password").value;
      var emailError = document.getElementById("emailError");
      var passwordError = document.getElementById("passwordError");

      var emailRegex = /^\S+@\S+\.\S+$/;
      var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/;

      var isValid = true;

      if (!emailRegex.test(email)) {
        emailError.innerHTML = "Adresa de email nu este validă!";
        isValid = false;
      } else {
        emailError.innerHTML = "";
      }

      if (!passwordRegex.test(password)) {
        passwordError.innerHTML = "Parolă nu este validă (minim 6 caractere, cel puțin o litera mare, o literă mică și o cifră!)";
        isValid = false;
      } else {
        passwordError.innerHTML = "";
      }

      return isValid;
    }
  </script>

  <script>
    // Add event listeners to input fields
    var emailInput = document.getElementById("email");
    var passwordInput = document.getElementById("password");

    emailInput.addEventListener("input", validateEmail);
    passwordInput.addEventListener("input", validatePassword);

    function validateEmail() {
      var email = emailInput.value;
      var emailError = document.getElementById("emailError");
      var emailRegex = /^\S+@\S+\.\S+$/;

      if (!emailRegex.test(email)) {
        emailError.innerHTML = "Adresa de email nu este validă!";
        emailInput.classList.remove("valid");
        emailInput.classList.add("error");
      } else {
        emailError.innerHTML = "";
        emailInput.classList.remove("error");
        emailInput.classList.add("valid");
      }
    }

    function validatePassword() {
      var password = passwordInput.value;
      var passwordError = document.getElementById("passwordError");
      var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

      if (!passwordRegex.test(password)) {
        passwordError.innerHTML = "Parolă invalidă (minim 6 caractere, cel puțin o litera mare, o literă mică și o cifră!)";
        passwordInput.classList.remove("valid");
        passwordInput.classList.add("error");
      } else {
        passwordError.innerHTML = "";
        passwordInput.classList.remove("error");
        passwordInput.classList.add("valid");
      }
    }
  </script>

</body>

</html>