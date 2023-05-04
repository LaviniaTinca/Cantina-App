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


//delete user GPT
// if (isset($_GET['delete'])){
//     $delete_id = $_GET['delete'];
//     $stmt = $pdo->prepare("SELECT image FROM `users` where id = :delete_id");
//     $stmt->execute(['delete_id' => $delete_id]);
//     $fetched_delete_image = $stmt->fetch(PDO::FETCH_ASSOC);
//     unlink('image/'.$fetched_delete_image['image']);

//     $stmt = $pdo->prepare("DELETE FROM `users` where id = :delete_id");
//     $stmt->execute(['delete_id' => $delete_id]);

//     $stmt = $pdo->prepare("DELETE FROM `reviews` where user_id = :delete_id");
//     $stmt->execute(['delete_id' => $delete_id]);

//     // $stmt = $pdo->prepare("DELETE FROM `wishlist` where user_id = :delete_id");
//     // $stmt->execute(['delete_id' => $delete_id]);

//     header('location: admin_user.php');
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

        header('location: admin_user.php');
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        $message[] = $e->getMessage();
    }
}


//delete user with image
// if (isset($_GET['delete'])){
//     $delete_id = $_GET['delete'];
//     $query = "SELECT image FROM `users` WHERE id = ?";
//     $stmt = $conn->prepare($query);
//     //$stmt->bind_param("i", $delete_id);
//     $stmt->execute([$delete_id]);
//     $result = $stmt->fetch(PDO::FETCH_ASSOC);

//     // if ($result->num_rows > 0) {
//     if ($result->rowCount() > 0) {

//         // $fetched_delete_image = $result->fetch_assoc();
//         // unlink('image/'.$fetched_delete_image['image']);

//         $query = "DELETE FROM `users` WHERE id = ?";
//         $stmt = $conn->prepare($query);
//         // $stmt->bind_param("i", $delete_id);
//         $stmt->execute([$delete_id]);

//         // $query = "DELETE FROM `reviews` WHERE user_id = ?";
//         // $stmt = $conn->prepare($query);
//         // // $stmt->bind_param("i", $delete_id);
//         // $stmt->execute([$delete_id]);

//         $query = "DELETE FROM `wishlist` WHERE user_id = ?";
//         $stmt = $conn->prepare($query);
//         // $stmt->bind_param("i", $delete_id);
//         $stmt->execute([$delete_id]);
//     }

//     header('location: admin_user.php');
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

        header('location: admin_user.php');
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
        header('location: admin_user.php');
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

    header('location: admin_user.php');
}

//another update without image for the form
if (isset($_POST['update_user'])) {
    $update_id = $_POST['edit_id'];
    $update_name = $_POST['edit_name'];
    $update_email = $_POST['edit_email'];
    $update_password = $_POST['edit_password'];

    try {
        $query = "UPDATE `users` SET `name`=?, `email`=?, `password`=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$update_name, $update_email, $update_password, $update_id]);
        header('location: admin_user.php');
    } catch (PDOException $e) {
        $message[] = "Error: " . $e->getMessage();
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
                            <a href="admin.php" style="color: var(--green);">admin </a><span>/ view products</span>
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
                            <h1>tabel</h1>

                            <section>
                                <div class="product-table-container">
                                    <table id="product-table" class="product-table">
                                        <thead>
                                            <tr>
                                                <th class="sortable" data-column="user_type">User Type</th>
                                                <th class="sortable" data-column="name">Name</th>
                                                <th class="sortable" data-column="email">Email</th>
                                                <!-- <th>Image</th> -->
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // $max_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                                            // $max_cart_items->execute([$user_id]);
                                            // $select_price = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
                                            // $select_price->execute([$product_id]);
                                            // $fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);
                                            $query = "SELECT * FROM `users`";
                                            $stmt = $conn->prepare($query);
                                            // $stmt = $conn->prepare("SELECT * FROM `users`");
                                            $stmt->execute();
                                            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            if (count($users) > 0) {
                                                foreach ($users as $user) {
                                            ?>
                                                    <tr>
                                                        <td><?php echo $user['user_type']; ?></td>
                                                        <td><?php echo $user['name']; ?></td>
                                                        <td><?php echo $user['email']; ?></td>
                                                        <!-- <td><img src="image/<?php echo $user['image']; ?>" alt="product image"></td> -->

                                                        <td>
                                                            <a href="admin_user.php?edit=<?php echo $user['id']; ?>" onclick="showPopup()" class="edit" id="edit">edit</a>
                                                            <a href="admin_user.php?delete=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('You really want to delete this user?');">delete</a>
                                                        </td>
                                                    </tr>
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


                            <!-- Add Product Section (initially hidden) -->
                            <!-- <a href="#" id="add-product-btn" style="text-decoration: none;">
                                <h2 style="color: var(--green); margin-left: 30px;"> * Add Product</h2>
                            </a>
                            <section class="add-products" id="add-products" style=" display: none; margin:0px 30px">
                                <h3 style="color:red">de stilizat formularul!!!</h3>
                                <form action="admin_view_products.php" method="post" enctype="multipart/form-data">
                                    <label for="add-name">Product Name:</label>
                                    <input type="text" name="add_name" id="add-name" required>

                                    <label for="add-detail">Product Detail:</label>
                                    <textarea name="add_detail" id="add-detail" required></textarea>

                                    <label for="add-price">Product Price:</label>
                                    <input type="number" name="add_price" id="add-price" required>

                                    <label for="add-image">Product Image:</label>
                                    <input type="file" name="add_image" id="add-image" required>

                                    <input type="submit" name="add_product" value="Add Product">
                                </form>
                            </section> -->

                            <!-- //show USERS with REVIEWS -->



                            <!-- //with errror at the id -->
                            <!-- <section>
                                <div class="product-table-container">
                                    <table id="product-table" class="product-table">
                                        <thead>
                                            <tr>
                                                <th class="sortable" data-column="name">Name</th>
                                                <th class="sortable" data-column="email">Email</th>
                                                <th class="sortable" data-column="user_type">User Type</th>
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
                                                foreach ($users as $user) {
                                            ?>
                                                    <tr>
                                                        <td><?php echo $user['name']; ?></td>
                                                        <td><?php echo $user['email']; ?></td>
                                                        <td><?php echo $user['user_type']; ?></td>
                                                        <td>
                                                            <a href="admin_users.php?edit=<?php echo $user['id']; ?>" class="edit" id="edit">edit</a>
                                                            <a href="admin_users.php?delete=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('You really want to delete user <?php echo $user['name']; ?>?');">delete</a>
                                                            <button class="toggle-reviews-btn" id="toggle-reviews-btn-<?php echo $user['id']; ?>">Toggle Reviews</button>

                                                        </td>
                                                    </tr>
                                                    <tr class="review-row" id="review-row-<?php echo $user['id']; ?>">
                                                        <td colspan="5">
                                                            <table class="review-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Product</th>
                                                                        <th>Rating</th>
                                                                        <th>Review</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $query = "SELECT * FROM `reviews` WHERE `user_id` = " . $user['id'];
                                                                    $stmt = $conn->prepare($query);
                                                                    $stmt->execute();
                                                                    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                                    if (count($reviews) > 0) {
                                                                        $i = 0;
                                                                        foreach ($reviews as $review) {
                                                                            $i++;
                                                                    ?>
                                                                            <tr>
                                                                                <td><?php echo $review['product_name']; ?></td>
                                                                                <td><?php echo $review['rating']; ?></td>
                                                                                <td><?php echo $review['review']; ?></td>
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
                                            <?php
                                                }
                                            } else {
                                                echo "<tr><td colspan='4'>No users found.</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </section> -->

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="script.js"></script>
</body>

</html>