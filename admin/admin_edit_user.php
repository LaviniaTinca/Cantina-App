<?php
include '../php/connection.php';
include '../php/session_handler.php';

//EDIT USER SECTION GOOD
if (isset($_POST['update_user'])) {
    try {
        $conn->beginTransaction();

        // Get the values from the form
        $id = $_POST['update_id'];
        $name = htmlspecialchars($_POST['add_name'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($_POST['add_email'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($_POST['add_password'], ENT_QUOTES, 'UTF-8');
        $confirm_password = htmlspecialchars($_POST['add_confirm_password'], ENT_QUOTES, 'UTF-8');
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
        header("Location: admin_users.php");
    } catch (Exception $e) {
        // Roll back the transaction
        $conn->rollBack();
        $messages[] = "Eroare la actualizare: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - admin</title>

    <link rel="stylesheet" href="../css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- HEADER SECTION -->
    <section>
        <?php include '../components/admin/header.php'; ?>
    </section>

    <div class="main" style="margin-top: 50px; ">
        <!-- SIDEBAR AND PANEL-CONTAINER  SECTION-->
        <section>
            <div class="a-container">
                <div class="admin-container">
                    <?php include('../components/admin/sidebar.php'); ?>
                    <div class="panel-container">


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
                                <h3 class="update">ACTUALIZEAZĂ UTILIZATORUL</h3>

                                <?php
                                try {

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

                                                            <option value="user" <?php if ($fetch_edit['user_type'] === 'user') echo 'selected'; ?>>User</option>
                                                            <option value="admin" <?php if ($fetch_edit['user_type'] === 'admin') echo 'selected'; ?>>Admin</option>
                                                        </select>


                                                        <label for="add-password">Password:</label>
                                                        <input type="password" name="add_password" id="add-password" value="" required><span id="passwordError" class="error"></span>
                                                        <span id="passwordError"></span>

                                                        <label for="add-confirm-password">Confirm Password:</label>
                                                        <input type="password" name="add_confirm_password" id="add-confirm-password" value="" required><span id="confirmPasswordError" class="error"></span>
                                                        <span id="confirmPasswordError"></span>
                                                        <input class="form-button" type="submit" name="update_user" value="ACTUALIZEAZĂ">

                                                    </form>
                                                </div>
                                <?php
                                            }
                                        } else {
                                            //no results found
                                        }
                                    }
                                } catch (PDOException $e) {
                                    echo "Eroare: " . $e->getMessage();
                                } catch (Exception $e) {
                                    echo "Eroare: " . $e->getMessage();
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
    <?php include '../components/alert.php'; ?>

    <script src="../js/script.js"></script>
    <script src="../js/formValidation.js"></script>
</body>

</html>