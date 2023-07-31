<?php
include '../php/connection.php';
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (!isset($_SESSION['user_id'])) {
    header('location:../login.php');
}
if ($_SESSION['user_type'] === 'user') {
    header('location:../home.php');
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: ../login.php");
}
$current_page = basename($_SERVER['PHP_SELF']);
$messages = array();



//delete user without image in the table
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `users` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);

        $success_msg[] = "Anunțul a fost șters!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        $messages[] = $e->getMessage();
    }
}
//-------------------------------------------------
//delete announcement


//edit announcement
if (isset($_POST['edit-announcement'])) {
    $update_id = $_POST['announcement_id'];
    $update_category = $_POST['category'];
    $update_description = $_POST['description'];

    try {
        $conn->beginTransaction();
        $query = "UPDATE `announcements` SET `category`=?, `description`=? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$update_category, $update_description, $update_id]);

        $conn->commit();
    } catch (PDOException $e) {
        $conn->rollback();
        echo "Error updating announcement: " . $e->getMessage();
    }
}

//edit user
if (isset($_POST['update_user'])) {
    try {
        // Connect to the database
        // Begin a transaction
        $conn->beginTransaction();

        // Get the values from the form
        $id = $_POST['user_id'];
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
    <title>Cantina - admin</title>

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>

    <!-- HEADER SECTION -->
    <section>
        <?php include '../components/admin/header.php'; ?>
    </section>

    <main class="main" style="margin-top: 50px; ">
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

                            <!-- Add User Section (initially hidden) -->
                            <!-- <a href="#" id="add-product-btn" style="text-decoration: none;">
                                <h2 style="color: var(--green); margin-left: 30px;"> * Add User</h2>
                            </a>
                            <section class="add-products" id="add-products" style=" display: none; margin:0px 30px">
                                <div class="form-container">
                                    <form class="Form" onsubmit="return validateForm()" action="register.php" method="post" enctype="multipart/form-data">
                                        <label for="add-name">Name:</label>
                                        <input type="text" name="add_name" id="add-name" required>
                                        <span id="nameError"></span>

                                        <label for="add-email">Email:</label>
                                        <input type="email" name="add_email" id="add-email" required><span id="emailError" class="error"></span>
                                        <span id="emailError"></span>

                                        <label for="add-password">Password:</label>
                                        <input type="password" name="add_password" id="add-password" required><span id="passwordError" class="error"></span>
                                        <span id="passwordError"></span>

                                        <label for="add-confirm-password">Confirm Password:</label>
                                        <input type="password" name="add_confirm_password" id="add-confirm-password" required><span id="confirmPasswordError" class="error"></span>
                                        <span id="confirmPasswordError"></span>
                                        <input class="form-button" type="submit" name="add_user" value="REGISTER">
                                    </form>
                                </div>
                            </section> -->
                            <!-- WIDGETS -->
                            <section class="widgets">
                                <div class="widget setting-widget">
                                    <div class="flex">

                                        <div class="small-widget">
                                            <i class='bx bx-cog'></i>
                                        </div>
                                        <a href="admin_users.php">
                                            <h3 style="color: var(--cart);"> Utilizatori</h3>
                                        </a>
                                    </div>

                                    <div class="widget user-widget jump" id="user-widget">
                                        <div class="flex">
                                            <div class="small-widget">
                                                <i class='bx bx-user'></i>
                                            </div>
                                            <h4> adaugă </h4>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!--Add New User Modal box -->
                            <div class="modal" id="user-modal">
                                <div class="modal-content">
                                    <span class="close" id="close-modal">&times;</span>
                                    <h2>Utilizator nou</h2>

                                    <form class="Form" onsubmit="return validateForm()" action="../register.php" method="post" enctype="multipart/form-data">
                                        <label for="add-name">Name:</label>
                                        <input type="text" name="add_name" id="add-name" required>
                                        <span id="nameError"></span>

                                        <label for="add-email">Email:</label>
                                        <input type="email" name="add_email" id="add-email" required><span id="emailError" class="error"></span>
                                        <span id="emailError"></span>

                                        <label for="add-password">Password:</label>
                                        <input type="password" name="add_password" id="add-password" required><span id="passwordError" class="error"></span>
                                        <span id="passwordError"></span>

                                        <label for="add-confirm-password">Confirm Password:</label>
                                        <input type="password" name="add_confirm_password" id="add-confirm-password" required><span id="confirmPasswordError" class="error"></span>
                                        <span id="confirmPasswordError"></span>
                                        <input class="form-button" type="submit" name="add_user" id="add_user" value="REGISTER">
                                    </form>
                                </div>
                            </div>

                            <!-- Edit Announcement Modal Box -->

                            <div class="modal" id="edit-user-modal">
                                <div class="modal-content">
                                    <span class="close edit-close-modal" id="edit-close-modal">&times;</span>

                                    <h2>Modifică utilizator</h2>
                                    <form class="Form" onsubmit="return validateForm()" action="admin_users.php?edit_id={user_id}" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="user_id" id="edit-user-id">


                                        <label for="edit-name">Name:</label>
                                        <input type="text" name="add_name" id="edit-name" required>
                                        <span id="nameError"></span>

                                        <label for="add-email">Email:</label>
                                        <input type="email" name="add_email" id="edit-email" required><span id="emailError" class="error"></span>
                                        <span id="emailError"></span>

                                        <label for="user-type">User Type:</label>
                                        <select name="user_type" id="edit-user-type">
                                            <option value="user">User</option>
                                            <option value="admin">Admin</option>
                                        </select>


                                        <label for="edit-add-password">Password:</label>
                                        <input type="password" name="add_password" id="edit-add-password" required><span id="passwordError" class="error"></span>
                                        <span id="passwordError"></span>

                                        <label for="edit-add-confirm-password">Confirm Password:</label>
                                        <input type="password" name="add_confirm_password" id="edit-add-confirm-password" required><span id="confirmPasswordError" class="error"></span>
                                        <span id="confirmPasswordError"></span>
                                        <input class="form-button" type="submit" name="update_user" id="update_user" value="MODIFICA">

                                    </form>
                                </div>
                            </div>

                            <!-- SHOW USERS TABLE with REVIEWS and FILTER  -->
                            <section>
                                <div class="product-table-container">
                                    <table id="product-table" class="product-table">
                                        <thead>
                                            <tr>
                                                <th>Nr.</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>User Type</th>
                                                <th>Actions</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT * FROM `users`";
                                            $stmt = $conn->prepare($query);
                                            $stmt->execute();
                                            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            if (count($users) > 0) {
                                                $nr = 1;
                                                foreach ($users as $user) {
                                            ?>
                                                    <tr>
                                                        <td><?php echo $nr;
                                                            $nr++; ?></td>
                                                        <td><?php echo $user['name']; ?></td>
                                                        <td><?php echo $user['email']; ?></td>
                                                        <td><?php echo $user['user_type']; ?></td>
                                                        <td>
                                                            <form method="post" action="admin_users.php">
                                                                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                                                <a href="#" class="edit edit-link" data-user-id="<?php echo $user['id']; ?>" data-name=" <?php echo $user['name']; ?>" data-email="<?php echo $user['email']; ?>" data-user-type="<?php echo $user['user_type']; ?>">
                                                                    <i class=" fas fa-edit" title="Editează"></i>
                                                                </a>
                                                                <a href="admin_users.php?delete=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('Dorești să ștergi utilizatorul <?php echo $user['name']; ?> ?');"><i class="fas fa-trash-alt" title="Șterge"></i></a>
                                                            </form>
                                                            <!-- <a href="admin_edit_user.php?edit=<?php echo $user['id']; ?>" class="edit" id="edit">edit</a> -->
                                                            <!-- <a href="admin_users.php?delete=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('You really want to delete user <?php echo $user['name']; ?> ?');">delete</a> -->
                                                        </td>
                                                    </tr>
                                                    <!-- //REVIEWS TABLE -->

                                                    <!-- <tr class="review-row" id="review-row-<?php echo $user['id']; ?>">
                                                        <td colspan="5">
                                                            <table class="review-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th> Product</th>
                                                                        <th>Img</th>
                                                                        <th>Rating</th>
                                                                        <th>Comment</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $query = "SELECT * FROM `reviews` WHERE `user_id` = ?";
                                                                    $stmt = $conn->prepare($query);
                                                                    $stmt->execute([$user['id']]);
                                                                    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                    if (count($reviews) > 0) {

                                                                        foreach ($reviews as $review) {

                                                                    ?>
                                                                            <tr>
                                                                                <?php
                                                                                try {
                                                                                    $query = "SELECT * FROM `products` WHERE `id` = ?";
                                                                                    $stmt = $conn->prepare($query);
                                                                                    $stmt->execute([$review['product_id']]);
                                                                                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                                                                                ?>
                                                                                    <td><?php echo substr($product['name'], 0, 20) . '...'; ?></td>

                                                                                    <td><img src="image/<?php echo $product['image']; ?>" alt="product image"></td>
                                                                                <?php
                                                                                } catch (\Throwable $th) {
                                                                                    //throw $th;
                                                                                    echo "Error: " . $th->getMessage();
                                                                                }
                                                                                ?>
                                                                                <td><?php
                                                                                    echo $review['rating'];
                                                                                    for ($i = 0; $i < $review['rating']; $i++) {
                                                                                        echo '*';
                                                                                    }
                                                                                    ?></td>
                                                                                <td><?php echo substr($review['comment'], 0, 20) . '...'; ?></td>

                                                                            </tr>
                                                                    <?php
                                                                        }
                                                                    } else {
                                                                        echo "<tr><td colspan='3' rowspan='2'>No reviews found.</td></tr>";
                                                                    }
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr> -->
                                                    <!-- //END REVIEWS TABLE -->
                                            <?php
                                                }
                                            } else {
                                                echo '
                            <tr>
                                <td colspan="5" class="empty">
                                    <p>No products added yet</p>
                                </td>
                            </tr>
                        ';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </section>

                            <!-- //END TABLE  -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <script src="../script.js"></script>
    <script src="../formValidation.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <?php include '../components/alert.php'; ?>
    <script>
        // Function to open the modal
        $("#user-widget").click(function() {
            $("#user-modal").show();
        });

        // Function to close the modal
        $("#close-modal").click(function() {
            $("#user-modal").hide();
        });

        // Function to save the announcement
        $("#add_user").click(function() {

            $("#user-modal").hide();
        });
    </script>
    <script>
        //script to set the old data to the edit announcement modal
        $(document).ready(function() {
            // Function to open the modal
            $(".edit-link").click(function() {
                // Get the announcement ID from the data attribute
                const userId = $(this).data("user-id");
                $("#edit-user-id").val(userId);


                // Fetch the announcement details from the server using AJAX
                $.ajax({
                    type: "POST",
                    url: "get_user_details.php", // Replace with the PHP script that fetches the announcement details
                    data: {
                        id: userId
                    },
                    dataType: "json",
                    success: function(response) {
                        // Populate the modal form with the fetched details
                        $("#edit-user-modal").attr("data-user-id", response.id).show();
                        // Set the announcement ID value in the hidden input field
                        $("#edit-user-id").val(response.id);
                        $("#edit-user-type option").each(function() {
                            if ($(this).val() === response.user_type) {
                                $(this).prop("selected", true);
                            } else {
                                $(this).prop("selected", false);
                            }
                        });
                        $("#add-name").val(response.name);
                        $("#add-email").val(response.email);
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                    }
                });
            });

            // Function to close the modal
            $(".edit-close-modal").click(function() {
                $(this).closest(".modal").hide();
            });

            // Function to save the announcement (submit the form)
            $("#update_user").click(function() {
                $(this).closest(".modal").hide();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Function to open the modal
            $(".edit-link").click(function() {
                // Get the user ID from the data attribute
                const userId = $(this).data("user-id");
                console.log("Fetching user details for user ID: " + userId);

                // Fetch the user details from the server using AJAX
                $.ajax({
                    type: "POST",
                    url: "get_user_details.php",
                    data: {
                        id: userId
                    },
                    dataType: "json",
                    success: function(response) {
                        // Check if the response contains user data
                        if (response && !response.error) {
                            console.log("User details fetched successfully:", response);

                            // Populate the modal form with the fetched details
                            $("#edit-user-id").val(response.id);
                            $("#edit-user-type").val(response.user_type);
                            $("#edit-name").val(response.name);
                            $("#edit-email").val(response.email);
                        } else {
                            console.log("Error fetching user details:", response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error:", error);
                    }
                });
            });

            // Function to close the modal
            $(".edit-close-modal").click(function() {
                $(this).closest(".modal").hide();
            });

            // Function to save the user details (submit the form)
            $("#update_user").click(function() {
                $(this).closest(".modal").hide();
            });
            // Function to save the user details (submit the form)
            $("#update_user").click(function(event) {
                // Prevent the form from submitting
                event.preventDefault();

                // Perform form validation
                const name = $("#edit-name").val().trim();
                const email = $("#edit-email").val().trim();
                const password = $("#edit-add-password").val().trim();
                const confirmPassword = $("#edit-add-confirm-password").val().trim();

                let isValid = true;

                // Validate Name
                if (name === "") {
                    $("#nameError").text("Name is required.");
                    isValid = false;
                } else {
                    $("#nameError").text("");
                }

                // Validate Email
                if (email === "") {
                    $("#emailError").text("Email is required.");
                    isValid = false;
                } else {
                    $("#emailError").text("");
                }

                // Validate Password
                if (password === "") {
                    $("#passwordError").text("Password is required.");
                    isValid = false;
                } else {
                    $("#passwordError").text("");
                }

                // Validate Confirm Password
                if (confirmPassword === "") {
                    $("#confirmPasswordError").text("Confirm Password is required.");
                    isValid = false;
                } else if (password !== confirmPassword) {
                    $("#confirmPasswordError").text("Passwords do not match.");
                    isValid = false;
                } else {
                    $("#confirmPasswordError").text("");
                }

                // If the form is valid, submit it
                if (isValid) {
                    // Perform AJAX request or submit the form
                    // ...
                    $(".Form").submit();
                    // Close the modal (if needed)
                    $(this).closest(".modal").hide();
                }
            });

        });
    </script>

</body>

</html>