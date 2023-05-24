<?php
include 'php/connection.php';
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
}

if ($_SESSION['user_type'] === 'user') {
    header('location:home.php');
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: login.php");
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
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>
    <!-- HEADER SECTION -->
    <section>
        <?php include('components/admin/header.php'); ?>

    </section>

    <main class="main" style="margin-top: 50px">

        <!-- SIDEBAR AND PANEL-CONTAINER -->
        <section>
            <div class="admin-container">
                <?php include('components/admin/sidebar.php'); ?>

                <div class="panel-container">
                    <div class="banner" style=" height: 100px; background: rgba(255, 255, 255, 0.9) url('https://thumbs.dreamstime.com/z/cooking-banner-background-spices-vegetables-top-view-cooking-banner-background-spices-vegetables-top-view-free-168096882.jpg') ; background-size:cover">
                        <h1 style="color:var(--green)">dashboard</h1>
                    </div>
                    <div class="title2">
                        <a href="admin.php">admin </a><span>/ dashboard</span>
                    </div>
                    <div class=" content">
                        <!-- WIDGETS -->


                        <!-- SEARCH USER -->
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


                    </div>
                </div>
            </div>
        </section>
        <!-- //END MAIN -->
    </main>

    <!-- SCRIPT SECTION -->

    <script src="script.js"></script>

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


</body>

</html>