<?php
include '../config/connection.php';
include '../config/session.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">

    <title>Cantina - contact</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!--NAVBAR  HEADER SECTION -->
    <section>
        <?php include '../components/header.php'; ?>
    </section>

    <main class="main">
        <div class="banner">
            <h1 style="color: var(--green)">Comenzi</h1>
        </div>
        <div class="title2">
            <a href="home.php">acasă </a><span>/ comenzi</span>
        </div>

        <!-- order table -->
        <section>
            <?php
            try {
                $query = "SELECT o.id, u.name, COUNT(oi.id) AS num_items, o.payment_status, o.order_status, o.total_amount, o.order_date
                            FROM orders o
                            INNER JOIN order_items oi ON o.id = oi.order_id
                            INNER JOIN users u ON o.user_id = u.id
                            WHERE o.user_id = ?
                            GROUP BY o.id, u.name, o.payment_status, o.order_status, o.total_amount, o.order_date
                            ORDER BY o.order_date DESC";
                $stmt = $conn->prepare($query);
                $stmt->execute([$user_id]);

                if ($stmt->rowCount() > 0) {
            ?>
                    <div class="product-table-container">
                        <table id="product-table" class="product-table">
                            <thead>
                                <tr>
                                    <th class="sortable">ID Comandă</th>
                                    <th class="sortable" data-column="num_items">Nr. de produse</th>
                                    <th class="sortable" data-column="order_status">Status livrare</th>
                                    <th class="sortable" data-column="payment_status">Status plată</th>
                                    <th class="sortable" data-column="total_amount">Total</th>
                                    <th class="sortable" data-column="order_date">Data adăugării</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($order = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                    <tr>
                                        <td><a style="color: var(--green)" href="view_order.php?oid=<?php echo $order['id']; ?>&total_amount=<?php echo $order['total_amount']; ?>" title="Vezi comanda"><?php echo $order['id']; ?></a></td>
                                        <td><?php echo $order['num_items']; ?></td>
                                        <td><?php echo ($order['order_status'] == 'delivered') ? 'livrat' : 'procesare'; ?></td>
                                        <td><?php echo ($order['payment_status'] == 'completed') ? 'achitat' : 'în așteptare'; ?></td>
                                        <td><?php echo $order['total_amount']; ?></td>
                                        <td><?php echo $order['order_date']; ?></td>
                                        <td>
                                            <a href="view_order.php?oid=<?php echo $order['id']; ?>&total_amount=<?php echo $order['total_amount']; ?>" title="Vezi comanda">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
            <?php
                } else {
                    echo '<p class="empty">Nu aveți comenzi!!</p>';
                }
            } catch (PDOException $e) {
                $error_msg[] = "Eroare: " . $e->getMessage();
            } catch (Exception $e) {
                $error_msg[] = "Eroare: " . $e->getMessage();
            }
            ?>
        </section>

    </main>
    <?php include '../components/footer.php'; ?>
    <?php include '../components/alert.php'; ?>

    <script src="../js/script.js"></script>
</body>

</html>