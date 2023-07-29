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

//delete message
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `messages` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        $success_msg[] = "Mesajul a fost șters!";
        // header('location: admin_view_products.php');
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// update_mark.php
// Include your database connection code here if not already included
// ...

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Get the message id and is_mark value from the AJAX request
    $messageId = $data['messageId'];
    $isMarked = $data['isMarked'];

    // Update the is_mark value in the database
    try {
        $stmt = $conn->prepare("UPDATE messages SET is_marked = ? WHERE id = ?");
        $stmt->execute([$isMarked, $messageId]);

        // Send a response back to the JavaScript to indicate success
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        // Handle the exception if needed
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}


// Assuming you have already established a database connection, you can fetch the data like this:
try {
    // Assuming your table structure has a `created_at` field for the date
    $stmt = $conn->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS record_count FROM messages GROUP BY month");
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
    <!-- <style>
        .show-messages {
            margin: 20px;
        }

        .box-container {
            display: grid;
            /* grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); */
            grid-template-columns: repeat(2, minmax(250px, 1fr));

            gap: 20px;
        }

        .box {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 100%;
        }

        .box img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .box h4 {
            margin: 0;
            font-size: 18px;
        }

        .details {
            margin-top: 10px;
        }

        .details p {
            margin: 0;
            font-size: 14px;
        }
    </style> -->
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
                                        die("Query failed: " . $e->getMessage());
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
                                $query = "SELECT * FROM `messages` ORDER BY created_at DESC";
                                $stmt = $conn->prepare($query);
                                $stmt->execute();
                                $fetch_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if (!empty($fetch_messages)) {
                                    foreach ($fetch_messages as $message) {
                                ?>
                                        <div class="box">
                                            <?php
                                            // Check if email already exists

                                            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                                            $stmt->execute([$message['email']]);
                                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                            ?>
                                            <div class="flex">
                                                <h6 class="filter"><img src=" ../image/<?php echo $user['image']; ?>" alt="user"><?php echo $message['name']; ?></h6>
                                                <h6 class="filter"><?php echo $message['email']; ?></h6>
                                                <h6 class="filter"><?php echo $message['number']; ?></h6>
                                            </div>

                                            <div class="flex">
                                                <p class="message-content filter"><?php echo $message['message']; ?></p>

                                                <div>
                                                    <form method="post" action="admin_messages.php">
                                                        <!-- <input type="checkbox" name="mark_message[]" value="<?php echo $message['id']; ?>" <?php echo $message['is_marked'] ? 'checked' : ''; ?>>

                                                        <a href="admin_messages.php?edit=<?php echo $message['id']; ?>" class="edit"><i class="fas fa-edit " title="Raspunde"></i></a> -->
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


    <!-- Add this JavaScript code inside a <script> tag or an external .js file -->
    <script>
        // Add an event listener to the checkboxes
        const checkboxes = document.querySelectorAll('input[name="mark_message[]"]');
        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('click', (event) => {
                const messageId = event.target.value;
                const isMarked = event.target.checked ? 1 : 0;

                // Send an AJAX request to update the is_mark value in the database
                // You can use fetch or XMLHttpRequest to make the AJAX request

                // For example, using fetch:
                fetch('admin_messages.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            messageId,
                            isMarked
                        }),
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        // Handle the response if needed
                        console.log(data);
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });
            });
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>