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
$current_page = basename($_SERVER['PHP_SELF']);
$messages = array();


//update and keep old image if necessary
if (isset($_POST['update_user2'])) {
    $update_id = $_POST['update_id'];
    $update_firstName = $_POST['update_firstName'];
    $update_lastName = $_POST['update_lastName'];
    $update_email = $_POST['update_email'];
    $update_password = $_POST['update_password'];
    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];

    try {
        $conn->beginTransaction();

        if (!empty($update_image)) {
            $update_image_folder = 'image/' . $update_image;
            move_uploaded_file($update_image_tmp_name, $update_image_folder);
            $query = "UPDATE `users` SET `firstName`=?, `lastName`=?, `email`=?, `password`=?, `image`=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$update_firstName, $update_lastName, $update_email, $update_password, $update_image, $update_id]);

            // Delete the old image
            $old_image = $_POST['old_image'];
            if (!empty($old_image)) {
                unlink('image/' . $old_image);
            }
        } else {
            $query = "UPDATE `users` SET `firstName`=?, `lastName`=?, `email`=?, `password`=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$update_firstName, $update_lastName, $update_email, $update_password, $update_id]);
        }

        $conn->commit();
        header('location: admin_users.php');
    } catch (PDOException $e) {
        $conn->rollback();
        $messages[] = "Error at update: " . $e->getMessage();

        echo "Error: " . $e->getMessage();
    }
}

//EFIT USER SECTION GOOD
if (isset($_POST['update_user'])) {
    try {
        // Connect to the database
        // Begin a transaction
        $conn->beginTransaction();

        // Get the values from the form
        $id = $_POST['update_id'];
        $name = $_POST['add_name'];
        $email = $_POST['add_email'];
        $password = $_POST['add_password'];
        $confirm_password = $_POST['add_confirm_password'];
        $user_type = "";

        // validate user type field
        if (empty($_POST["user_type"])) {
            $user_type_err = "User type is required";
        } else {
            $user_type = test_input($_POST["user_type"]);
        }

        // Check if the passwords match
        if ($password != $confirm_password) {
            throw new Exception("Passwords do not match.");
        }

        // Check if the email already exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND id != :id");
        $stmt->execute(['email' => $email, 'id' => $id]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("Email is already taken.");
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update the user in the database
        $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email, password = :password, user_type=:user_type WHERE id = :id");
        $stmt->execute(['name' => $name, 'email' => $email, 'password' => $hashed_password, 'user_type' => $user_type, 'id' => $id]);

        // Commit the transaction
        $conn->commit();

        // Redirect to the user list page
        header("Location: admin_users.php");
    } catch (Exception $e) {
        // Roll back the transaction
        $conn->rollBack();

        // Display the error message
        echo "Error at update: " . $e->getMessage();
        $messages[] = "Error at update: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <title>Cantina - admin</title>
</head>

<body>
    <!-- HEADER SECTION -->
    <section>
        <?php include 'components/admin/header.php'; ?>
    </section>

    <div class="main" style="margin-top: 50px; ">
        <!-- SIDEBAR AND PANEL-CONTAINER  SECTION-->
        <section>
            <div class="a-container">
                <div class="admin-container">
                    <?php include('components/admin/sidebar.php'); ?>
                    <div class="panel-container">
                        <div class="banner" style=" height: 100px; color: var(--olive); background: rgba(255, 255, 255, 0.9) url('https://thumbs.dreamstime.com/z/cooking-banner-background-spices-vegetables-top-view-cooking-banner-background-spices-vegetables-top-view-free-168096882.jpg') ; background-size:cover">
                            <h1 style="color:var(--green)">edit user</h1>
                        </div>
                        <div class="title2">
                            <a href="admin.php">admin </a><span>/ edit user</span>
                        </div>

                        <div class="content">
                            <!-- //MESSAGES -->
                            <div class="detail">
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
                            </div>

                            <!-- EDIT USER SECTION -->
                            <section class="update-container">
                                <?php
                                if (isset($_GET['edit'])) {
                                    $edit_id = $_GET['edit'];
                                    $query = "SELECT * FROM `users` WHERE id = '$edit_id'";
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();
                                    $num_rows = $stmt->rowCount();

                                    if ($num_rows > 0) {
                                        while ($fetch_edit = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                            <div class="form-container">

                                                <form class="Form" onsubmit="return validateForm()" action="admin_edit_user.php" method="post" enctype="multipart/form-data">
                                                    <input type="hidden" name="update_id" value="<?php echo $fetch_edit['id']; ?>"><br>

                                                    <label for="add-name">Name:</label>
                                                    <input type="text" name="add_name" id="add-name" value="<?php echo $fetch_edit['name']; ?>" required>
                                                    <span id="nameError"></span>

                                                    <label for="add-email">Email:</label>
                                                    <input type="email" name="add_email" id="add-email" value="<?php echo $fetch_edit['email']; ?>" required><span id="emailError" class="error"></span>
                                                    <span id="emailError"></span>

                                                    <label for="user-type">User Type:</label>
                                                    <select name="user_type" id="user-type">
                                                        <option value="user">User</option>
                                                        <option value="admin">Admin</option>
                                                    </select>


                                                    <label for="add-password">Password:</label>
                                                    <input type="password" name="add_password" id="add-password" value="" required><span id="passwordError" class="error"></span>
                                                    <span id="passwordError"></span>

                                                    <label for="add-confirm-password">Confirm Password:</label>
                                                    <input type="password" name="add_confirm_password" id="add-confirm-password" value="" required><span id="confirmPasswordError" class="error"></span>
                                                    <span id="confirmPasswordError"></span>
                                                    <input class="form-button" type="submit" name="update_user" value="UPDATE">

                                                </form>
                                            </div>
                                <?php
                                        }
                                    } else {
                                        //no results found
                                    }
                                }
                                ?>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END MAIN -->
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
    <script src="formValidation.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>


</body>

</html>