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
);

// Get the requested page from the URL
$request_uri = $_SERVER['REQUEST_URI'];

// Check if the requested page is in the "not found" pages array
if (in_array($request_uri, $not_found_pages)) {
    // Redirect to the custom "not found" page
    header('Location: not_found.php');
    exit;
}

//update product in cart

if (isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $cart_id = htmlspecialchars($cart_id, ENT_QUOTES, 'UTF-8');
    $qty = $_POST['qty'];
    $qty = $_POST['qty'];
    $qty = htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');

    $update_qty = $conn->prepare("UPDATE `cart` SET qty = ? WHERE id = ?");
    $update_qty->execute([$qty, $cart_id]);

    // $success_msg[] = 'cart quantity updated successfully';
}

//delete menu item
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `cart` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        // $success_msg[] = "cart item deleted successfully";
        $success_msg[] = "Produsul a fost șters din coș!";
        // header('location: admin_view_products.php');
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        // echo "Error deleting product: " . $e->getMessage();
    }
}

//empty cart
if (isset($_POST['empty_cart'])) {
    $verify_empty_item = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
    $verify_empty_item->execute([$user_id]);

    if ($verify_empty_item->rowCount() > 0) {
        $delete_cart_id = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
        $delete_cart_id->execute([$user_id]);
        // $success_msg[] = "The cart is empty now! ";
        $success_msg[] = "Coșul este gol! ";
    } else {
        $warning_msg[] = 'produsul nu există în coș';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - Cart Page</title>
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
            <h1 style="color: var(--green)">Coșul de cumpărături</h1>
        </div>
        <div class="title2">
            <a href="home.php">acasă </a><span>/ coș</span>
        </div>

        <!-- cart table -->
        <section>
            <?php
            $grand_total = 0;
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            if ($select_cart->rowCount() > 0) {
            ?>
                <div id="popup-container" style="display: none;">
                    <img id="popup-image" src="" alt="popup image">
                </div>
                <div class="product-table-container">
                    <table id="product-table" class="product-table">
                        <thead>
                            <tr>
                                <th class="th-cart">Imagine</th>
                                <th class="th-cart">Produs</th>
                                <th class="th-cart">Măsura</th>
                                <th class="th-cart">Cantitate</th>
                                <th class="th-cart">Preț unitar</th>
                                <th class="th-cart">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                                $select_products = $conn->prepare("SELECT * FROM `products` WHERE id= ?");
                                $select_products->execute([$fetch_cart['product_id']]);
                                if ($select_products->rowCount() > 0) {
                                    $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                                    $sub_total = $fetch_cart['qty'] * $fetch_products['price'];
                                    $grand_total += $sub_total;
                            ?>
                                    <tr>
                                        <td>
                                            <img src=" image/<?= $fetch_products['image']; ?>" class="img">
                                        </td>
                                        <td>
                                            <h3 class="name"><?= $fetch_products['name']; ?></h3>
                                        </td>
                                        <td><?php echo $fetch_products['measure']; ?></td>
                                        <td>
                                            <form method="post" action="">
                                                <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                                                <input type="number" name="qty" required min="1" value="<?= $fetch_cart['qty']; ?>" max="99" maxlength="2" class="qty edit">
                                                <button type="submit" name="update_cart" title="Modifică">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                                                <a href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" class="delete" onclick="return confirm('You really want to delete <?php echo $fetch_products['name']; ?> from the cart?');"><i class="fas fa-trash-alt" title="Șterge"></i></a>
                                            </form>
                                        </td>
                                        <td><?= $fetch_products['price']; ?> Ron</td>
                                        <td><?= $sub_total; ?> Ron</td>
                                    </tr>
                            <?php
                                } else {
                                    echo '<tr><td colspan="5">Produsul nu a fost găsit</td></tr>';
                                    // echo '<tr><td colspan="5">Product was not found</td></tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                <?php } else {
                echo '<p class="empty">Nu au fost adăugate produse!</p>';
                // echo '<p class="empty">No products added yet!</p>';
            } ?>

                <?php if ($grand_total != 0) { ?>
                    <div class="cart-total">
                        <p>Total: <span> <?= $grand_total; ?> Ron</span></p>
                        <div class="button">
                            <a href="view_menu.php" class="cart-btn">Continuă cumpărăturile</a>
                            <form method="post">
                                <button type="submit" name="empty_cart" class="cart-btn transparent-button" onclick="return confirm('Confirmi golirea coșului?')"><i class="fas fa-trash-alt" title="Golește"></i> Golește coșul</button>
                            </form>
                            <a href="checkout.php" class="cart-btn">Comandă</a>

                        </div>

                    </div>
                <?php } ?>
                </div>
        </section>


        <!-- END MAIN -->
    </main>

    <!-- FOOTER SECTION -->
    <section id="menu">
        <?php include 'components/footer.php'; ?>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="script.js"></script>
    <?php include 'components/alert.php'; ?>
</body>

</html>