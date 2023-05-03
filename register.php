<?php 
//session_start();

include("php/connection.php");
include("php/functions.php");
    $message = array();

	if($_SERVER['REQUEST_METHOD'] == "POST")
	{
		//something was posted
    $firstName = $_POST['firstName'];
		$lastName = $_POST['lastName'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$isAdmin = 0;

        $query1 = "SELECT * FROM `users` WHERE email = '$email'";
        $find_user = mysqli_query($conn, $query1) or die('query failed');
        
        $message = array();
        if (mysqli_num_rows($find_user) > 0)
        {
            $message[] = 'email already exists';
            // echo "user exist Please enter some valid information!";

        }
        else{
            if(!empty($email) && !empty($password) && !is_numeric($firstName))
            {

                //save to database
                $query2 = "INSERT INTO `users`(`firstName`, `lastName`, `email`,`password`, `isAdmin`) VALUES ('$firstName','$lastName', '$email','$password', '$isAdmin')";

                mysqli_query($conn, $query2) or die('query failed');
                $message[] ='registered successfully';

                header("Location: login.php");
                die;
            }else
            {
                echo "Please enter some valid information!";
            }
	    }
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">

    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-SX9vZZ8x6BG5Uk/5I6El5UvC5U6WBN45zCZHEBn/YeQ2ZDgWnSgS55lPKObaImwL2QvCm4i4h1udm8xgZ9MAmQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css" integrity="sha512-VCZpKng0q/E8xTL0XSDi9rLrYc1Oed8U5CCbU6oNGI6o/+E6M8M6WZ2zJr+YlfUcH6U8W5gJjj6ESHD+LfxdiA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icon@1.9.1/font/bootstrap-icons.css"  />


    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/auth.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>
  <body>

    <!-- <header class="Navbar">
      <div class="logo logo-image"></div>
      <a href="index.php">Cantina</a>
      <ul>
        <li><a href="login.php">Login</a></li>
        <li><a class="active" href="register.php">Register</a></li>
      </ul>
    </header> -->

    <!-- <div class="Container">
      <div class="wrapper">

        <h1>REGISTER</h1>
        <form class="Form" onsubmit="return validateForm()" method="post">
          <label for="firstName"><b>First Name:</b></label
          ><br />
          <input type="text" id="firstName" name="firstName" /><span
            id="firstNameError"
            class="error"
          ></span
          ><br /><br />
          <label for="lastName"><b>Last Name:</b></label
          ><br />
          <input type="text" id="lastName" name="lastName" /><span
            id="lastNameError"
            class="error"
          ></span
          ><br /><br />
          <label for="email"><b>Email:</b></label
          ><br />
          <input type="email" id="email" name="email" /><span
            id="emailError"
            class="error"
          ></span
          ><br /><br />
          <label for="password"><b>Password:</b></label
          ><br />
          <input type="password" id="password" name="password" /><span
            id="passwordError"
            class="error"
          ></span
          ><br /><br />
          <label for="country"><b>Country:</b></label
          ><br />
          <select id="country" name="country" onchange="toggleFacultyList()">
            <option value="employee">Romania</option>
            <option value="student">Serbia</option></select
          ><br /><br />
          <div id="facultyList" style="display: none">
            <label for="city"><b>City:</b></label
            ><br />
            <select id="city" name="city">
              <option value="engineering">Kikinda</option>
              <option value="science">Vrset</option>
              
            </select
            ><br /><br />
          </div>
          <div id="departmentList" style="display: none">
            <label for="city"><b>City:</b></label
            ><br />
            <select id="city" name="city">
              <option value="engineering">Jimbolia</option>
              <option value="science">Moravita</option>
 
            </select
            ><br /><br />
          </div>
          <button type="submit">REGISTER</button>
          <a href="login2.php"
            ><h3>Already have an account? Sign In here</h3></a
          >
        </form>
      </div>
    </div> -->
        <!-- HEADER SECTION include headerAuth -->
        <section>
      <?php include 'components/headerAuth.php'; ?>
    </section>
<div class="main" style="margin-top: 0px;">


    <div class="Container">
        <div class="wrapper">

            <h1>REGISTER</h1>
            <?php
                if (isset($message))
                {
                    foreach ($message as $message)
                    {
                        echo '
                            <div class="message">
                                <span>'.$message.'</span>
                                <i class = "bi bi-x-circle" onclick="this.parentElement.remove()"></i>
                            </div> 
                        ';
                    }
                }
            ?>
            <form class="Form" onsubmit="return validateForm()" method="post">
            <div>
                <label for="firstName"><b>First Name:</b></label><br />
                <input type="text" id="firstName" name="firstName" />
                <span id="firstNameError" class="error"></span><br /><br />
                <label for="lastName"><b>Last Name:</b></label><br />
                <input type="text" id="lastName" name="lastName" />
                <span id="lastNameError" class="error"></span><br /><br />
            </div>
            <div>
                <label for="email"><b>Email:</b></label><br />
                <input type="email" id="email" name="email" />
                <span id="emailError" class="error"></span><br /><br />
                <label for="password"><b>Password:</b></label><br />
                <input type="password" id="password" name="password" />
                <span id="passwordError" class="error"></span><br /><br />
            </div>
            <button type="submit">REGISTER</button>
            <a href="login.php"><h3>Already have an account? Sign In here</h3></a>
            </form>
        </div>
    </div>


    <!-- FOOTER SECTION -->
    <section id = "menu">
      <?php include 'components/footer.php'; ?>
    </section> 

    </div>

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
    </script>
    <script src="formValidation.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.min.js" integrity="sha512-8Zb//S7l1Dwomj0oWwnveUc4I88zF6vnj1x68TY8aQYwsYqloIqocwOJaxW8/uRyM0oH5D5GX5Db5W1gRJZfIQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js" integrity="sha512-NsKjivVh27/rMnIBmVXpZof+FJNSPG40gysJLdtDR1iVJ1tQqxGtOuOJfX9tVetPfYefit7Vch1wQGh7VbhUpg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  </body>
</html>
