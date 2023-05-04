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
$messages = array();


// //add product to the db
// if (isset($_POST['add_product'])) {
//     // Validate input
//     if (empty($_POST['add_name'])) {
//         $messages[] = "Product name is required.";
//     } else {
//         $add_name = filter_var($_POST['add_name'], FILTER_SANITIZE_STRING);
//     }

//     if (empty($_POST['add_detail'])) {
//         $messages[] = "Product detail is required.";
//     } else {
//         $add_detail = filter_var($_POST['add_detail'], FILTER_SANITIZE_STRING);
//     }

//     if (empty($_POST['add_price'])) {
//         $messages[] = "Product price is required.";
//     } else {
//         $add_price = filter_var($_POST['add_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
//     }

//     if (empty($_FILES['add_image']['name'])) {
//         $messages[] = "Product image is required.";
//     } else {
//         if ($_FILES['add_image']['error'] == UPLOAD_ERR_INI_SIZE || $_FILES['add_image']['error'] == UPLOAD_ERR_FORM_SIZE) {
//             $messages[] = "The uploaded file is too large.";
//         } elseif ($_FILES['add_image']['error'] == UPLOAD_ERR_NO_FILE) {
//             $messages[] = "No file was uploaded.";
//         } elseif ($_FILES['add_image']['error'] == UPLOAD_ERR_PARTIAL) {
//             $messages[] = "The uploaded file was only partially uploaded.";
//         } elseif ($_FILES['add_image']['error'] == UPLOAD_ERR_NO_TMP_DIR || $_FILES['add_image']['error'] == UPLOAD_ERR_CANT_WRITE || $_FILES['add_image']['error'] == UPLOAD_ERR_EXTENSION) {
//             $messages[] = "An error occurred while uploading the file. Please try again later.";
//         } elseif (!in_array($_FILES['add_image']['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
//             $messages[] = "The uploaded file must be a JPEG, PNG, or GIF image.";
//         } else {
//             $add_image_name = $_FILES['add_image']['name'];
//             $add_image_tmp_name = $_FILES['add_image']['tmp_name'];
//             $add_image_folder = 'image/' . $add_image_name;
//         }
//     }

//     // Insert product into database
//     if (empty($messages)) {
//         try {
//             $conn->beginTransaction();

//             // If image was uploaded, move it to the "image" directory
//             if (!empty($add_image_name)) {
//                 move_uploaded_file($add_image_tmp_name, $add_image_folder);
//             }

//             $query = "INSERT INTO `products` (`name`, `price`, `product_detail`, `image`) VALUES (?, ?, ?, ?)";
//             $stmt = $conn->prepare($query);
//             $stmt->execute([$add_name, $add_price, $add_detail, $add_image_folder]);

//             $conn->commit();
//             header('location: admin_view_products.php');
//         } catch (PDOException $e) {
//             $conn->rollback();
//             echo "Error adding product: " . $e->getMessage();
//         }
//     } else {
//         // Display messages
//         // foreach ($errors as $error) {
//         //     echo $error . "<br>";
//         // }
//     }
// }


// //delete user without image in the table
// if (isset($_GET['delete'])) {
//     $delete_id = $_GET['delete'];
//     try {
//         $query = "SELECT image FROM `products` WHERE id = ?";
//         $stmt = $conn->prepare($query);
//         $stmt->execute([$delete_id]);
//         $result = $stmt->fetch(PDO::FETCH_ASSOC);

//         if ($result) {
//             unlink('image/' . $result['image']);

//             $query = "DELETE FROM `products` WHERE id = ?";
//             $stmt = $conn->prepare($query);
//             $stmt->execute([$delete_id]);

//             $query = "DELETE FROM `wishlist` WHERE user_id = ?";
//             $stmt = $conn->prepare($query);
//             $stmt->execute([$delete_id]);

//             $query = "DELETE FROM `cart` WHERE id = ?";
//             $stmt = $conn->prepare($query);
//             $stmt->execute([$delete_id]);
//         }

//         header('location: admin_user.php');
//     } catch (PDOException $e) {
//         echo "Error deleting product: " . $e->getMessage();
//     }
// }

// //update product
// // if (isset($_POST['update_product'])){
// //     $update_id = $_POST['update_id'];
// //     $update_name = $_POST['update_name'];
// //     $update_detail = $_POST['update_detail'];
// //     $update_price = $_POST['update_price'];
// //     $update_image = $_FILES['update_image']['name'];
// //     $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
// //     $update_image_folder = 'image/'.$update_image;

// //     $query1 = "UPDATE `products` SET `id`='$update_id', `name`='$update_name', `price`='$update_price', `product_detail`='$update_detail',
// //                 `image`='$update_image' where id = '$update_id'";
// //     $update_query = mysqli_query($conn, $query1) or die ('query failed');
// //     if ($update_query){
// //         move_uploaded_file($update_image_tmp_name, $update_image_folder);
// //         header('location: admin_product.php');
// //     }
// // }

//update product
if (isset($_POST['update_product'])) {
    $update_id = $_POST['update_id'];
    $update_name = $_POST['update_name'];
    $update_detail = $_POST['update_detail'];
    $update_price = $_POST['update_price'];
    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_folder = 'image/' . $update_image;

    try {
        $conn->beginTransaction();
        $query = "SELECT image FROM `products` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$update_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && !empty($update_image)) {
            unlink('image/' . $result['image']);
            move_uploaded_file($update_image_tmp_name, $update_image_folder);

            $query = "UPDATE `products` SET `name`=?, `price`=?, `product_detail`=?, `image`=? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$update_name, $update_price, $update_detail, $update_image, $update_id]);
        } else {
            $query = "UPDATE `products` SET `name`=?, `price`=?, `product_detail`=? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$update_name, $update_price, $update_detail, $update_id]);
        }

        $conn->commit();
        header('location: admin_view_products.php');
    } catch (PDOException $e) {
        $conn->rollback();
        echo "Error updating product: " . $e->getMessage();
    }
}


//update product with validation, shoud add old image and transaction
if (isset($_POST['update_product2'])) {
    $update_id = $_POST['update_id'];
    $update_name = $_POST['update_name'];
    $update_detail = $_POST['update_detail'];
    $update_price = $_POST['update_price'];

    // Validate input
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $update_name)) {
        $error_message = "Product name can only contain alphanumeric characters and spaces";
        error_log($error_message);
        header('location: admin_product.php?error=' . urlencode($error_message));
        exit();
    }
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $update_price)) {
        $error_message = "Price must be a valid decimal number";
        error_log($error_message);
        header('location: admin_product.php?error=' . urlencode($error_message));
        exit();
    }

    // Validate file type
    $allowed_file_types = array('image/jpeg', 'image/png', 'image/webp');
    if (!in_array($_FILES['update_image']['type'], $allowed_file_types)) {
        $error_message = "Invalid file type. Please upload a JPEG, PNG, or WEBP image";
        error_log($error_message);
        header('location: admin_product.php?error=' . urlencode($error_message));
        exit();
    }

    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_folder = 'image/' . $update_image;

    try {
        $query = "UPDATE `products` SET `name`=?, `price`=?, `product_detail`=?, `image`=? WHERE `id`=?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$update_name, $update_price, $update_detail, $update_image, $update_id]);

        if ($stmt->rowCount() > 0) {
            move_uploaded_file($update_image_tmp_name, $update_image_folder);
            header('location: admin_product.php');
        } else {
            $error_message = "Product update failed";
            error_log($error_message);
            header('location: admin_product.php?error=' . urlencode($error_message));
            exit();
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
        error_log($error_message);
        header('location: admin_product.php?error=' . urlencode($error_message));
        exit();
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
    <link rel="stylesheet" href="css/header.css">

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
                        <!-- <div class="banner-container"> -->
                        <div class="title2">
                            <a href="admin.php" style="color: var(--green);">admin </a><span>/ edit product</span>
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

                            <!-- EDIT PRODUCT SECTION -->
                            <section class="update-container">
                                <?php
                                if (isset($_GET['edit'])) {
                                    $edit_id = $_GET['edit'];
                                    $query = "SELECT * FROM `products` WHERE id = '$edit_id'";
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();
                                    // Get the number of rows returned by the SELECT statement
                                    $num_rows = $stmt->rowCount();

                                    if ($num_rows > 0) {
                                        while ($fetch_edit = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                            <div class="form-container">
                                                <form action="" method="post" enctype="multipart/form-data">
                                                    <img src="image/<?php echo $fetch_edit['image']; ?>" alt="product to be edited">
                                                    <input type="hidden" name="update_id" value="<?php echo $fetch_edit['id']; ?>"><br>
                                                    <label for="name">Name:</label>
                                                    <input type="text" name="update_name" value="<?php echo $fetch_edit['name']; ?>">
                                                    <label for="price">Price:</label>
                                                    <input type="number" name="update_price" min="0" value="<?php echo $fetch_edit['price']; ?>">
                                                    <label for="product_detail">Product Detail:</label>
                                                    <textarea name="update_detail"><?php echo $fetch_edit['product_detail']; ?></textarea>
                                                    <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png, image/webp">
                                                    <div class="button-container">
                                                        <input type="submit" name="update_product" value="Update" class="edit" onclick="closeForm()">
                                                        <button type="button" class="close-btn" onclick="closeForm()">Close</button>
                                                    </div>
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

                            <section class="NO NO NOT YET">
                                <!-- //varianta 4 mai incerc cu mai multe tabel dar NU inca -->
                                <!-- <form action="update_product.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                                <label for="name">Name:</label>
                                <input type="text" name="name" id="name" value="<?php echo $product['name']; ?>">

                                <label for="description">Description:</label>
                                <textarea name="description" id="description"><?php echo $product['description']; ?></textarea>

                                <label for="image">Image:</label>
                                <?php if ($product['image']) { ?>
                                    <img src="image/<?php echo $product['image']; ?>" alt="product image"><br>
                                    <input type="checkbox" name="delete_image" id="delete_image">
                                    <label for="delete_image">Delete image</label>
                                <?php } ?>
                                <input type="file" name="image" id="image">

                                <label for="price">Price:</label>
                                <input type="text" name="price" id="price" value="<?php echo $product['price']; ?>">
                                <input type="submit" name="update_product" value="Update" class="edit">
                                <input type="submit" name="submit" value="Save">

                                </form> -->
                            </section>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END MAIN -->
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <script src="script.js"></script>

</body>

</html>