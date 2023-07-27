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


//add product to menu
if (isset($_POST['add-to-menu'])) {
    // Validate input
    if (empty($_POST['product_id'])) {
        $messages[] = "Product id is required.";
    } else {
        $product_id = htmlspecialchars($_POST['product_id'], ENT_QUOTES, 'UTF-8');
    }
    if (empty($_POST['qty'])) {
        $messages[] = "Quantity id is required.";
    } else {
        $qty = htmlspecialchars($_POST['qty'], ENT_QUOTES, 'UTF-8');
    }

    // Insert product into menu table 
    if (empty($messages)) {
        try {
            $conn->beginTransaction();

            $id = unique_id();
            $verify_menu = $conn->prepare("SELECT * FROM `menu` WHERE product_id = ?");
            $verify_menu->execute([$product_id]);

            if ($verify_menu->rowCount() > 0) {
                $warning_msg[] = 'product already exist in your menu';
            } else {

                $query = "INSERT INTO `menu` (`id`,`product_id`, `qty`) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->execute([$id, $product_id, $qty]);

                $conn->commit();
                $success_msg[] = 'product added to menu successfully';
                header('location: admin_view_products.php');
            }
        } catch (PDOException $e) {
            $conn->rollback();
            echo "Error adding product: " . $e->getMessage();
        }
    }
}

//update product qty in menu 
if (isset($_POST['update_menu'])) {
    $menu_id = htmlspecialchars($_POST['menu_id'], ENT_QUOTES, 'UTF-8');
    $qty = htmlspecialchars($_POST['qty'], ENT_QUOTES, 'UTF-8');
    try {
        $update_qty = $conn->prepare("UPDATE `menu` SET qty = ? WHERE id = ?");
        $update_qty->execute([$qty, $menu_id]);

        $success_msg[] = 'menu item quantity updated successfully';
    } catch (PDOException $e) {
        echo "Error updating menu product: " . $e->getMessage();
    }
}

//delete menu item
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `menu` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        header('location: admin_view_products.php');
    } catch (PDOException $e) {
        echo "Error deleting product: " . $e->getMessage();
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

    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* General styles for the admin page */
        .category-box {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            /* Add any additional styling for the admin page layout */
        }

        /* Styles for the filter boxes */
        .filter-box {
            width: 140px;
            height: 100px;
            /* border: 1px solid #ccc; */
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 8px;
            /* Add any additional styling for the filter boxes */
        }

        /* Styles for the category names */
        .filter-box h3 {
            font-size: 14px;
            font-weight: bold;
            /* Add any additional styling for the category names */
        }

        /* Add specific styles for each filter box */
        .filter-box[data-category="soup"] {
            /* background-color: #c5eff7; */
            background-image: url('images/soup.png');
            background-size: cover;
        }

        .filter-box[data-category="garniture"] {
            background-image: url('images/orez_legume.png');
            background-size: cover;
        }

        .filter-box[data-category="principal"] {
            /* background-color: #c2dfff; */
            background-image: url('images/gratar.png');
            background-size: cover;
        }

        .filter-box[data-category="desert"] {
            background-image: url('images/cookie.jpg');
            background-size: cover;
        }

        .filter-box[data-category="salad"] {
            background-image: url('images/salata.png');
            background-size: cover;
        }

        .filter-box[data-category="beverage"] {
            background-image: url('images/tea2.jpg');
            background-size: cover;
        }

        /* Custom style for the icon */
        button i.fas.fa-edit {
            color: teal;
            font-size: 18px;
        }

        /* Custom style for the delete icon */
        button i.fas.fa-trash-alt {
            color: red;
            font-size: 18px;
        }

        .menu-date-picker {
            width: 220px;
            border: none;
        }
    </style>
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
                        <!-- <div class="banner" style=" height: 100px; color: var(--olive); background: rgba(255, 255, 255, 0.9) url('https://thumbs.dreamstime.com/z/cooking-banner-background-spices-vegetables-top-view-cooking-banner-background-spices-vegetables-top-view-free-168096882.jpg') ; background-size:cover">
                            <h1 style="color:var(--green)" id="menu-heading">today's menu</h1>
                        </div> -->
                        <!-- <div class="title2">
                            <a href="admin.php">admin </a><span>/ set menu</span>
                            <input type="date" class="menu-date-picker" id="datePicker" onchange="updateMenuHeading()">
                            <input type="text" id="search-input" placeholder="Search by keyword..." style="width:min-content">

                        </div> -->

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

                            <!-- Add Product Section (initially hidden) -->
                            <!-- <a href="#" id="add-product-btn" style="text-decoration: none;">
                                <h2 style="color: var(--green); margin-left: 30px;"> * Add Product</h2>
                            </a>
                            <section class="add-products" id="add-products" style=" display: none; margin:0px 30px">
                                <form class="Form" action="admin_view_products.php" method="post" enctype="multipart/form-data">
                                    <label for="add-name">Product Name:</label>
                                    <input type="text" name="add_name" id="add-name" required>

                                    <label for="add-detail">Product Detail:</label>
                                    <textarea name="add_detail" id="add-detail" required></textarea>

                                    <label for="add-price">Product Price:</label>
                                    <input type="number" name="add_price" id="add-price" required>

                                    <label for="add-image">Product Image:</label>
                                    <input type="file" name="add_image" id="add-image" required>

                                    <input class="form-button" type="submit" name="add_product" value="Add Product">
                                </form>
                            </section> -->
                            <section>
                                <div class="flex">
                                    <a href="admin_view_products.php" style="margin-left: 20px;"> Add / Edit a Product</a>
                                    <input type="date" class="menu-date-picker" id="datePicker" onchange="updateMenuHeading()">
                                </div>

                                <div class="category-box">
                                    <div class="filter-box" data-category="soup" title="Supe/Ciorbe">
                                    </div>
                                    <div class="filter-box" data-category="garniture" title="Garnituri">
                                    </div>
                                    <div class="filter-box" data-category="principal" title="Fel principal">
                                    </div>
                                    <div class="filter-box" data-category="salad" title="Salate">
                                    </div>
                                    <div class="filter-box" data-category="desert" title="Dulciuri">
                                    </div>
                                    <div class="filter-box" data-category="beverage" title="Bauturi/Ceai/Cafea">
                                    </div>
                                </div>
                            </section>
                            <!-- <input type="text" id="search-input" placeholder="Search by keyword..." style="width:min-content">

                            <a href="admin_view_products.php"> Add / Edit a Product</a> -->

                            <!-- SHOW TABLE PRODUCTS WITH SORT AND FILTER-->
                            <section>
                                <div id="popup-container" style="display: none;">
                                    <img id="popup-image" src="" alt="popup image">
                                </div>
                                <div class="product-table-container">
                                    <table id="product-table" class="product-table">
                                        <thead>
                                            <tr>
                                                <th class="sortable" data-column="image">Image</th>
                                                <th class="sortable" data-column="name">Name</th>
                                                <th class="sortable" data-column="price">Price</th>
                                                <th class="sortable" data-column="category">Category</th>
                                                <th class="sortable" data-column="qty">Quantity</th>
                                                <th>Unit</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT products.*, menu.qty AS qty, menu.id AS menu_id
                                            FROM menu
                                            JOIN products ON menu.product_id = products.id";
                                            $stmt = $conn->prepare($query);
                                            $stmt->execute();
                                            $fetch_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            if (count($fetch_products) > 0) {
                                                foreach ($fetch_products as $product) {
                                            ?>
                                                    <tr>
                                                        <td> <img src="image/<?php echo $product['image']; ?>" alt="product image" class="product-image"></td>
                                                        <td><?php echo $product['name']; ?></td>
                                                        <td><?php echo $product['price']; ?></td>
                                                        <td><?php echo substr($product['category'], 0, 15) . '...'; ?></td>
                                                        <td><?php echo $product['qty']; ?></td>
                                                        <td><?php echo $product['measure']; ?></td>
                                                        <td>
                                                            <form action="admin_menu.php" method="post">
                                                                <input type="hidden" name="menu_id" value="<?php echo $product['menu_id']; ?>">
                                                                <input type="number" name="qty" class="qty edit" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="<?= $product['qty']; ?>">
                                                                <button type="submit" name="update_menu" title="Update quantity">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <a href="admin_menu.php?delete=<?php echo $product['menu_id']; ?>" class="delete" onclick="return confirm('You really want to delete <?php echo $product['name']; ?> from the menu?');"><i class="fas fa-trash-alt" title="Delete"></i></a>
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



    <!-- SHOW PRODUCT CARD SECTION -->
    <!-- <section class="show-products">
        <div class="box-container">
            <?php
            $query = "SELECT * FROM `products`";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $fetch_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($fetch_products)) {
                foreach ($fetch_products as $product) {
            ?>
                    <div class="box">
                        <img src="image/<?php echo $product['image']; ?>" alt="product image">
                        <p>price : <?php echo $product['price']; ?> lei</p>
                        <h4><?php echo $product['name']; ?></h4>
                        <details> <?php echo $product['product_detail']; ?> </details>
                        <a href="admin_product.php?edit=<?php echo $product['id']; ?>" class="edit">edit</a>
                        <a href="admin_product.php?delete=<?php echo $product['id']; ?>" class="delete" onclick="
                        return confirm('You really want to delete this product?'); ">delete</a>
                    </div>
            <?php
                }
            } else {
                echo '
                            <div class="empty">
                                <p>no products added yet</p>
                            </div>
                        ';
            }
            ?>
        </div>
    </section> -->
    </div>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script src="script.js"></script>
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
    <!-- <script>
        // Function to format the date as "Month Day, Year"
        function formatDate(date) {
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return date.toLocaleDateString('en-US', options);
        }

        // Function to update the heading with the selected date
        function updateMenuHeading() {
            const menuHeading = document.getElementById('menu-heading');
            const datePicker = document.getElementById('datePicker');
            const selectedDate = new Date(datePicker.value); // Get the selected date from the date picker
            menuHeading.textContent = "Today's menu - " + formatDate(selectedDate);
        }
    </script> -->
    <script>
        // const menuHeading = document.getElementById('menu-heading');

        // Function to format the date as "Month Day, Year"
        function formatDate(date) {
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return date.toLocaleDateString('en-US', options);
        }

        // Function to set the date picker value from local storage
        function setDatePickerValue() {
            const datePicker = document.getElementById('datePicker');
            const savedDate = localStorage.getItem('selectedDate');
            if (savedDate) {
                datePicker.value = savedDate;
            }
        }

        // Function to update the heading with the selected date and save to local storage
        function updateMenuHeading() {
            // const menuHeading = document.getElementById('menu-heading');
            const datePicker = document.getElementById('datePicker');
            const selectedDate = new Date(datePicker.value); // Get the selected date from the date picker
            // menuHeading.textContent = "Today's menu - " + formatDate(selectedDate);

            // Save the selected date to local storage
            localStorage.setItem('selectedDate', datePicker.value);
        }

        // Call the function to set the date picker value from local storage
        setDatePickerValue();
    </script>

    <?php include 'components/alert.php'; ?>
</body>

</html>