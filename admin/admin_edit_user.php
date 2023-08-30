<?php
include '../config/connection.php';
include '../config/session_admin.php';
include '../api/functions.php';

// update user in the database
if (isset($_POST['update_user'])) {
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
            $add_email = htmlspecialchars($add_email, ENT_QUOTES, 'UTF-8');
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

        $id = $_POST['update_id'];
        $user_type = "";

        // validate user type field
        if (empty($_POST["user_type"])) {
            $messages[] = "Rolul utilizatorului este necesar.";
        } else {
            $user_type = test_input($_POST["user_type"]);
        }

        // Check if the email already exists in the database
        // $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND id != ?");
        // $stmt->execute([$add_email, $id]);
        // if ($stmt->rowCount() > 0) {
        //     throw new Exception("Acest email este deja folosit.");
        // }
    } catch (PDOException $e) {
        $messages[] = "Eroare: " . $e->getMessage();
        $warning_msg[] = "Eroare: " . $e->getMessage();
    } catch (Exception $e) {
        $messages[] = "Eroare: " . $e->getMessage();
        $warning_msg[] = "Eroare: " . $e->getMessage();
    }

    // Insert user into database
    if (empty($messages)) {
        try {
            $query = "UPDATE users SET name = :name, email = :email, password = :password, user_type=:user_type WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->execute(['name' => $add_name, 'email' => $add_email, 'password' => $add_password, 'user_type' => $user_type, 'id' => $id]);

            $success_msg[] = "Utilizatorul a fost actualizat";
            header("Location: admin_users.php");
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

                                                        <label for="add-name">Nume:</label>
                                                        <input type="text" name="add_name" id="add-name" value="<?php echo $fetch_edit['name']; ?>" required>
                                                        <span id="nameError"></span>

                                                        <!-- <label for="add-email">Email:</label> -->
                                                        <input type="hidden" name="add_email" id="add-email" value="<?php echo $fetch_edit['email']; ?>" required><span id="emailError" class="error"></span>
                                                        <span id="emailError"></span>

                                                        <label for="user-type">Rol:</label>
                                                        <select name="user_type" id="user-type">

                                                            <option value="user" <?php if ($fetch_edit['user_type'] === 'user') echo 'selected'; ?>>User</option>
                                                            <option value="admin" <?php if ($fetch_edit['user_type'] === 'admin') echo 'selected'; ?>>Admin</option>
                                                        </select>

                                                        <label for="add-password">Parola:</label>
                                                        <input type="password" name="add_password" id="add-password" value="" required><span id="passwordError" class="error"></span>
                                                        <span id="passwordError"></span>

                                                        <label for="add-confirm-password">Confirmă Parola:</label>
                                                        <input type="password" name="add_confirm_password" id="add-confirm-password" value="" required><span id="confirmPasswordError" class="error"></span>
                                                        <span id="confirmPasswordError"></span>
                                                        <input class="form-button" type="submit" name="update_user" value="ACTUALIZEAZĂ">

                                                    </form>
                                                </div>
                                <?php
                                            }
                                        } else {
                                            echo '<p>Nu s-a găsit utilizatorul!</p>';
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