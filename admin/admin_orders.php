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
if ($_SESSION['user_type'] === 'user') {
    header('location:../home.php');
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: ../login.php");
}

$current_page = basename($_SERVER['PHP_SELF']);
$messages = array();

//delete order 
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `orders` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
    } catch (PDOException $e) {
        echo "Error deleting order: " . $e->getMessage();
        $error_msg[] = "Error deleting order: " . $e->getMessage();
    } catch (Exception $e) {
        $error_msg[] = "Error: " . $e->getMessage();
        echo "Error deleting order: " . $e->getMessage();
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

// //for checkbox order_status
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Get the announcement ID and is_set value from the AJAX request
//     $orderId = $_POST['id'];
//     $isSet = $_POST['order_status'];

//     try {
//         // Update the is_set value in the database for the specified announcement ID
//         $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
//         $stmt->execute([$isSet, $orderId]);
//         $success_msg[] = "Statusul comenzii a fost modificat!";
//         echo json_encode(array('success' => true));
//     } catch (PDOException $e) {
//         // Return a JSON response indicating a database error
//         echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
//     }
// } else {
//     // Return a JSON response indicating an error for invalid request method
//     echo json_encode(array('error' => 'Invalid request method'));
// }

// //for payment_status checkbox
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Get the announcement ID and is_set value from the AJAX request
//     $orderId = $_POST['id'];
//     $isSet = $_POST['payment_status'];

//     try {
//         // Update the is_set value in the database for the specified announcement ID
//         $stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
//         $stmt->execute([$isSet, $orderId]);
//         $success_msg[] = "Statusul plății comenzii a fost modificat!";
//         echo json_encode(array('success' => true));
//     } catch (PDOException $e) {
//         // Return a JSON response indicating a database error
//         echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
//     }
// } else {
//     // Return a JSON response indicating an error for invalid request method
//     echo json_encode(array('error' => 'Invalid request method'));
// }

// //for both checkboxes
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Get the announcement ID, status type, and is_set value from the AJAX request
//     $orderId = $_POST['id'];
//     $statusType = $_POST['status_type']; // 'order_status' or 'payment_status'
//     $isSet = $_POST['is_set']; // Value of the checkbox (e.g., 'completed', 'pending', etc.)

//     try {
//         // Update the status value in the database for the specified order ID
//         $stmt = $conn->prepare("UPDATE orders SET $statusType = ? WHERE id = ?");
//         $stmt->execute([$isSet, $orderId]);

//         if ($statusType === 'order_status') {
//             $success_msg[] = "Statusul comenzii a fost modificat!";
//         } elseif ($statusType === 'payment_status') {
//             $success_msg[] = "Statusul plății comenzii a fost modificat!";
//         }

//         echo json_encode(array('success' => true));
//     } catch (PDOException $e) {
//         // Return a JSON response indicating a database error
//         echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
//         $error_msg[] = 'Database error: ' . $e->getMessage();
//     }
// } else {
//     // Return a JSON response indicating an error for invalid request method
//     echo json_encode(array('error' => 'Invalid request method'));
//     $error_msg[] = 'Error  Invalid request method';
// }


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

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
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
                                    <div class="widget order-widget">
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

                                            // $stmt = $conn->prepare("SELECT * FROM orders ");
                                            // $stmt->execute();
                                            // $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            // $num_of_orders = count($result); 
                                        } catch (PDOException $e) {
                                            die("Query failed: " . $e->getMessage());
                                            $error_msg[] = 'Error PDO' . $e->getMessage();
                                        } catch (Exception $e) {
                                            $error_msg[] = 'Error' . $e->getMessage();
                                        }
                                        ?>
                                        <div class="flex">
                                            <div class="small-widget">
                                                <i class="bx bx-receipt"></i>
                                            </div>
                                            <h3><?php echo $todayOrders; ?> Comenzi</h3>
                                        </div>
                                        <h5>din <?php echo $totalOrders; ?> înregistrate</h5>

                                    </div>
                                </a>
                            </section>

                            <!--Add New User Modal box -->
                            <!-- <section class="modal" id="product-modal">
                                <div class="modal-content">
                                    <span class="close" id="close-modal">&times;</span>
                                    <div class="form-container">
                                        <h2>Produs nou</h2>

                                        <form class="Form" action="admin_products.php" method="post" enctype="multipart/form-data">

                                            <label for="add-name">Product Name:</label>
                                            <input type="text" name="add_name" id="add-name" required>
                                            <label for="product-category">Catgoria:</label>
                                            <select name="category" id="product-category">
                                                <option value="soup">Supă/Ciorbă</option>
                                                <option value="principal">Garnitură/Fel principal</option>
                                                <option value="desert">Desert</option>
                                                <option value="beverages">Băuturi</option>
                                                <option value="altele">Altele</option>
                                            </select>
                                            <label for="add-detail">Product Detail:</label>
                                            <textarea name="add_detail" id="add-detail" required></textarea>
                                            <label for="add-price">Product Price:</label>
                                            <input type="number" name="add_price" id="add-price" required>
                                            <label for="measure">Unitatea de Masura:</label>
                                            <input type="text" name="measure" id="measure" required>

                                            <label for="add-image">Product Image:</label>
                                            <input type="file" name="add_image" id="add-image" required>

                                            <input class="form-button" type="submit" name="add_product" value="INREGISTREAZA">
                                        </form>
                                    </div>

                                </div>
                            </section> -->
                            <!-- order table -->
                            <section>
                                <?php
                                try {
                                    $stmt = $conn->prepare("SELECT o.id, u.name, COUNT(oi.id) AS num_items, o.payment_status, o.order_status, o.total_amount, o.order_date
                           FROM orders o
                           INNER JOIN order_items oi ON o.id = oi.order_id
                           INNER JOIN users u ON o.user_id = u.id
                           WHERE o.user_id = ?
                           GROUP BY o.id, u.name, o.payment_status, o.order_status, o.total_amount, o.order_date
                           ORDER BY o.order_date");
                                    $stmt->execute([$user_id]);

                                    if ($stmt->rowCount() > 0) {
                                ?>
                                        <div class="product-table-container">
                                            <table id="product-table" class="product-table">
                                                <thead>
                                                    <tr>
                                                        <th>Nr.</th>
                                                        <th>ID Comandă</th>
                                                        <th class="sortable" data-sort="string" data-column="name">Nume client</th>
                                                        <th>Nr. de produse</th>
                                                        <th class="sortable" data-sort="string" data-column="order_status">Status livrare</th>
                                                        <th class="sortable" data-sort="string" data-column="payment_status">Status plată</th>
                                                        <th class="sortable" data-sort="string" data-column="total_amount">Total</th>
                                                        <th class="sortable" data-sort="string" data-column="order_date">Data adăugării</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $nr = 0;
                                                    while ($order = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        $nr++;

                                                    ?>
                                                        <tr class="filter">
                                                            <td><?php echo $nr; ?></td>
                                                            <td><a href="admin_view_order.php?oid=<?php echo $order['id']; ?>&total_amount=<?php echo $order['total_amount']; ?>" title="Vezi comanda"><?php echo $order['id']; ?></a></td>
                                                            <td><?php echo $order['name']; ?></td>
                                                            <td><?php echo $order['num_items']; ?></td>

                                                            <td>
                                                                <!-- <form class="status-form" action=".php" method="post"> -->
                                                                <div class="flex">
                                                                    <?php echo $order['order_status']; ?>
                                                                    <input type="checkbox" class="status-checkbox" title="Setează livrarea comenzii" data-order-id="<?php echo $order['id']; ?>" data-status-type="order_status" <?php echo ($order['order_status'] == 'delivered') ? 'checked' : ''; ?>>
                                                                </div>
                                                                <!-- </form> -->
                                                            </td>
                                                            <td>
                                                                <!-- <form class="status-form" action="admin_orders.php" method="post"> -->
                                                                <div class="flex">
                                                                    <?php echo $order['payment_status']; ?>
                                                                    <input type="checkbox" class="status-checkbox" title="Setează efectuarea plății" data-order-id="<?php echo $order['id']; ?>" data-status-type="payment_status" <?php echo ($order['payment_status'] == 'completed') ? 'checked' : ''; ?>>
                                                                </div>
                                                                <!-- </form> -->
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
                                    $error_msg[] = "Error fetching order items: " . $e->getMessage();
                                } catch (Exception $e) {
                                    $error_msg[] = "Error: " . $e->getMessage();
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
    <script src="../js/searchCard.js"></script>
    <script src="../script.js"></script>
    <!-- 
    <script>
        //for setting announcement through checkbox
        $(document).ready(function() {
            // Function to handle checkbox click event
            $(".order-status-checkbox").click(function() {
                const recordId = $(this).data("order-id");
                const isSet = $(this).prop("checked") ? 'delivered' : 'processing';

                // Send the AJAX request to update the is_set value in the database
                $.ajax({
                    type: "POST",
                    url: "admin_orders.php", // Replace with the PHP script that updates the database
                    data: {
                        id: recordId,
                        status_type: 'order_status',
                        is_set: isSet
                    },
                    dataType: "json",
                    success: function(response) {
                        // Handle the response if needed
                        if (response.success) {
                            $("#success-message").text("Modificarea s-a efectuat cu succes!!"); // Set the success message
                            setTimeout(function() {
                                $("#success-message").empty();
                            }, 3000);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                    }
                });
            });
            $(".payment-status-checkbox").click(function() {
                const recordId = $(this).data("order-id");
                const isSet = $(this).prop("checked") ? 'completed' : 'pending';

                // Send the AJAX request to update the is_set value in the database
                $.ajax({
                    type: "POST",
                    url: "admin_orders.php", // Replace with the PHP script that updates the database
                    data: {
                        id: recordId,
                        is_set: isSet
                    },
                    dataType: "json",
                    success: function(response) {
                        // Handle the response if needed
                        if (response.success) {
                            $("#success-message").text("Modificarea s-a efectuat cu succes!"); // Set the success message
                            setTimeout(function() {
                                $("#success-message").empty();
                            }, 3000);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                    }
                });
            });
        });
    </script> -->
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
                    backgroundColor: "rgba(244, 211, 94, 1.0)",
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