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
$message = array();

//add user //!!!!! good for register but without IMAGE
// Add user to the database
// if (isset($_POST['add_user'])) {
//     // Validate input
//     if (empty($_POST['add_name'])) {
//         $messages[] = "Name is required.";
//     } else {
//         $add_name = filter_var($_POST['add_name'], FILTER_SANITIZE_STRING);
//     }

//     if (empty($_POST['add_email'])) {
//         $messages[] = "Email is required.";
//     } else {
//         $add_email = filter_var($_POST['add_email'], FILTER_SANITIZE_EMAIL);
//         if (!filter_var($add_email, FILTER_VALIDATE_EMAIL)) {
//             $messages[] = "Invalid email format.";
//         }
//     }

//     if (empty($_POST['add_password'])) {
//         $messages[] = "Password is required.";
//     } elseif (strlen($_POST['add_password']) < 6) {
//         $messages[] = "Password must be at least 6 characters long.";
//     } else {
//         $add_password = password_hash($_POST['add_password'], PASSWORD_DEFAULT);
//     }

//     if (empty($_POST['add_confirm_password'])) {
//         $messages[] = "Confirm password is required.";
//     } elseif ($_POST['add_confirm_password'] != $_POST['add_password']) {
//         $messages[] = "Passwords do not match.";
//     }

//     // Insert user into database
//     if (empty($messages)) {
//         try {
//             $conn->beginTransaction();

//             $id = unique_id();
//             $query = "INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES (?, ?, ?, ?)";
//             $stmt = $conn->prepare($query);
//             $stmt->execute([$id, $add_name, $add_email, $add_password]);

//             $conn->commit();
//             header('location: admin_users.php');
//         } catch (PDOException $e) {
//             $conn->rollback();
//             echo "Error adding user: " . $e->getMessage();
//         }
//     } else {
//         // Display errors
//     }
// }

///////////////////////////////////////////////////////////////////////




//delete user GPT
// if (isset($_GET['delete'])){
// $delete_id = $_GET['delete'];
// $stmt = $pdo->prepare("SELECT image FROM `users` where id = :delete_id");
// $stmt->execute(['delete_id' => $delete_id]);
// $fetched_delete_image = $stmt->fetch(PDO::FETCH_ASSOC);
// unlink('image/'.$fetched_delete_image['image']);

// $stmt = $pdo->prepare("DELETE FROM `users` where id = :delete_id");
// $stmt->execute(['delete_id' => $delete_id]);

// $stmt = $pdo->prepare("DELETE FROM `reviews` where user_id = :delete_id");
// $stmt->execute(['delete_id' => $delete_id]);

// // $stmt = $pdo->prepare("DELETE FROM `wishlist` where user_id = :delete_id");
// // $stmt->execute(['delete_id' => $delete_id]);

// header('location: admin_users.php');
// }


//delete user without image in the table
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `users` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);

        $query = "DELETE FROM `wishlist` WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        $message[] = 'User deleted!';

        header('location: admin_users.php');
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        $message[] = $e->getMessage();
    }
}


//delete user with image
// if (isset($_GET['delete'])){
// $delete_id = $_GET['delete'];
// $query = "SELECT image FROM `users` WHERE id = ?";
// $stmt = $conn->prepare($query);
// //$stmt->bind_param("i", $delete_id);
// $stmt->execute([$delete_id]);
// $result = $stmt->fetch(PDO::FETCH_ASSOC);

// // if ($result->num_rows > 0) {
// if ($result->rowCount() > 0) {

// // $fetched_delete_image = $result->fetch_assoc();
// // unlink('image/'.$fetched_delete_image['image']);

// $query = "DELETE FROM `users` WHERE id = ?";
// $stmt = $conn->prepare($query);
// // $stmt->bind_param("i", $delete_id);
// $stmt->execute([$delete_id]);

// // $query = "DELETE FROM `reviews` WHERE user_id = ?";
// // $stmt = $conn->prepare($query);
// // // $stmt->bind_param("i", $delete_id);
// // $stmt->execute([$delete_id]);

// $query = "DELETE FROM `wishlist` WHERE user_id = ?";
// $stmt = $conn->prepare($query);
// // $stmt->bind_param("i", $delete_id);
// $stmt->execute([$delete_id]);
// }

// header('location: admin_users.php');
// }
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "SELECT image FROM `users` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $query = "DELETE FROM `users` WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$delete_id]);

            $query = "DELETE FROM `wishlist` WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$delete_id]);
        }

        header('location: admin_users.php');
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


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
        echo "Error: " . $e->getMessage();
    }
}

//update simple var1
if (isset($_POST['update_user2'])) {
    $update_id = $_POST['update_id'];
    $update_firstName = $_POST['update_name'];
    $update_email = $_POST['update_email'];
    $update_password = $_POST['update_password'];

    $query = "UPDATE `users` SET `name`=?, `email`=?, `password`=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$update_name, $update_email, $update_password, $update_id]);

    header('location: admin_users.php');
}

//another update for the form
if (isset($_POST['update_user'])) {
    $update_id = $_POST['edit_id'];
    $update_name = $_POST['edit_name'];
    $update_email = $_POST['edit_email'];
    $update_password = $_POST['edit_password'];

    try {
        $query = "UPDATE `users` SET `name`=?, `email`=?, `password`=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$update_name, $update_email, $update_password, $update_id]);
        header('location: admin_users.php');
    } catch (PDOException $e) {
        $message[] = "Error: " . $e->getMessage();
    }
}


// //update user
// if (isset($_POST['update_user2'])){
// $update_id = $_POST['update_id'];
// $update_firstName = $_POST['update_firstName'];
// $update_lastName = $_POST['update_lastName'];
// $update_email = $_POST['update_email'];
// $update_password = $_POST['update_password'];
// // $update_image = $_FILES['update_image']['name'];
// // $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
// // $update_image_folder = 'image/'.$update_image;

// $query1 = "UPDATE `users` SET `id`='$update_id', `firstName`='$update_firstName', `lastName`='$update_lastName', `email`='$update_email', `password`='$update_password',
// `image`='$update_image' where id = '$update_id'";
// $update_query = mysqli_query($conn, $query1) or die ('query failed');
// // if ($update_query){
// // move_uploaded_file($update_image_tmp_name, $update_image_folder);
// // header('location: admin_user.php');
// // }
// }

//update user
// if (isset($_POST['update_user'])){
// $update_id = $_POST['update_id'];
// $update_firstName = $_POST['update_firstName'];
// $update_lastName = $_POST['update_lastName'];
// $update_email = $_POST['update_email'];
// $update_password = $_POST['update_password'];

// $query1 = "UPDATE `users` SET `firstName`='$update_firstName', `lastName`='$update_lastName', `email`='$update_email', `password`='$update_password'
// WHERE `id` = '$update_id'";
// $update_query = mysqli_query($conn, $query1) or die ('query failed');

// if ($update_query){
// header('location: admin_user.php');
// }
// }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">

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
                        <!-- <div class="banner-container"> -->
                        <div class="title2">
                            <a href="admin.php" style="color: var(--green);">admin </a><span>/ view users</span>
                            <input type="text" id="search-input" placeholder="Search by keyword..." style="width:min-content">
                        </div>
                        <!-- </div> -->

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
                            <a href="#" id="add-product-btn" style="text-decoration: none;">
                                <h2 style="color: var(--green); margin-left: 30px;"> * Add User</h2>
                            </a>
                            <section class="add-products" id="add-products" style=" display: none; margin:0px 30px">
                                <h3 style="color:red">de stilizat formularul!!!</h3>

                                <form class="Form" onsubmit="return validateForm()" action="register_copy.php" method="post" enctype="multipart/form-data">
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

                                    <!-- <input type="submit" name="add_user" value="Add User"> -->
                                    <button type="submit" name="add_user">REGISTER</button>

                                </form>
                                <!-- <form action="admin_users.php" method="post" enctype="multipart/form-data">
                                    <label for="add-name">Name:</label>
                                    <input type="text" name="add_name" id="add-name" required>

                                    <label for="add-email">Email:</label>
                                    <input type="email" name="add_email" id="add-email" required>

                                    <label for="add-password">Password:</label>
                                    <input type="password" name="add_password" id="add-password" required>

                                    <label for="add-confirm-password">Confirm Password:</label>
                                    <input type="password" name="add_confirm_password" id="add-confirm-password" required>


                                    <input type="submit" name="add_user" value="Add User">
                                </form> -->
                            </section>

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
                                                            <a href="admin_edit_user.php?edit=<?php echo $user['id']; ?>" class="edit" id="edit">edit</a>
                                                            <a href="admin_users.php?delete=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('You really want to delete user <?php echo $user['name']; ?> ?');">delete</a>
                                                            <button class="toggle-reviews-btn" id="toggle-reviews-btn-<?php echo $user['id']; ?>">Toggle Reviews</button>
                                                        </td>
                                                    </tr>
                                                    <!-- //REVIEWS TABLE -->

                                                    <tr class="review-row" id="review-row-<?php echo $user['id']; ?>">
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
                                                                        echo "<tr><td colspan='3'>No reviews found.</td></tr>";
                                                                    }
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
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
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <script src="script.js"></script>
    <script src="formValidation.js"></script>

</body>

</html>