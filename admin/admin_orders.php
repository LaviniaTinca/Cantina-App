<?php
include '../php/connection.php';
include '../php/session_handler.php';

//delete order 
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `orders` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
    } catch (PDOException $e) {
        $error_msg[] = "Eroare la ștergerea comenzii: " . $e->getMessage();
    } catch (Exception $e) {
        $error_msg[] = "Eroare: " . $e->getMessage();
    }
}

//for chart
try {
    // Assuming your table structure has a `created_at` field for the date
    $stmt = $conn->prepare("SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, COUNT(*) AS record_count FROM orders GROUP BY month");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Extract xValues and yValues from the result
    $xValues = array_column($result, 'month');
    $yValues = array_column($result, 'record_count');
} catch (PDOException $e) {
    // Handle any errors that may occur during database query
    die("Query failed: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
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
                            <!-- WIDGETS -->
                            <section class="widgets">
                                <canvas id="myChart" style="max-width:400px"></canvas>
                                <a href="admin_orders.php">
                                    <div class="widget order-widget" style="width: max-content;">
                                        <?php
                                        try {
                                            // Get the total number of orders
                                            $totalOrdersQuery = $conn->prepare("SELECT COUNT(*) AS total_orders FROM orders");
                                            $totalOrdersQuery->execute();
                                            $totalOrdersResult = $totalOrdersQuery->fetch(PDO::FETCH_ASSOC);
                                            $totalOrders = $totalOrdersResult['total_orders'];

                                            // Get the number of orders from today
                                            $today = date('Y-m-d');
                                            $todayOrdersQuery = $conn->prepare("SELECT COUNT(*) AS today_orders FROM orders WHERE DATE(order_date) = ?");
                                            $todayOrdersQuery->execute([$today]);
                                            $todayOrdersResult = $todayOrdersQuery->fetch(PDO::FETCH_ASSOC);
                                            $todayOrders = $todayOrdersResult['today_orders'];
                                        } catch (PDOException $e) {
                                            $error_msg[] = 'Error PDO' . $e->getMessage();
                                        } catch (Exception $e) {
                                            $error_msg[] = 'Error' . $e->getMessage();
                                        }
                                        ?>
                                        <div class="flex">
                                            <div class="small-widget">
                                                <i class="bx bx-receipt"></i>
                                            </div>
                                            <h3><?php echo $todayOrders; ?> Comenzi azi</h3>
                                        </div>
                                        <h5>din <?php echo $totalOrders; ?> înregistrate</h5>
                                    </div>
                                </a>
                            </section>

                            <!-- order table -->
                            <section>
                                <?php
                                try {
                                    $query = "SELECT o.id, u.name, COUNT(oi.id) AS num_items, o.payment_status, o.order_status, o.total_amount, o.order_date
                                                FROM orders o
                                                INNER JOIN order_items oi ON o.id = oi.order_id
                                                INNER JOIN users u ON o.user_id = u.id
                                                GROUP BY o.id, u.name, o.payment_status, o.order_status, o.total_amount, o.order_date
                                                ORDER BY o.order_date DESC";
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();

                                    if ($stmt->rowCount() > 0) {
                                ?>
                                        <div class="product-table-container">
                                            <table id="product-table" class="product-table">
                                                <thead>
                                                    <tr>
                                                        <th class="sortable">Nr.</th>
                                                        <th class="sortable">ID Comandă</th>
                                                        <th class="sortable" data-column="name">Nume client</th>
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
                                                    $nr = 0;
                                                    while ($order = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        $nr++;
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $nr; ?></td>
                                                            <td><a href="admin_view_order.php?oid=<?php echo $order['id']; ?>&total_amount=<?php echo $order['total_amount']; ?>" title="Vezi comanda"><?php echo $order['id']; ?></a></td>
                                                            <td><?php echo $order['name']; ?></td>
                                                            <td><?php echo $order['num_items']; ?></td>

                                                            <td>
                                                                <div class="flex">
                                                                    <!-- <?php echo $order['order_status']; ?> -->
                                                                    <?php echo ($order['order_status'] == 'delivered') ? 'livrat' : 'procesare'; ?>
                                                                    <input type="checkbox" class="status-checkbox" title="Setează livrarea comenzii" data-order-id="<?php echo $order['id']; ?>" data-status-type="order_status" <?php echo ($order['order_status'] == 'delivered') ? 'checked' : ''; ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="flex">
                                                                    <!-- <?php echo $order['payment_status']; ?> -->
                                                                    <?php echo ($order['payment_status'] == 'completed') ? 'achitat' : 'în așteptare'; ?>
                                                                    <input type="checkbox" class="status-checkbox" title="Setează efectuarea plății" data-order-id="<?php echo $order['id']; ?>" data-status-type="payment_status" <?php echo ($order['payment_status'] == 'completed') ? 'checked' : ''; ?>>
                                                                </div>
                                                            </td>

                                                            <td><?php echo $order['total_amount']; ?></td>
                                                            <td><?php echo $order['order_date']; ?></td>

                                                            <td>
                                                                <a href="admin_view_order.php?oid=<?php echo $order['id']; ?>&total_amount=<?php echo $order['total_amount']; ?>" title="Vezi comanda">
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
                                        echo '<p class="empty">Nu aveți elemente in comanda!!</p>';
                                    }
                                } catch (PDOException $e) {
                                    $error_msg[] = "Eroare: " . $e->getMessage();
                                } catch (Exception $e) {
                                    $error_msg[] = "Eroare: " . $e->getMessage();
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
    <script src="../js/script.js"></script>

    <script>
        $(document).ready(function() {
            $(".status-checkbox").click(function() {
                const recordId = $(this).data("order-id");
                const statusType = $(this).data("status-type");
                const isSet = $(this).prop("checked") ? (statusType === 'order_status' ? 'delivered' : 'completed') : (statusType === 'order_status' ? 'processing' : 'pending');

                // Send the AJAX request to update the status value in the database
                $.ajax({
                    type: "POST",
                    url: "set_order_status.php",
                    data: {
                        id: recordId,
                        status_type: statusType,
                        is_set: isSet
                    },
                    dataType: "json",
                    success: function(response) {
                        // Handle the response if needed
                        if (response.success) {
                            const successMessage = (statusType === 'order_status') ? "Statusul comenzii a fost modificat!" : "Statusul plății comenzii a fost modificat!";
                            $("#success-message").text(successMessage);
                            setTimeout(function() {
                                $("#success-message").empty();
                            }, 3000);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                        const errorMessage = "A apărut o eroare. Vă rugăm să reîncercați mai târziu.";
                        $("#error-message").text(errorMessage);
                    }
                });
            });
        });
    </script>

    <script>
        // Use PHP's json_encode function to convert PHP arrays to JavaScript arrays
        const xValues = <?php echo json_encode($xValues); ?>;
        const yValues = <?php echo json_encode($yValues); ?>;

        new Chart("myChart", {
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                    fill: true,
                    lineTension: 0,
                    backgroundColor: "rgba(212, 175, 55, 1.0)",
                    borderColor: "rgba(244, 211, 94, 0.1)",
                    data: yValues
                }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            callback: function(value, index, values) {
                                return yValues.includes(value) ? value : '';
                            }
                        }
                    }],
                }
            }
        });
    </script>

</body>

</html>