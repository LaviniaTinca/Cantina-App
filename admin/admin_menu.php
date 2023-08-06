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
                header('location: admin_products.php');
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

        $success_msg[] = 'cantitatea produsului din meniu a fost modificata!';
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

        $success_msg[] = 'produsul a fost sters din meniu';

        header('location: admin_menu.php');
    } catch (PDOException $e) {
        echo "Error deleting product: " . $e->getMessage();
    }
}

//empty menu
if (isset($_POST['empty_menu'])) {
    try {
        $query = "DELETE FROM `menu`";
        $stmt = $conn->prepare($query);
        $stmt->execute();

        $success_msg[] = 'datele din meniu au fost sterse';
        header('location: admin_menu.php');
    } catch (PDOException $e) {
        echo "Error deleting product: " . $e->getMessage();
    } catch (Exception $e) {
        $error_msg[] = $e;
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
            background-image: url('../images/soup.png');
            background-size: cover;
        }

        .filter-box[data-category="garniture"] {
            background-image: url('../images/orez_legume.png');
            background-size: cover;
        }

        .filter-box[data-category="principal"] {
            /* background-color: #c2dfff; */
            background-image: url('../images/gratar.png');
            background-size: cover;
        }

        .filter-box[data-category="desert"] {
            background-image: url('../images/cookie.jpg');
            background-size: cover;
        }

        .filter-box[data-category="salad"] {
            background-image: url('../images/salata.png');
            background-size: cover;
        }

        .filter-box[data-category="beverage"] {
            background-image: url('../images/tea2.jpg');
            background-size: cover;
        }

        .menu-date-picker {
            width: 220px;
            border: none;
        }

        .cart-btn:hover i.fas.fa-trash-alt {
            color: white;
        }
    </style>
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

                            <!-- Banner Section - with image card -->

                            <section>
                                <div class="flex">
                                    <a href="admin_products.php">
                                        <h4> + Adaugă produs nou / Modifică produs</h4>
                                    </a>
                                    <input type="date" class="menu-date-picker" id="datePicker" onchange="updateMenuHeading()">

                                    <form method="post">
                                        <button type="submit" name="empty_menu" class="cart-btn transparent-button" onclick="return confirm('Dorești să golești meniul zilei?')"><i class="fas fa-trash-alt" title="Sterge datele din meniu"></i> Șterge meniul</button>
                                    </form>
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
                                                        <td> <img src="../image/<?php echo $product['image']; ?>" alt="product image" class="product-image"></td>
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
                                                                <p>Nu au fost adăugate produse în meniul zilei!</p>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include '../components/alert.php'; ?>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script src="../script.js"></script>
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
</body>

</html>