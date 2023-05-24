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
if ($_SESSION['user_type'] === 'user') {
    header('location:home.php');
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: login.php");
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

        $query = "DELETE FROM `wishlist` WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        $messages[] = 'User deleted!';

        header('location: admin_users.php');
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        $messages[] = $e->getMessage();
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
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>

    <!-- HEADER SECTION -->
    <section>
        <?php include 'components/admin/header.php'; ?>
    </section>

    <main class="main" style="margin-top: 50px; ">
        <!-- SIDEBAR AND PANEL-CONTAINER  SECTION-->
        <section>
            <div class="a-container">
                <div class="admin-container">
                    <?php include('components/admin/sidebar.php'); ?>
                    <div class="panel-container">
                        <div class="banner" style=" height: 100px; color: var(--olive); background: rgba(255, 255, 255, 0.9) url('https://thumbs.dreamstime.com/z/cooking-banner-background-spices-vegetables-top-view-cooking-banner-background-spices-vegetables-top-view-free-168096882.jpg') ; background-size:cover">
                            <h1 style="color:var(--green)">USERS</h1>
                        </div>
                        <div class="title2">
                            <a href="admin.php">admin </a><span>/ view users</span>
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

                            <!-- Add User Section (initially hidden) -->
                            <a href="#" id="add-product-btn" style="text-decoration: none;">
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
                                                                        echo "<tr><td colspan='3' rowspan='2'>No reviews found.</td></tr>";
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
    </main>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <script src="script.js"></script>
    <script src="formValidation.js"></script>

</body>

</html>