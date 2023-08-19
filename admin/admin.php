<?php
include '../php/connection.php';
include '../php/session_handler.php';

//for chart
try {
    $stmt = $conn->prepare("SELECT category, COUNT(*) AS product_count FROM products GROUP BY category");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Extract xValues and yValues from the result
    $xValues = array_column($result, 'category');
    $yValues = array_column($result, 'product_count');
} catch (PDOException $e) {
    $error_msg[] = "Eroare: " . $e->getMessage();
} catch (Exception $e) {
    $error_msg[] = "Eroare: " . $e->getMessage();
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>

<body>
    <!-- HEADER SECTION -->
    <section>
        <?php include('../components/admin/header.php'); ?>
    </section>

    <main class="main" style="margin-top: 50px">

        <!-- SIDEBAR AND PANEL-CONTAINER -->
        <section>
            <div class="admin-container">
                <?php include('../components/admin/sidebar.php'); ?>
                <div class="panel-container">
                    <div class=" content">
                        <!-- WIDGETS -->
                        <section class="widgets">
                            <a href="admin_users.php">
                                <div class="widget  jump user-widget">
                                    <?php
                                    try {
                                        $stmt = $conn->prepare("SELECT * FROM users WHERE user_type = 'user'");
                                        $stmt->execute();
                                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        $num_of_users = count($users);
                                    } catch (PDOException $e) {
                                        $error_msg[] = "Eroare: " . $e->getMessage();
                                    } catch (Exception $e) {
                                        $error_msg[] = "Eroare: " . $e->getMessage();
                                    }
                                    ?>
                                    <div class="small-widget">
                                        <i class='bx bx-group'></i>
                                    </div>
                                    <h3><?php echo $num_of_users; ?></h3>
                                    <p>Utilizatori</p>
                                </div>
                            </a>
                            <a href="admin_orders.php">
                                <div class="widget jump order-widget">
                                    <?php
                                    try {
                                        $stmt = $conn->prepare("SELECT * FROM orders");
                                        $stmt->execute();
                                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        $num_of_orders = count($result);
                                    } catch (PDOException $e) {
                                        $error_msg[] = "Eroare: " . $e->getMessage();
                                    } catch (Exception $e) {
                                        $error_msg[] = "Eroare: " . $e->getMessage();
                                    }
                                    ?>
                                    <div class="small-widget">
                                        <i class="bx bx-receipt"></i>
                                    </div>
                                    <h3><?php echo $num_of_orders; ?></h3>
                                    <p>Comenzi</p>
                                </div>
                            </a>

                            <a href="admin_products.php">
                                <div class="widget jump product-widget">
                                    <?php
                                    try {
                                        $stmt = $conn->prepare("SELECT * FROM products ");
                                        $stmt->execute();
                                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        $num_of_products = count($result);
                                    } catch (PDOException $e) {
                                        $error_msg[] = "Eroare: " . $e->getMessage();
                                    } catch (Exception $e) {
                                        $error_msg[] = "Eroare: " . $e->getMessage();
                                    }
                                    ?>
                                    <div class="small-widget">
                                        <i class="bx bx-package"></i>
                                    </div>
                                    <h3><?php echo $num_of_products; ?></h3>
                                    <p>Produse</p>
                                </div>
                            </a>

                            <a href="admin_messages.php">
                                <div class="widget jump message-widget">
                                    <?php
                                    try {
                                        $stmt = $conn->prepare("SELECT * FROM messages");
                                        $stmt->execute();
                                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        $num_of_messages = count($result);
                                    } catch (PDOException $e) {
                                        $error_msg[] = "Eroare: " . $e->getMessage();
                                    } catch (Exception $e) {
                                        $error_msg[] = "Eroare: " . $e->getMessage();
                                    }
                                    ?>
                                    <div class="small-widget">
                                        <i class="bx bx-envelope"></i>
                                    </div>
                                    <h3><?php echo $num_of_messages; ?></h3>
                                    <p>Mesaje</p>
                                </div>
                            </a>
                        </section>
                    </div>
                    <div class="banner" style="height: auto;">
                        <canvas id="myChart" style="width:70%;max-width:600px"></canvas>
                    </div>
                </div>
            </div>
            </div>
        </section>
        <!-- //END MAIN -->
    </main>

    <!-- SCRIPT SECTION -->
    <script src="../js/script.js"></script>
    <script>
        const xValues = <?php echo json_encode($xValues); ?>;
        const yValues = <?php echo json_encode($yValues); ?>;
        var barColors = [
            "#b91d47",
            "#00aba9",
            "#2b5797",
            "#e8c3b9",
            "#1e7145"
        ];

        new Chart("myChart", {
            type: "pie",
            data: {
                labels: xValues,
                datasets: [{
                    backgroundColor: barColors,
                    data: yValues
                }]
            },
            options: {
                title: {
                    display: true,
                    text: "Produsele noastre pe categorii - 2023"
                    // text: "Vanzari lunare - 2023"
                }
            }
        });
    </script>
</body>

</html>