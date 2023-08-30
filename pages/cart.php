<?php
include '../config/connection.php';
include '../config/session.php';
include '../api/functions.php';

//update product in cart
if (isset($_POST['update_cart'])) {
    try {

        $cart_id = $_POST['cart_id'];
        $cart_id = htmlspecialchars($cart_id, ENT_QUOTES, 'UTF-8');
        $qty = $_POST['qty'];
        $qty = htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');

        $update_qty = $conn->prepare("UPDATE `cart` SET qty = ? WHERE id = ?");
        $update_qty->execute([$qty, $cart_id]);
        $success_msg[] = 'cantitatea produsului a fost modificata';
    } catch (PDOException $th) {
        $error_msg = 'Eroare ' . $th->getMessage();
    } catch (Exception $th) {
        $error_msg = 'Eroare' . $th->getMessage();
    }
}

//delete menu item
if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `cart` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        $success_msg[] = "Produsul a fost șters din coș!";
    } catch (PDOException $th) {
        $error_msg = 'Eroare ' . $th->getMessage();
    } catch (Exception $th) {
        $error_msg = 'Eroare' . $th->getMessage();
    }
}

//empty cart
if (isset($_POST['empty_cart'])) {
    try {
        $verify_empty_item = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
        $verify_empty_item->execute([$user_id]);

        if ($verify_empty_item->rowCount() > 0) {
            $delete_cart_id = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $delete_cart_id->execute([$user_id]);
            $success_msg[] = "Coșul este gol! ";
        } else {
            $warning_msg[] = 'Nu s-a putut goli coșul!';
        }
    } catch (PDOException $th) {
        $error_msg = 'Eroare ' . $th->getMessage();
    } catch (Exception $th) {
        $error_msg = 'Eroare' . $th->getMessage();
    }
}

//handle order
if (isset($_POST['order'])) {
    $user_id = $_SESSION['user_id'];

    try {
        $conn->beginTransaction();

        // Step 1: Retrieve Cart Data
        $query = "SELECT c.*, dmi.qty AS dmi_qty, dmi.id AS dmi_id
                    FROM cart c
                    LEFT JOIN daily_menu dm ON dm.date = CURDATE()
                    LEFT JOIN daily_menu_items dmi ON dmi.daily_menu_id = dm.id AND dmi.product_id = c.product_id
                    WHERE c.user_id = ?";
        $stmt_cart = $conn->prepare($query);
        $stmt_cart->execute([$user_id]);
        $cart_data = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

        // Step 2: Create Order
        $order_date = date('Y-m-d H:i:s'); // Current date and time
        $total_amount = 0;

        foreach ($cart_data as $cart_item) {
            if ($cart_item['dmi_qty'] < $cart_item['qty']) {
                $product_query = $conn->prepare("SELECT name FROM products WHERE id = ?");
                $product_query->execute([$cart_item['product_id']]);
                $product_name = $product_query->fetchColumn();
                $warning_msg[] = 'Pe stoc mai sunt ' . $cart_item['dmi_qty'] . ' portii din produsul ' . $product_name;
                $cart_item['qty'] = $cart_item['dmi_qty'];
            }
            $total_amount += ($cart_item['qty'] * $cart_item['price']);
        }

        $payment_status = 'pending';
        $shipping_address = '';
        $order_status = 'processing';
        $id = unique_id();

        $query = "INSERT INTO orders (id, user_id, order_date, total_amount, payment_status, shipping_address, order_status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_order = $conn->prepare($query);
        $stmt_order->execute([$id, $user_id, $order_date, $total_amount, $payment_status, $shipping_address, $order_status]);

        $order_id = $id;
        // Step 3: Store Order Items
        foreach ($cart_data as $cart_item) {
            if ($cart_item['dmi_qty'] > 0) {
                if ($cart_item['dmi_qty'] < $cart_item['qty']) {
                    $quantity = $cart_item['dmi_qty'];
                } else {
                    $quantity = $cart_item['qty'];
                }
                $product_id = $cart_item['product_id'];
                $price = $cart_item['price'];
                $subtotal = $quantity * $price;

                $query = "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt_order_items = $conn->prepare($query);
                $stmt_order_items->execute([$order_id, $product_id, $quantity, $price, $subtotal]);

                //update the qty in the menu
                $stmt_update_menu_qty = $conn->prepare("UPDATE `daily_menu_items` SET qty = ? WHERE id = ?");
                $stmt_update_menu_qty->execute([$cart_item['dmi_qty'] - $quantity, $cart_item['dmi_id']]);
            }
        }

        // Step 4: Empty Cart
        $stmt_empty_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt_empty_cart->execute([$user_id]);

        $conn->commit(); // Commit the transaction
        $success_msg[] = "Comanda a fost plasată! Sunteti asteptati sa o ridicati! ";
    } catch (PDOException $e) {
        $conn->rollBack(); // Rollback the transaction in case of any error
        $error_msg[] = "Eroare la plasarea comenzii: " . $e->getMessage();
    } catch (Exception $e) {
        $conn->rollBack();
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
    <link rel="stylesheet" href="../css/style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</head>

<body>
    <!-- HEADER SECTION -->
    <section>
        <?php include '../components/header.php'; ?>
    </section>

    <main class="main">
        <div class="banner">
            <h1 style="color: var(--green)">Coșul de cumpărături</h1>
        </div>
        <div class="title2">
            <a href="../pages/home.php">acasă </a><span>/ coș</span>
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
                                <th class="th-cart">Cantitate</th>
                                <th class="th-cart">Nr. de porții</th>
                                <th class="th-cart">Preț unitar</th>
                                <th class="th-cart">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                                $sub_total = 0;
                                $query = "SELECT products.*, dmi.id AS menu_id, dmi.qty AS qty
                                            FROM daily_menu
                                            JOIN daily_menu_items AS dmi ON dmi.daily_menu_id = daily_menu.id
                                            JOIN products ON dmi.product_id = products.id
                                            WHERE daily_menu.date = CURDATE() and products.id = ?";
                                $select_product = $conn->prepare($query);
                                $select_product->execute([$fetch_cart['product_id']]);
                                if ($select_product->rowCount() > 0) {
                                    $questioned_product = $select_product->fetch(PDO::FETCH_ASSOC);
                                    $finished = ($questioned_product['qty'] <= 0) ? 'finished' : '';
                                }

                                $select_products = $conn->prepare("SELECT * FROM `products` WHERE id= ?");
                                $select_products->execute([$fetch_cart['product_id']]);
                                if ($select_products->rowCount() > 0) {
                                    $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                                    if ($fetch_cart['qty'] <= $questioned_product['qty']) {
                                        $sub_total = $fetch_cart['qty'] * $fetch_products['price'];
                                    } else {
                                        $sub_total = $questioned_product['qty'] * $fetch_products['price'];
                                    }
                                    $grand_total += $sub_total;
                            ?>
                                    <tr class="<?php echo $finished; ?>">
                                        <td>
                                            <a href="view_item.php?php echo $fetch_products['id']; ?>">
                                                <img src=" ../public/image/<?= $fetch_products['image']; ?>" alt="product image" class="img">
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
                                                <input type="number" name="qty" required min="1" value="<?php echo $questioned_product['qty'] > $fetch_cart['qty'] ? $fetch_cart['qty'] : $questioned_product['qty'];
                                                                                                        ?>" max="<?php echo $questioned_product['qty']; ?>" title="<?php echo $questioned_product['qty'] > $fetch_cart['qty'] ? $fetch_cart['qty'] : 'pe stoc sunt' . $questioned_product['qty'] . ' porții disponibile'; ?>" maxlength="2" class="qty edit">
                                                <button type="submit" name="update_cart" title="Modifică">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                                                <a href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" class="delete" onclick="return confirm('Dorești să ștergi produsul <?php echo $fetch_products['name']; ?> din coș?');">
                                                    <i class="fas fa-trash-alt" title="Șterge"></i>
                                                </a>
                                            </form>
                                        </td>
                                        <td><?= $fetch_products['price']; ?> Ron</td>
                                        <td><?= $sub_total; ?> Ron</td>
                                    </tr>
                            <?php
                                } else {
                                    echo '<tr><td colspan="5">Produsul nu a fost găsit</td></tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                <?php } else {
                echo '<p class="empty">Coșul de cumpărături este gol!</p>';
                echo '
                <div class="button flex" style="width:30%">
                <a href="../pages/view_menu.php" class="cart-btn">Continuă cumpărăturile</a>
                </div>
                ';
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
                                <button type="submit" name="order" style="margin: .5rem;" class="cart-btn transparent-button"><i class="fas fa-shopping-cart" title="Comanda"></i> Comandă</button>
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
        <?php include '../components/footer.php'; ?>
    </section>
    <?php include '../components/alert.php'; ?>

    <script src="../js/script.js"></script>
</body>

</html>