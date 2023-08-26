<?php
include '../config/connection.php';
include '../config/session_admin.php';

//delete message
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `messages` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        $success_msg[] = "Mesajul a fost șters!";
    } catch (PDOException $e) {
        $error_msg[] = "Eroare: " . $e->getMessage();
    } catch (Exception $e) {
        $error_msg[] = "Eroare: " . $e->getMessage();
    }
}

// chart
try {
    $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS record_count FROM messages GROUP BY month";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Extract xValues and yValues from the result
    $xValues = array_column($result, 'month');
    $yValues = array_column($result, 'record_count');
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                            <canvas id="myChart" style="max-width:400px"></canvas>
                            <a href="admin_messages.php">
                                <div class="widget message-widget">
                                    <?php
                                    try {
                                        $stmt = $conn->prepare("SELECT * FROM messages where is_marked = 1");
                                        $stmt->execute();
                                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        $num_of_messages = count($result);
                                    } catch (PDOException $e) {
                                        $error_msg[] = "Eroare: " . $e->getMessage();
                                    } catch (Exception $e) {
                                        $error_msg[] = "Eroare: " . $e->getMessage();
                                    }
                                    ?>
                                    <div class="flex">
                                        <div class="small-widget">
                                            <i class="bx bx-envelope"></i>
                                        </div>
                                        <h3><?php echo $num_of_messages; ?> Mesaje</h3>
                                    </div>
                                </div>
                            </a>
                        </section>

                        <!-- SHOW MESSAGES CARD SECTION -->
                        <section class="show-card">
                            <div class="box-container">
                                <?php
                                try {
                                    $query = "SELECT * FROM `messages` ORDER BY created_at DESC";
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();
                                    $fetch_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if (!empty($fetch_messages)) {
                                        foreach ($fetch_messages as $message) {
                                ?>
                                            <div class="box">
                                                <div class="flex">
                                                    <h6 class="filter"><?php echo $message['email']; ?></h6>
                                                    <h6 class="filter"><?php echo $message['number']; ?></h6>
                                                </div>

                                                <div class="flex">
                                                    <p class="message-content filter"><?php echo $message['message']; ?></p>

                                                    <div>
                                                        <form method="post" action="admin_messages.php">
                                                            <input type="hidden" name="message_id" value="<?= $message['id']; ?>">
                                                            <a href="admin_messages.php?delete=<?php echo $message['id']; ?>" class="delete" onclick="return confirm('Dorești să ștergi mesajul de la <?php echo $message['name']; ?> ?');"><i class="fas fa-trash-alt" title="Șterge"></i></a>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                <?php
                                        }
                                    } else {
                                        echo '<div class="empty"><p>nu sunt mesaje</p></div>';
                                    }
                                } catch (PDOException $e) {
                                    $error_msg[] = "Eroare: " . $e->getMessage();
                                } catch (Exception $e) {
                                    $error_msg[] = "Eroare: " . $e->getMessage();
                                }
                                ?>
                            </div>
                        </section>
                    </div>

                </div>
            </div>
            </div>
        </section>
        <!-- //END MAIN -->
    </main>
    <?php include '../components/alert.php'; ?>

    <!-- SCRIPT SECTION -->
    <script src="../js/script.js"></script>
    <script src="../js/searchCard.js"></script>
    <script>
        // Use PHP's json_encode function to convert PHP arrays to JavaScript arrays
        const xValues = <?php echo json_encode($xValues); ?>;
        const yValues = <?php echo json_encode($yValues); ?>;

        new Chart("myChart", {
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgba(0,0,255,1.0)",
                    borderColor: "rgba(0,0,255,0.1)",
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
                            min: Math.min(...yValues), // Set the minimum value based on the minimum of yValues
                            max: Math.max(...yValues) // Set the maximum value based on the maximum of yValues
                        }
                    }],
                }
            }
        });
    </script>
</body>

</html>