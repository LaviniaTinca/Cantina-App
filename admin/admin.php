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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js">
    </script>

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
                                        die("Query failed: " . $e->getMessage());
                                    }
                                    // $select_users = mysqli_query($con, "SELECT * FROM `users` where isAdmin = 0") or die('query failed');
                                    // $num_of_users = mysqli_num_rows($select_users);
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
                                        die("Query failed: " . $e->getMessage());
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
                                        die("Query failed: " . $e->getMessage());
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
                                        die("Query failed: " . $e->getMessage());
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
                    <div class="banner" style="margin: 2rem;">
                        <canvas id="myChart" style="width:70%;max-width:600px"></canvas>
                    </div>


                    <!-- SEARCH USER -->
                    <!-- <section>
                            <label for="search-input3">Search user:</label>
                            <input type="text" id="search-input3">
                            <select id="user-select3">
                                <option value="">Select a user</option>
                                <?php
                                try {
                                    $query = "SELECT * FROM `users`";
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();
                                    $fetch_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if (count($fetch_users) > 0) {
                                        foreach ($fetch_users as $user) {
                                ?>
                                            <option value="<?php echo $user['name']; ?>"><?php echo $user['name']; ?></option>
                                <?php
                                        }
                                    } else {
                                        echo '<p>No users</p>';
                                    }
                                } catch (PDOException $e) {
                                    echo 'Error: ' . $e->getMessage();
                                }
                                ?>

                            </select>
                            <br><br><br><br>
                            <div id="filtered-options"></div>


                        </section> -->

                </div>
            </div>
            </div>
        </section>
        <!-- //END MAIN -->
    </main>

    <!-- SCRIPT SECTION -->

    <script src="../script.js"></script>

    <script>
        $(document).ready(function() {
            $('#search-input3').on('input', function() {
                var filter = $(this).val().toLowerCase();
                $('#filtered-options').empty();
                $('#user-select3 option').each(function() {
                    var text = $(this).text().toLowerCase();
                    var match = text.indexOf(filter) > -1;
                    $(this).toggle(match);
                    if (match) {
                        $('#filtered-options').append('<div>' + $(this).text() + '</div>');
                    }
                });
                if (filter === '') {
                    $('#filtered-options').empty();
                }
            });
        });
    </script>

    <script>
        var xValues = ["Italy", "France", "Spain", "USA", "Argentina"];
        var yValues = [55, 49, 44, 24, 15];
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