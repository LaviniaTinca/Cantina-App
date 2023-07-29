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

//delete subscriber
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `subscribers` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        $success_msg[] = "Abonatul a fost șters!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}



// Assuming you have already established a database connection, you can fetch the data like this:
try {
    // Assuming your table structure has a `created_at` field for the date
    $stmt = $conn->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS record_count FROM subscribers GROUP BY month");
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

                            <a href="admin_subscribers.php">
                                <div class="widget subscriber-widget">
                                    <?php
                                    try {
                                        $stmt = $conn->prepare("SELECT * FROM subscribers");
                                        $stmt->execute();
                                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        $num_of_users = count($result);
                                    } catch (PDOException $e) {
                                        die("Query failed: " . $e->getMessage());
                                    }
                                    ?>
                                    <div class="flex">
                                        <div class="small-widget">
                                            <i class="bx bx-envelope"></i>
                                        </div>
                                        <h3><?php echo $num_of_users; ?> Abonați</h3>
                                    </div>
                                </div>
                            </a>
                        </section>

                        <!-- SHOW SUBSCRIBERS CARD SECTION -->
                        <section class="show-card">
                            <div class="box-container">
                                <?php
                                $query = "SELECT * FROM `subscribers` ORDER BY created_at DESC";
                                $stmt = $conn->prepare($query);
                                $stmt->execute();
                                $fetch_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if (!empty($fetch_records)) {
                                    foreach ($fetch_records as $record) {
                                ?>
                                        <div class="box">
                                            <?php
                                            // Check if email already exists

                                            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                                            $stmt->execute([$record['email']]);
                                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                            ?>
                                            <div class="flex">
                                                <h4 class="filter"><img src="../image/<?php echo $user['image']; ?>" alt="user"><?php echo isset($user['name']) ? $user['name'] : ''; ?></h4>

                                                <h4 class="subscriber-content filter"><?php echo $record['email']; ?></h4>
                                                <div>
                                                    <form method="post" action="admin_subscribers.php">
                                                        <input type="hidden" name="record_id" value="<?= $record['id']; ?>">
                                                        <a href="admin_subscribers.php?delete=<?php echo $record['id']; ?>" class="delete" onclick="return confirm('Dorești să ștergi abonatul <?php echo $message['name']; ?> ?');"><i class="fas fa-trash-alt" title="Șterge"></i></a>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                } else {
                                    echo '<div class="empty"><p>nu sunt mesaje</p></div>';
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

    <!-- SCRIPT SECTION -->

    <script src="../script.js"></script>
    <script src="../js/searchCard.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <?php include '../components/alert.php'; ?>

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
                    backgroundColor: "rgba(147, 123, 99, 1.0)",
                    borderColor: "rgba(147, 123, 99,0.1)",
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