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

// // Get the requested page from the URL
// $request_uri = $_SERVER['REQUEST_URI'];

// // Check if the requested page or resource exists
// if (!file_exists($request_uri)) {
// 	// Redirect to the custom "not found" page
// 	header('Location: not_found.php');
// 	exit;
// }


// List of pages that don't exist yet
$not_found_pages = array(
    '/wishlist.php',
    '/cart.php'
);

// Get the requested page from the URL
$request_uri = $_SERVER['REQUEST_URI'];

// Check if the requested page is in the "not found" pages array
if (in_array($request_uri, $not_found_pages)) {
    // Redirect to the custom "not found" page
    header('Location: not_found.php');
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
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- HEADER SECTION -->
    <section>
        <?php include 'components/header.php'; ?>
    </section>

    <main class="main">
        <div class="banner" style=" height: 200px; ">
            <h1 style="color: var(--green)">Detalii </h1>
        </div>
        <div class="title2">
            <a href="home.php">acasă </a><a href="view_menu.php">/ meniu </a><span>/ detalii produs</span>
        </div>

        <!-- VIEW MENU ITEM SECTION -->
        <section class="view_page">
            <?php
            if (isset($_GET['pid'])) {
                $pid = $_GET['pid'];
                $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = '$pid'");
                $select_products->execute();
                if ($select_products->rowCount() > 0) {
                    // while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                    $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)
            ?>
                    <form action="view_menu.php" method="post">
                        <img src="image/<?php echo $fetch_products['image']; ?>">
                        <div class="detail">
                            <div class="name"><?php echo $fetch_products['name']; ?></div>
                            <div class="detail">
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                    tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                    consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                                    cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                                    proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

                            </div>
                            <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">
                            <div class="button">
                                <input type="hidden" name="qty" value="1" min="0" class="quantity">
                            </div>
                            <div class="flex">
                                <p class="price"> <?= $fetch_products['price']; ?> Ron</p>
                                <p class="price"> <?= $fetch_products['measure']; ?></p>

                                <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
                                <button type="submit" name="add_to_cart" class="menu0-btn">Adaugă <i class="bx bx-cart"></i></button>

                            </div>

                        </div>
                    </form>
            <?php
                }
            }
            // }
            ?>
        </section>


        <!-- END MAIN -->
    </main>

    <!-- FOOTER SECTION -->
    <section id="menu">
        <?php include 'components/footer.php'; ?>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <!-- <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script> -->

    <script src="script.js"></script>
    <?php include 'components/alert.php'; ?>
</body>

</html>