<?php
include 'php/connection.php';
include 'php/session.php'

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - contact</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!--NAVBAR  HEADER SECTION -->
    <section>
        <?php include 'components/header.php'; ?>
    </section>

    <main class="main">
        <div class="banner">
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
                                            <td> <img src="image/<?php echo $order_item['image']; ?>" alt="img" class="product-image"></td>
                                            <td><a style="color: var(--dark-olive);" href="view_item.php?pid=<?php echo $order_item['product_id']; ?>"><?php echo $order_item['name']; ?></a></td>
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
                        echo '<p class="empty">Nu s-au găsit elementele comenzii!</p>';
                    }
                }
            } catch (PDOException $e) {
                $error_msg[] = "Eroare: " . $e->getMessage();
            } catch (Exception $e) {
                $error_msg[] = "Eroare: " . $e->getMessage();
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

    <script src="js/script.js"></script>
</body>

</html>