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
    $success_msg[] = 'cantitatea produsului a fost modificata';
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
        $error_msg[] = $e->getMessage();

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
        $success_msg[] = "Coșul este gol! ";
    } else {
        $warning_msg[] = 'coșul nu s-a putut goli!';
    }
}

if (isset($_POST['order'])) {
    $user_id = $_SESSION['user_id'];
    $id = unique_id();

    try {
        $conn->beginTransaction();

        // Step 1: Retrieve Cart Data
        $stmt_cart = $conn->prepare("SELECT product_id, qty, price FROM cart WHERE user_id = ?");
        $stmt_cart->execute([$user_id]);
        $cart_data = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

        // Step 2: Create Order
        $order_date = date('Y-m-d H:i:s'); // Current date and time
        $total_amount = 0;

        foreach ($cart_data as $cart_item) {
            $total_amount += ($cart_item['qty'] * $cart_item['price']);
        }

        $payment_status = 'pending';
        $shipping_address = '';
        $order_status = 'processing';

        $stmt_order = $conn->prepare("INSERT INTO orders (id, user_id, order_date, total_amount, payment_status, shipping_address, order_status) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt_order->execute([$id, $user_id, $order_date, $total_amount, $payment_status, $shipping_address, $order_status]);

        // $order_id = $conn->lastInsertId(); // Get the last inserted order_id IF is AUTO-INCREMENTED
        $order_id = $id;
        // Step 3: Store Order Items
        foreach ($cart_data as $cart_item) {
            $product_id = $cart_item['product_id'];
            $quantity = $cart_item['qty'];
            $price = $cart_item['price'];
            $subtotal = $quantity * $price;

            $stmt_order_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
                                               VALUES (?, ?, ?, ?, ?)");

            $stmt_order_items->execute([$order_id, $product_id, $quantity, $price, $subtotal]);
        }

        // Step 4: Empty Cart
        $stmt_empty_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt_empty_cart->execute([$user_id]);

        $conn->commit(); // Commit the transaction
        $success_msg[] = "Comanda a fost plasată! ";

        // echo "Order placed successfully!";
    } catch (PDOException $e) {
        $conn->rollBack(); // Rollback the transaction in case of any error
        $error_msg[] = "Eroare la plasarea comenzii: " . $e->getMessage();

        // echo "Error placing the order: " . $e->getMessage();
    } catch (Exception $e) {
        $conn->rollBack(); // Rollback the transaction in case of any error
        // echo "Error: " . $e->getMessage();
        $error_msg[] = "Eroare la plasarea comenzii: " . $e->getMessage();
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
                                            <a href="view_item.php?pid=<?php echo $fetch_products['id']; ?>">
                                                <img src=" image/<?= $fetch_products['image']; ?>" alt="product image" class="img">
                                            </a>
                                        </td>
                                        <td>
                                            <a href="view_item.php?pid=<?php echo $fetch_products['id']; ?>">
                                                <h3 class="name"><?= $fetch_products['name']; ?></h3>
                                            </a>
                                        </td>
                                        <td><?php echo $fetch_products['measure']; ?></td>
                                        <td>
                                            <form method="post" action="cart.php">
                                                <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                                                <input type="number" name="qty" required min="1" value="<?= $fetch_cart['qty']; ?>" max="99" maxlength="2" class="qty edit">
                                                <button type="submit" name="update_cart" title="Modifică">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                                                <a href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" class="delete" onclick="return confirm('Dorești să ștergi produsul <?php echo $fetch_products['name']; ?> din coș?');"><i class="fas fa-trash-alt" title="Șterge"></i></a>
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
                echo '<p class="empty">Coșul de cumpărături este gol!</p>';
                echo '
                <div class="button flex" style="width:30%">
                <a href="view_menu.php" class="cart-btn">Continuă cumpărăturile</a>
                </div>
                ';
                // echo '<p class="empty">No products added yet!</p>';
            } ?>

                <?php if ($grand_total != 0) { ?>
                    <div class="cart-total">
                        <p>Total: <span> <?= $grand_total; ?> Ron</span></p>
                        <div class="button">
                            <a href="view_menu.php" class="cart-btn">Continuă cumpărăturile</a>
                            <form method="post">
                                <button type="submit" name="empty_cart" class="cart-btn transparent-button" onclick="return confirm('Dorești să golești coșul de cumpărături?')"><i class="fas fa-trash-alt" title="Golește"></i> Golește coșul</button>
                            </form>
                            <form action="cart.php" method="post">
                                <button type="submit" name="order" style="margin: .5rem;" class="cart-btn transparent-button">Comandă</button>
                            </form>
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