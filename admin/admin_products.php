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


//add product to the db
if (isset($_POST['add_product'])) {
    // Validate input
    if (empty($_POST['add_name'])) {
        $messages[] = "Product name is required.";
    } else {
        $add_name = filter_var($_POST['add_name'], FILTER_SANITIZE_STRING);
    }
    if (empty($_POST['add_detail'])) {
        $messages[] = "Product detail is required.";
    } else {
        $add_detail = filter_var($_POST['add_detail'], FILTER_SANITIZE_STRING);
    }

    if (empty($_POST['category'])) {
        $messages[] = "Product category is required.";
    } else {
        $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
    }
    if (empty($_POST['measure'])) {
        $messages[] = "Product measure is required.";
    } else {
        $measure = filter_var($_POST['measure'], FILTER_SANITIZE_STRING);
    }

    if (empty($_POST['add_price'])) {
        $messages[] = "Product price is required.";
    } else {
        $add_price = filter_var($_POST['add_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    if (empty($_FILES['add_image']['name'])) {
        $messages[] = "Product image is required.";
    } else {
        if ($_FILES['add_image']['error'] == UPLOAD_ERR_INI_SIZE || $_FILES['add_image']['error'] == UPLOAD_ERR_FORM_SIZE) {
            $messages[] = "The uploaded file is too large.";
        } elseif ($_FILES['add_image']['error'] == UPLOAD_ERR_NO_FILE) {
            $messages[] = "No file was uploaded.";
        } elseif ($_FILES['add_image']['error'] == UPLOAD_ERR_PARTIAL) {
            $messages[] = "The uploaded file was only partially uploaded.";
        } elseif ($_FILES['add_image']['error'] == UPLOAD_ERR_NO_TMP_DIR || $_FILES['add_image']['error'] == UPLOAD_ERR_CANT_WRITE || $_FILES['add_image']['error'] == UPLOAD_ERR_EXTENSION) {
            $messages[] = "An error occurred while uploading the file. Please try again later.";
        } elseif (!in_array($_FILES['add_image']['type'], ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
            $messages[] = "The uploaded file must be a JPEG, PNG, or GIF image.";
        } else {
            $add_image_name = $_FILES['add_image']['name'];
            $add_image_size = $_FILES['add_image']['size'];
            $add_image_tmp_name = $_FILES['add_image']['tmp_name'];
            $add_image_folder = '../image/' . $add_image_name;
        }
    }

    // Insert product into database
    if (empty($messages)) {
        try {
            $conn->beginTransaction();

            // If image was uploaded, move it to the "image" directory
            if (!empty($add_image_name)) {
                move_uploaded_file($add_image_tmp_name, $add_image_folder);
            }

            $id = unique_id();
            $query = "INSERT INTO `products` (`id`,`name`, `price`, `product_detail`, `image`, `category`, `measure`) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id, $add_name, $add_price, $add_detail, $add_image_name, $category, $measure]);

            $conn->commit();
            $success_msg[] = "Produsul a fost adaugat!";

            header('location: admin_products.php');
        } catch (PDOException $e) {
            $conn->rollback();
            echo "Eroare la adăugarea produsului: " . $e->getMessage();
        }
    } else {
        //display errors
    }
}


//delete product 
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "SELECT image FROM `products` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            unlink('../image/' . $result['image']);

            $query = "DELETE FROM `products` WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$delete_id]);

            $query = "DELETE FROM `cart` WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$delete_id]);
            $success_msg[] = "Produsul a fost sters!";
        }

        header('location: admin_products.php');
    } catch (PDOException $e) {
        echo "Eroare la ștergerea produsului: " . $e->getMessage();
    }
}


//update product
if (isset($_POST['update_product'])) {
    if ($_FILES['update_image']['error'] == UPLOAD_ERR_INI_SIZE || $_FILES['update_image']['error'] == UPLOAD_ERR_FORM_SIZE) {
        $messages[] = "The uploaded file is too large.";
    } elseif ($_FILES['update_image']['error'] == UPLOAD_ERR_NO_FILE) {
        $messages[] = "No file was uploaded.";
    } elseif ($_FILES['update_image']['error'] == UPLOAD_ERR_PARTIAL) {
        $messages[] = "The uploaded file was only partially uploaded.";
    } elseif ($_FILES['update_image']['error'] == UPLOAD_ERR_NO_TMP_DIR || $_FILES['update_image']['error'] == UPLOAD_ERR_CANT_WRITE || $_FILES['update_image']['error'] == UPLOAD_ERR_EXTENSION) {
        $messages[] = "An error occurred while uploading the file. Please try again later.";
    } elseif (!in_array($_FILES['update_image']['type'], ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
        $messages[] = "The uploaded file must be a JPEG, PNG, or GIF image.";
    } else {
        $update_image_size = $_FILES['update_image']['size'];
        $update_image = $_FILES['update_image']['name'];
        $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
        $update_image_folder = '../image/' . $update_image;
    }

    $update_id = $_POST['update_id'];
    $update_name = $_POST['update_name'];
    $update_detail = $_POST['update_detail'];
    $update_price = $_POST['update_price'];
    // $update_image = $_FILES['update_image']['name'];
    // $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    // $update_image_folder = '../image/' . $update_image;
    if (empty($messages)) {
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
            $success_msg[] = "Produsul a fost adaugat!";

            header('location: admin_products.php');
        } catch (PDOException $e) {
            $conn->rollback();
            echo "Eroare la actualizare: " . $e->getMessage();
        }
    }
}

// //add product to menu
// if (isset($_POST['add-to-menu'])) {
//     // Validate input
//     if (empty($_POST['product_id'])) {
//         $messages[] = "Product id is required.";
//     } else {
//         $product_id = htmlspecialchars($_POST['product_id'], ENT_QUOTES, 'UTF-8');
//     }
//     if (empty($_POST['qty'])) {
//         $messages[] = "Quantity id is required.";
//     } else {
//         $qty = htmlspecialchars($_POST['qty'], ENT_QUOTES, 'UTF-8');
//     }

//     // Insert product into menu table 
//     if (empty($messages)) {
//         try {
//             $conn->beginTransaction();

//             $id = unique_id();
//             $verify_menu = $conn->prepare("SELECT * FROM `menu` WHERE product_id = ?");
//             $verify_menu->execute([$product_id]);

//             if ($verify_menu->rowCount() > 0) {
//                 $warning_msg[] = 'produsul exista deja in meniu';
//             } else {

//                 $query = "INSERT INTO `menu` (`id`,`product_id`, `qty`) VALUES (?, ?, ?)";
//                 $stmt = $conn->prepare($query);
//                 $stmt->execute([$id, $product_id, $qty]);

//                 $conn->commit();
//                 $success_msg[] = 'produs adaugat in meniu';
//                 header('location: admin_products.php');
//             }
//         } catch (PDOException $e) {
//             $conn->rollback();
//             echo "Error adding product: " . $e->getMessage();
//         }
//     }
// }
//add product to menu
if (isset($_POST['add-to-menu'])) {
    // Validate input
    if (empty($_POST['product_id'])) {
        $warning_msg[] = "Product id is required.";
        $messages[] = "Product id is required.";
    } else {
        $product_id = htmlspecialchars($_POST['product_id'], ENT_QUOTES, 'UTF-8');
    }
    if (empty($_POST['qty'])) {
        $messages[] = "Quantity id is required.";
        $warning_msg[] = "Quantity id is required.";
    } else {
        $qty = htmlspecialchars($_POST['qty'], ENT_QUOTES, 'UTF-8');
    }

    // Insert product into menu table 
    if (empty($messages)) {
        try {
            $conn->beginTransaction();

            $query = "SELECT * FROM `daily_menu` WHERE date = CURDATE() FOR UPDATE";
            $check_menu = $conn->prepare($query);
            $check_menu->execute();

            if ($check_menu->rowCount() > 0) {
                // Retrieve the daily_menu_id for today
                $daily_menu_id = $check_menu->fetch(PDO::FETCH_ASSOC)['id'];

                $verify_menu = $conn->prepare("
                                        SELECT dmi.* 
                                        FROM `daily_menu_items` dmi
                                        INNER JOIN `daily_menu` dm ON dmi.daily_menu_id = dm.id
                                        WHERE dmi.product_id = ? AND dm.date > CURDATE()
                                    ");
                $verify_menu->execute([$product_id]);

                if ($verify_menu->rowCount() > 0) {
                    $update_qty = $conn->prepare("UPDATE `daily_menu_items` SET qty = ? WHERE product_id = ? and daily_menu_id =?");
                    $update_qty->execute([$qty, $product_id, $daily_menu_id]);

                    $success_msg[] = 'cantitatea produsului din meniu a fost modificata!';
                } else {
                    $query = "INSERT INTO `daily_menu_items` (`daily_menu_id`, `product_id`, `qty`) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$daily_menu_id, $product_id, $qty]);

                    // Commit the transaction
                    $conn->commit();

                    $success_msg[] = 'product added to menu successfully';
                    header('location: admin_products.php');
                }
            } else {
                // There is no daily menu for today, so create one and add the product

                // Create a new daily menu entry for today
                $create_menu = $conn->prepare("INSERT INTO `daily_menu` (`date`) VALUES (CURDATE()) ");
                $create_menu->execute();

                // Retrieve the newly created daily_menu_id
                $daily_menu_id = $conn->lastInsertId();

                // Perform your insertion into the daily_menu_items table
                $query = "INSERT INTO `daily_menu_items` (`daily_menu_id`, `product_id`, `qty`) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->execute([$daily_menu_id, $product_id, $qty]);

                // Commit the transaction
                $conn->commit();

                // Display a success message or perform other actions
                $success_msg[] = 'product added to menu successfully';
                header('location: admin_products.php');
            }
        } catch (PDOException $e) {
            $conn->rollback();
            $error_msg[] = "Error adding product: " . $e->getMessage();
        }
    }
}


//update product with validation, should add old image and transaction
if (isset($_POST['update_product2'])) {
    $update_id = $_POST['update_id'];
    $update_name = $_POST['update_name'];
    $update_detail = $_POST['update_detail'];
    $update_price = $_POST['update_price'];

    // Validate input
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $update_name)) {
        $error_message = "Product name can only contain alphanumeric characters and spaces";
        error_log($error_message);
        header('location: admin_products.php?error=' . urlencode($error_message));
        exit();
    }
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $update_price)) {
        $error_message = "Price must be a valid decimal number";
        error_log($error_message);
        header('location: admin_products.php?error=' . urlencode($error_message));
        exit();
    }

    // Validate file type
    $allowed_file_types = array('image/jpeg', 'image/png', 'image/webp');
    if (!in_array($_FILES['update_image']['type'], $allowed_file_types)) {
        $error_message = "Invalid file type. Please upload a JPEG, PNG, or WEBP image";
        error_log($error_message);
        header('location: admin_products.php?error=' . urlencode($error_message));
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

//CHART
try {
    $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS record_count FROM products GROUP BY month";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Extract xValues and yValues from the result
    $xValues = array_column($result, 'month');
    $yValues = array_column($result, 'record_count');
} catch (PDOException $e) {
    $error_msg[] = "Eroare: " . $e->getMessage();
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>


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
                            <!-- WIDGETS -->
                            <section class="widgets">

                                <div class="widget setting-widget">
                                    <div class="flex">
                                        <div class="small-widget">
                                            <i class='bx bx-cog'></i>
                                        </div>
                                        <a href="admin_products.php">
                                            <h3 style="color: #EE964B"> Produse</h3>
                                        </a>
                                    </div>

                                    <div class="widget product-widget jump" id="product-widget">
                                        <div class="flex">
                                            <div class="small-widget">
                                                <i class="bx bx-package"></i>
                                            </div>
                                            <h4> adaugă </h4>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="myChart" style="max-width:400px"></canvas>

                            </section>

                            <!--Add New Product Modal box -->
                            <section class="modal" id="product-modal">
                                <div class="modal-content">
                                    <span class="close" id="close-modal">&times;</span>
                                    <div class="form-container">
                                        <h2>Produs nou</h2>

                                        <form class="Form" action="admin_products.php" method="post" enctype="multipart/form-data">
                                            <label for="add-name">Product Name:</label>
                                            <input type="text" name="add_name" id="add-name" required>
                                            <label for="product-category">Catgoria:</label>
                                            <select name="category" id="product-category">
                                                <option value="soup">Supă/Ciorbă</option>
                                                <option value="principal">Garnitură/Fel principal</option>
                                                <option value="desert">Desert</option>
                                                <option value="beverages">Băuturi</option>
                                                <option value="altele">Altele</option>
                                            </select>
                                            <label for="add-detail">Product Detail:</label>
                                            <textarea name="add_detail" id="add-detail" required></textarea>
                                            <label for="add-price">Product Price:</label>
                                            <input type="number" name="add_price" id="add-price" required>
                                            <label for="measure">Unitatea de Masura:</label>
                                            <input type="text" name="measure" id="measure" required>
                                            <label for="add-image">Product Image:</label>
                                            <input type="file" name="add_image" id="add-image" required>

                                            <input class="form-button" type="submit" name="add_product" value="INREGISTREAZA">
                                        </form>
                                    </div>

                                </div>
                            </section>
                            <!-- SHOW TABLE PRODUCTS WITH SORT AND FILTER-->
                            <section>
                                <div id="popup-container" style="display: none;">
                                    <img id="popup-image" src="" alt="popup image">
                                </div>
                                <div class="product-table-container">
                                    <table id="product-table" class="product-table">
                                        <thead>
                                            <tr>
                                                <th>Imagine</th>
                                                <th class="sortable" data-sort="string" data-column="name">Nume</th>
                                                <th class="sortable" data-sort="string" data-column="category">Categorie</th>
                                                <th class="sortable" data-sort="string" data-column="measure">Unitate de măsură</th>
                                                <th class="sortable" data-sort="number" data-column="price">Preț</th>
                                                <th>Acțiuni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            try {
                                                $query = "SELECT * FROM `products`";
                                                $stmt = $conn->prepare($query);
                                                $stmt->execute();
                                                $fetch_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                if (count($fetch_products) > 0) {
                                                    foreach ($fetch_products as $product) {
                                            ?>
                                                        <tr class="filter">
                                                            <td> <img src="../image/<?php echo $product['image']; ?>" alt="img" class="product-image"></td>
                                                            <td title="<?php echo $product['name']; ?>"><a href="admin_view_product.php?pid=<?php echo $product['id']; ?>"><?php echo substr($product['name'], 0, 25) . '...'; ?></a></td>
                                                            <td title="<?php echo $product['category']; ?>"><?php echo substr($product['category'], 0, 15) . '...'; ?></td>
                                                            <td title="<?php echo $product['measure']; ?>"><?php echo substr($product['measure'], 0, 15) . '...'; ?></td>
                                                            <td><?php echo $product['price']; ?></td>
                                                            <td>
                                                                <form action="admin_products.php" method="post" class="add-to-menu-form">
                                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                                    <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">

                                                                    <!-- <input class="form-button meal-icon" type="submit" name="add-to-menu" value="menu"> -->
                                                                    <button class="form-button" type="submit" name="add-to-menu" title="Adaugă cantitatea specificată în meniul zilei">

                                                                        <i class="fas fa-utensils"></i>
                                                                        <i class="fas fa-pizza-slice"></i>
                                                                    </button>
                                                                </form>
                                                                <form method="post" action="admin_products.php">
                                                                    <input type="hidden" name="user_id" value="<?= $product['id']; ?>">
                                                                    <a href="admin_edit_product.php?edit=<?php echo $product['id']; ?>" class="edit" id="edit"><i class=" fas fa-edit" title="Editează"></i></a>
                                                                    <a href="admin_products.php?delete=<?php echo $product['id']; ?>" class="delete" onclick="return confirm('Dorești să ștergi produsul <?php echo $product['name']; ?> ?');"><i class="fas fa-trash-alt" title="Șterge"></i></a>
                                                                </form>
                                                            </td>
                                                        </tr>
                                            <?php
                                                    }
                                                } else {
                                                    echo '
                                                        <tr>
                                                            <td colspan="5" rowspan="2" class="empty">
                                                                <p>No products added yet</p>
                                                            </td>
                                                        </tr>
                                                    ';
                                                }
                                            } catch (Exception $e) {
                                                $error_msg[] = 'Error: ' . $e->getMessage();
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END MAIN -->
    </div>
    <?php include '../components/alert.php'; ?>
    <script src="../js/searchCard.js"></script>
    <script src="../js/script.js"></script>
    <script>
        // popup 
        $(document).ready(function() {
            $('.product-image').on('click', function() {
                var src = $(this).attr('src');
                $('#popup-image').attr('src', src);
                $('#popup-container').fadeIn();
            });

            $('#popup-container').on('click', function() {
                $(this).fadeOut();
            });
        });
    </script>
    <script>
        // Function to open the modal
        $("#product-widget").click(function() {
            $("#product-modal").show();
        });

        // Function to close the modal
        $("#close-modal").click(function() {
            $("#product-modal").hide();
        });

        // Function to save the announcement
        $("#add_product").click(function() {

            $("#product-modal").hide();
        });
    </script>
    <script>
        // Use PHP's json_encode function to convert PHP arrays to JavaScript arrays
        const xValues = <?php echo json_encode($xValues); ?>;
        const yValues = <?php echo json_encode($yValues); ?>;

        new Chart("myChart", {
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                    fill: true,
                    lineTension: 0,
                    backgroundColor: "rgba(238, 150, 75, 1.0)",
                    borderColor: "rgba(238, 150, 75, 0.1)",
                    data: yValues
                }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            callback: function(value, index, values) {
                                return yValues.includes(value) ? value : '';
                            }
                            // callback: function(value, index, values) {
                            //     return [0, 20, 40, 60].includes(value) ? value : '';
                            // }
                            // min: Math.min(...yValues), // Set the minimum value based on the minimum of yValues
                            // max: Math.max(...yValues) // Set the maximum value based on the maximum of yValues
                        }
                    }],
                }
            }
        });
    </script>

</body>

</html>