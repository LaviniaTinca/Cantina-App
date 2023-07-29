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
    '/wishlist.php'
);

// Get the requested page from the URL
$request_uri = $_SERVER['REQUEST_URI'];

// Check if the requested page is in the "not found" pages array
if (in_array($request_uri, $not_found_pages)) {
    // Redirect to the custom "not found" page
    header('Location: not_found.php');
    exit;
}


//adding products in the cart
if (isset($_POST['add_to_cart'])) {
    $id = unique_id();
    $product_id = $_POST['product_id'];

    $qty = $_POST['qty'];
    $qty = htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');

    $product = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
    $product->execute([$product_id]);
    $fetch_product = $product->fetch(PDO::FETCH_ASSOC);

    // Check if the product is already in the cart for the user
    $existingCartItem = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ? LIMIT 1");
    $existingCartItem->execute([$user_id, $product_id]);
    $fetch_existingCartItem = $existingCartItem->fetch(PDO::FETCH_ASSOC);

    if ($fetch_existingCartItem) {
        // Update the quantity of the existing cart item
        $newQty = $fetch_existingCartItem['qty'] + $qty;

        $update_cart = $conn->prepare("UPDATE `cart` SET qty = ? WHERE id = ?");
        $update_cart->execute([$newQty, $fetch_existingCartItem['id']]);

        if ($update_cart) {
            // $success_msg[] = 'Product quantity updated in cart successfully';
            $success_msg[] = 'Cantitatea a fost modificată!';
        } else {
            $error_msg[] = 'Nu s-a putut efectuat modificarea cantității!';
        }
    } else {
        // Insert a new cart item
        $id = unique_id();

        $insert_cart = $conn->prepare("INSERT INTO `cart` (id, user_id, product_id, price, qty) VALUES (?, ?, ?, ?, ?)");
        $insert_cart->execute([$id, $user_id, $product_id, $fetch_product['price'], $qty]);

        if ($insert_cart) {
            $success_msg[] = 'Produsul a fost adaugat în coș';
            // $success_msg[] = 'Product added to cart successfully';
        } else {
            $error_msg[] = 'Nu s-a putut adăuga produsul în coș';
            // $error_msg[] = 'Failed to add product to cart';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - products</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
            <h1 style="color: var(--green)">Meniul zilei</h1>
        </div>
        <div class="title2">
            <a href="home.php">acasă </a><span>/ meniu</span>
        </div>

        <!-- SHOW PRODUCTS SECTION -->

        <div class="menu1">
            <div id="popup-container" style="display: none;">
                <img id="popup-image" src="" alt="popup image">
            </div>
            <section class="products">
                <div class="box-container">
                    <?php
                    $select_products = $conn->prepare("SELECT products.*
                                 FROM menu
                                 JOIN products ON menu.product_id = products.id");
                    $select_products->execute();
                    if ($select_products->rowCount() > 0) {
                        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {

                    ?>
                            <form action="view_menu.php" method="post" class="box">
                                <!-- <a href="not_found.php?page=checkout?get_id=<?= $fetch_products['id']; ?>" class="add-btn">add</a> -->
                                <div class="products-img-wrapper">
                                    <img src="image/<?= $fetch_products['image']; ?>" alt="product image" class="img product-image">
                                    <!-- <a href="view_item.php?pid=<?php echo $fetch_products['id']; ?>" class="bx bxs-show"></a> -->
                                    <a href="view_item.php?pid=<?php echo $fetch_products['id']; ?>" class="far fa-eye eye-icon" title="Previzualizare"></a>

                                </div>
                                <br>

                                <br>
                                <h3 class="name"><?= $fetch_products['name']; ?></h3>
                                <input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">
                                <div class="flex">
                                    <p class="price"> <?= $fetch_products['price']; ?> Ron</p>
                                    <p class="price"> <?= $fetch_products['measure']; ?></p>

                                    <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
                                    <button type="submit" name="add_to_cart" class="menu-add-btn" title="Adaugă în coș"><i class="bx bx-cart"></i></button>

                                </div>

                            </form>
                    <?php
                        }
                    } else {
                        echo '<p class="empty">nu au fost incă adăugate produse!</p>';
                        // echo '<p class="empty">no products added yet!</p>';
                    }
                    ?>
                </div>
            </section>
        </div>

        <!-- END MAIN -->
    </main>

    <!-- FOOTER SECTION -->
    <section id="menu">
        <?php include 'components/footer.php'; ?>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <!-- <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script> -->
    <script src="script.js"></script>
    <script src="popup.js"></script>

    <!-- <script>
        //asta e bun doar pt toggle culoare
        $(document).ready(function() {
            $(".wishlistButton").click(function(event) {
                event.preventDefault(); // Prevent form submission

                $(this).toggleClass("active");
            });
        });
    </script> -->

    <?php include 'components/alert.php'; ?>
</body>

</html>