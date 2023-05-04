<?php

include("php/connection.php");
$messages = array();

//add user //!!!!! good for register but without IMAGE
// Add user to the database
if (isset($_POST['add_user'])) {
  // Validate input
  if (empty($_POST['add_name'])) {
    $messages[] = "Name is required.";
  } else {
    $add_name = filter_var($_POST['add_name'], FILTER_SANITIZE_STRING);
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

  // Insert user into database
  if (empty($messages)) {
    try {
      $conn->beginTransaction();

      $id = unique_id();
      $query = "INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES (?, ?, ?, ?)";
      $stmt = $conn->prepare($query);
      $stmt->execute([$id, $add_name, $add_email, $add_password]);

      $conn->commit();
      header('location: admin_users.php');
    } catch (PDOException $e) {
      $conn->rollback();
      echo "Error adding user: " . $e->getMessage();
    }
  } else {
    // Display errors
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-SX9vZZ8x6BG5Uk/5I6El5UvC5U6WBN45zCZHEBn/YeQ2ZDgWnSgS55lPKObaImwL2QvCm4i4h1udm8xgZ9MAmQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css" integrity="sha512-VCZpKng0q/E8xTL0XSDi9rLrYc1Oed8U5CCbU6oNGI6o/+E6M8M6WZ2zJr+YlfUcH6U8W5gJjj6ESHD+LfxdiA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icon@1.9.1/font/bootstrap-icons.css" /> -->


  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/auth.css" />
  <title>Register</title>

</head>

<body>

  <!-- HEADER SECTION include headerAuth -->
  <section>
    <?php include 'components/headerAuth.php'; ?>
  </section>
  <div class="main" style="margin-top: 30px;">


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
          <label for="add-name">Name:</label>
          <input type="text" name="add_name" id="add-name" required>
          <span id="nameError"></span>

          <label for="add-email">Email:</label>
          <input type="email" name="add_email" id="add-email" required><span id="emailError" class="error"></span>
          <span id="emailError"></span>

          <label for="add-password">Password:</label>
          <div class="password-container">
            <input type="password" name="add_password" id="add-password" value="" required><span id="passwordError" class="error"></span>
            <button type="button" id="show-password-btn" onclick="togglePasswordVisibility('add-password', 'show-password-btn')"><box-icon name='low-vision'></box-icon></button>
          </div>
          <span id="passwordError"></span>

          <label for="add-confirm-password">Confirm Password:</label>
          <div class="password-container">
            <input type="password" name="add_confirm_password" id="add-confirm-password" value="" required><span id="confirmPasswordError" class="error"></span>
            <button type="button" id="show-confirm-password-btn" onclick="togglePasswordVisibility('add-confirm-password', 'show-confirm-password-btn')"><box-icon name='low-vision'></box-icon></button>
          </div>
          <span id="confirmPasswordError"></span>

          <!-- <label for="add-password">Password:</label>
          <input type="password" name="add_password" id="add-password" required><span id="passwordError" class="error"></span>
          <span id="passwordError"></span>

          <label for="add-confirm-password">Confirm Password:</label>
          <input type="password" name="add_confirm_password" id="add-confirm-password" required><span id="confirmPasswordError" class="error"></span>
          <span id="confirmPasswordError"></span>

          <input type="submit" name="add_user" value="Add User"> -->
          <button type="submit" name="add_user">REGISTER</button>
          <a href="login.php">
            <h3>Already have an account? Sign In here</h3>
          </a>
        </form>

      </div>
    </div>


    <!-- FOOTER SECTION -->
    <section id="menu">
      <?php include 'components/footer.php'; ?>
    </section>

  </div>
  <!-- //toggle faculty list
  <script>
    function toggleFacultyList() {
      var facultyList = document.getElementById("facultyList");
      var userType = document.getElementById("userType").value;
      if (userType == "student") {
        facultyList.style.display = "block";
        departmentList.style.display = "none";
      } else {
        facultyList.style.display = "none";
        departmentList.style.display = "block";
      }
    }
  </script> -->
  <!-- 
  <script>
    function validateForm() {
      var name = document.getElementById("add-name").value;
      var email = document.getElementById("add-email").value;
      var password = document.getElementById("add-password").value;
      var confirmPassword = document.getElementById("add-confirm-password").value;
      var nameError = document.getElementById("nameError");
      var emailError = document.getElementById("emailError");
      var passwordError = document.getElementById("passwordError");
      var confirmPasswordError = document.getElementById("confirmPasswordError");

      var emailRegex = /^\S+@\S+\.\S+$/;
      var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

      var isValid = true;

      if (name.trim() === "") {
        nameError.innerHTML = "Please enter your name";
        isValid = false;
      } else {
        nameError.innerHTML = "";
      }

      if (!emailRegex.test(email)) {
        emailError.innerHTML = "Invalid email address";
        isValid = false;
      } else {
        emailError.innerHTML = "";
      }

      if (!passwordRegex.test(password)) {
        passwordError.innerHTML = "Invalid password (minimum 8 characters, at least one uppercase letter, one lowercase letter, and one number)";
        isValid = false;
      } else {
        passwordError.innerHTML = "";
      }

      if (password !== confirmPassword) {
        confirmPasswordError.innerHTML = "Passwords do not match";
        isValid = false;
      } else {
        confirmPasswordError.innerHTML = "";
      }

      return isValid;
    }
  </script> -->
  <!-- <script>
    var emailInput = document.getElementById("add-email");
    var passwordInput = document.getElementById("add-password");
    var confirmPasswordInput = document.getElementById("add-confirm-password");

    emailInput.addEventListener("input", validateEmail);
    passwordInput.addEventListener("input", validatePassword);
    confirmPasswordInput.addEventListener("input", validateConfirmPassword);

    function validateEmail() {
      var email = emailInput.value;
      var emailError = document.getElementById("emailError");
      var emailRegex = /^\S+@\S+\.\S+$/;

      if (!emailRegex.test(email)) {
        emailError.innerHTML = "Invalid email address";
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
        passwordError.innerHTML = "Invalid password (minimum 8 characters, at least one uppercase letter, one lowercase letter, and one number)";
        passwordInput.classList.remove("valid");
        passwordInput.classList.add("error");
      } else {
        passwordError.innerHTML = "";
        passwordInput.classList.remove("error");
        passwordInput.classList.add("valid");
      }
    }

    function validateConfirmPassword() {
      var password = confirmPasswordInput.value;
      var passwordError = document.getElementById("confirmPasswordError");
      var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

      if (!passwordRegex.test(password)) {
        passwordError.innerHTML = "Invalid password (minimum 8 characters, at least one uppercase letter, one lowercase letter, and one number)";
        confirmPasswordInput.classList.remove("valid");
        confirmPasswordInput.classList.add("error");
      } else if (password !== passwordInput.value) {
        passwordError.innerHTML = "Passwords do not match";
        confirmPasswordInput.classList.remove("valid");
        confirmPasswordInput.classList.add("error");
      } else {
        passwordError.innerHTML = "";
        confirmPasswordInput.classList.remove("error");
        confirmPasswordInput.classList.add("valid");
      }
    }
  </script> -->

  <script src="formValidation.js"></script>
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.min.js" integrity="sha512-8Zb//S7l1Dwomj0oWwnveUc4I88zF6vnj1x68TY8aQYwsYqloIqocwOJaxW8/uRyM0oH5D5GX5Db5W1gRJZfIQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js" integrity="sha512-NsKjivVh27/rMnIBmVXpZof+FJNSPG40gysJLdtDR1iVJ1tQqxGtOuOJfX9tVetPfYefit7Vch1wQGh7VbhUpg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
  <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

</body>

</html>