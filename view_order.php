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

// Handle order 

// if (isset($_POST['order'])) {
//     $user_id = $_SESSION['user_id'];
//     $id = unique_id();

//     try {
//         $conn->beginTransaction();

//         // Step 1: Retrieve Cart Data
//         $stmt_cart = $conn->prepare("SELECT product_id, qty, price FROM cart WHERE user_id = ?");
//         $stmt_cart->execute([$user_id]);
//         $cart_data = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

//         // Step 2: Create Order
//         $order_date = date('Y-m-d H:i:s'); // Current date and time
//         $total_amount = 0;

//         foreach ($cart_data as $cart_item) {
//             $total_amount += ($cart_item['qty'] * $cart_item['price']);
//         }

//         $payment_status = 'pending';
//         $shipping_address = '';
//         $order_status = 'processing';

//         $stmt_order = $conn->prepare("INSERT INTO orders (id, user_id, order_date, total_amount, payment_status, shipping_address, order_status) 
//                                      VALUES (?, ?, ?, ?, ?, ?, ?)");

//         $stmt_order->execute([$id, $user_id, $order_date, $total_amount, $payment_status, $shipping_address, $order_status]);

//         // $order_id = $conn->lastInsertId(); // Get the last inserted order_id IF is AUTO-INCREMENTED
//         $order_id = $id;
//         // Step 3: Store Order Items
//         foreach ($cart_data as $cart_item) {
//             $product_id = $cart_item['product_id'];
//             $quantity = $cart_item['qty'];
//             $price = $cart_item['price'];
//             $subtotal = $quantity * $price;

//             $stmt_order_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
//                                                VALUES (?, ?, ?, ?, ?)");

//             $stmt_order_items->execute([$order_id, $product_id, $quantity, $price, $subtotal]);
//         }

//         // Step 4: Empty Cart
//         $stmt_empty_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
//         $stmt_empty_cart->execute([$user_id]);

//         $conn->commit(); // Commit the transaction
//         $success_msg[] = "Comanda a fost plasată! ";

//         // echo "Order placed successfully!";
//     } catch (PDOException $e) {
//         $conn->rollBack(); // Rollback the transaction in case of any error
//         $error_msg[] = "Eroare la plasarea comenzii: " . $e->getMessage();

//         // echo "Error placing the order: " . $e->getMessage();
//     } catch (Exception $e) {
//         $conn->rollBack(); // Rollback the transaction in case of any error
//         // echo "Error: " . $e->getMessage();
//         $error_msg[] = "Eroare la plasarea comenzii: " . $e->getMessage();
//     }
// }


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css">

    <title>Cantina - contact</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!--NAVBAR  HEADER SECTION -->
    <section>
        <?php include 'components/header.php'; ?>
    </section>

    <main class="main">
        <div class="banner" style="height:200px; ">
            <h1 style="color: var(--green)">detalii comandă </h1>
        </div>
        <div class="title2">
            <a href="view_orders.php">comenzi </a><span>/ detalii comandă cu seria: <?php echo $_GET['oid'] ?></span>
        </div>

        <!-- order_item table -->
        <section>
            <?php
            try {
                if (isset($_GET['oid'])) {
                    $oid = $_GET['oid'];
                    $stmt_order_items = $conn->prepare("SELECT oi.id, p.name, p.image, oi.quantity, oi.price, oi.subtotal, oi.product_id
                                       FROM order_items oi
                                       INNER JOIN products p ON oi.product_id = p.id
                                       WHERE oi.order_id = ?");
                    $stmt_order_items->execute([$oid]);

                    if ($stmt_order_items->rowCount() > 0) {
            ?>
                        <div id="popup-container" style="display: none;">
                            <img id="popup-image" src="" alt="popup image">
                        </div>
                        <div class="product-table-container">
                            <table id="product-table" class="product-table">
                                <thead>
                                    <tr>
                                        <!-- <th></th> -->
                                        <th></th>
                                        <th>Nume produs</th>
                                        <th>Cantitate</th>
                                        <th>Preț</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($order_item = $stmt_order_items->fetch(PDO::FETCH_ASSOC)) {
                                    ?>
                                        <tr>
                                            <!-- <td><?php echo $order_item['id']; ?></td> -->
                                            <td> <img src="image/<?php echo $order_item['image']; ?>" alt="img" class="product-image"></td>

                                            <td><a style="color: var(--dark-olive);" href="view_item.php?pid=<?php echo $order_item['product_id']; ?>"><?php echo $order_item['name']; ?></a></td>
                                            <!-- <td title="<?php echo $product['name']; ?>"><a href="admin_view_product.php?pid=<?php echo $product['id']; ?>"><?php echo substr($product['name'], 0, 25) . '...'; ?></a></td> -->

                                            <td><?php echo $order_item['quantity']; ?></td>
                                            <td><?php echo $order_item['price']; ?></td>
                                            <td><?php echo $order_item['subtotal']; ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
            <?php
                    } else {
                        echo '<p class="empty">No order items found!</p>';
                    }
                }
            } catch (PDOException $e) {
                $error_msg[] = "Error fetching order items: " . $e->getMessage();
            }

            ?>
            <div class="cart-total">
                <?php if (isset($_GET['total_amount'])) {
                ?>
                    <p>Total: <span> <?= $_GET['total_amount']; ?> Ron</span></p>
                <?php } ?>
            </div>
        </section>

    </main>
    <?php include 'components/footer.php'; ?>
    <?php include 'components/alert.php'; ?>

    <script src="script.js"></script>
</body>

</html>