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

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: ../login.php");
}
$current_page = basename($_SERVER['PHP_SELF']);
$messages = array();


// List of pages that don't exist yet
$not_found_pages = array(
    '/wishlist.php',
);

// Get the requested page from the URL
$request_uri = $_SERVER['REQUEST_URI'];

// Check if the requested page is in the "not found" pages array
if (in_array($request_uri, $not_found_pages)) {
    // Redirect to the custom "not found" page
    header('Location: ../not_found.php');
    exit;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - View Item</title>
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

                            <!-- VIEW MENU ITEM SECTION -->
                            <section>
                                <?php
                                if (isset($_GET['pid'])) {

                                    $pid = $_GET['pid'];
                                    $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
                                    $select_products->execute([$pid]);
                                    if ($select_products->rowCount() > 0) {
                                        $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)
                                ?>

                                        <div class="banner" style="height:50px; ">
                                            <h1 style="color: var(--green)">detalii produs - <?php echo $fetch_products['name'] ?></h1>
                                        </div>
                                        <div class="title2">
                                            <a href="admin_products.php">produse </a><span>/ detalii produs cu seria: <?php echo $_GET['pid'] ?></span>
                                        </div>
                                        <div class="view_page">
                                            <form action="admin_products.php" method="post">
                                                <img src="../image/<?php echo $fetch_products['image']; ?>">
                                                <div class="detail">
                                                    <div class="name"><?php echo $fetch_products['name']; ?></div>
                                                    <div class="detail">
                                                        <?php echo $fetch_products['product_detail']; ?>
                                                        <!-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                                                        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                                        consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                                                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                                                        proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p> -->

                                                    </div>
                                                    <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">

                                                    <div class="flex">
                                                        <!-- <p class="price"> <?= $fetch_products['price']; ?> Ron</p>
                                                    <p class="price"> <?= $fetch_products['measure']; ?></p> -->
                                                        <!-- <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
                                                    <button type="submit" name="add_to_cart" class="menu0-btn">Adaugă <i class="bx bx-cart"></i></button> -->
                                                    </div>
                                                    <hr class="dotted-line">
                                                    <p>Pret: <?= $fetch_products['price']; ?> Ron</p>
                                                    <p>Unitatea de măsură: <?= $fetch_products['measure']; ?></p>
                                                    <p> Data adăugării: <?= $fetch_products['created_at']; ?></p>

                                                    <div class="form-container">
                                                        <br>
                                                        <form method="post" action="admin_products.php">
                                                            <input type="hidden" name="user_id" value="<?= $fetch_products['id']; ?>">
                                                            <a href="admin_edit_product.php?edit=<?php echo $fetch_products['id']; ?>" class="edit" id="edit"><i class=" fas fa-edit" title="Editează"></i></a>
                                                            <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="delete" onclick="return confirm('Dorești să ștergi produsul <?php echo $fetch_products['name']; ?> ?');"><i class="fas fa-trash-alt" title="Șterge"></i></a>
                                                        </form>
                                                    </div>


                                                </div>
                                            </form>
                                        </div>

                                <?php
                                    }
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../script.js"></script>

</body>

</html>