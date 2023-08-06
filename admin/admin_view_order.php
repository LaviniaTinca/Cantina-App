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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- HEADER SECTION -->
    <section>
        <?php include '../components/admin/header.php'; ?>
    </section>

    <main class="main" style="margin-top: 50px; ">
        <!-- SIDEBAR AND PANEL-CONTAINER  SECTION-->
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


                        <!-- order_item table -->
                        <section>
                            <?php
                            try {
                                if (isset($_GET['oid'])) {
                                    $oid = $_GET['oid'];

                                    $stmt = $conn->prepare('SELECT u.name FROM orders o INNER JOIN users u ON o.user_id = u.id WHERE o.id = ?');

                                    $stmt->execute([$oid]);
                                    $order = $stmt->fetch(PDO::FETCH_ASSOC);


                            ?>
                                    <div class="banner" style="height:50px; ">
                                        <h1 style="color: var(--green)">detalii comandă - <?php echo $order['name'] ?></h1>
                                    </div>
                                    <div class="title2">
                                        <a href="admin_orders.php">comenzi </a><span>/ detalii comandă cu seria: <?php echo $_GET['oid'] ?></span>
                                    </div>
                                    <?php
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
                                                        <th></th>
                                                        <th class="sortable" data-sort="string" data-column="name">Nume produs</th>
                                                        <th class="sortable" data-sort="string" data-column="qty">Cantitate</th>
                                                        <th class="sortable" data-sort="string" data-column="price">Preț</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    while ($order_item = $stmt_order_items->fetch(PDO::FETCH_ASSOC)) {
                                                    ?>
                                                        <tr class="filter">
                                                            <td> <img src="../image/<?php echo $order_item['image']; ?>" alt="img" class="product-image"></td>
                                                            <td><a style="color: var(--dark-olive);" href="admin_view_product.php?pid=<?php echo $order_item['product_id']; ?>"><?php echo $order_item['name']; ?></a></td>
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
                            } catch (Exception $e) {
                                $error_msg[] = "Error: " . $e->getMessage();
                            }


                            ?>
                            <div class="cart-total">
                                <?php if (isset($_GET['total_amount'])) {
                                ?>
                                    <p>Total: <span> <?= $_GET['total_amount']; ?> Ron</span></p>
                                <?php } ?>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>

        <!-- END MAIN -->
    </main>
    <?php include '../components/alert.php'; ?>
    <script src="../script.js"></script>

</body>

</html>