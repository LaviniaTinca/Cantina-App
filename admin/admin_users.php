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

//add user //!!!!! good for register but without IMAGE
// Add user to the database
if (isset($_POST['add_user'])) {
    try {
        //code...

        // Validate input
        if (empty($_POST['add_name'])) {
            $messages[] = "Name is required.";
        } else {
            $add_name = htmlspecialchars($_POST['add_name'], ENT_QUOTES, 'UTF-8');
        }

        if (empty($_POST['add_email'])) {
            $messages[] = "Email is required.";
        } else {
            $add_email = filter_var($_POST['add_email'], FILTER_SANITIZE_EMAIL);
            if (!filter_var($add_email, FILTER_VALIDATE_EMAIL)) {
                $messages[] = "Invalid email format.";
            }
        }

        if (empty($_POST['add_password'])) {
            $messages[] = "Password is required.";
        } elseif (strlen($_POST['add_password']) < 6) {
            $messages[] = "Password must be at least 6 characters long.";
        } else {
            $add_password = password_hash($_POST['add_password'], PASSWORD_DEFAULT);
        }

        if (empty($_POST['add_confirm_password'])) {
            $messages[] = "Confirm password is required.";
        } elseif ($_POST['add_confirm_password'] != $_POST['add_password']) {
            $messages[] = "Passwords do not match.";
        }

        // Check if the email already exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $add_email]);
        if ($stmt->rowCount() > 0) {
            // $messages[] = "Email-ul exista deja";

            throw new Exception("Email is already taken.");
        }
    } catch (PDOException $e) {
        echo $e;
    } catch (Exception $e) {

        // Display the error message
        echo "Error at register: " . $e->getMessage();
        $messages[] = "Error at register: " . $e->getMessage();
    }

    // Insert user into database
    if (empty($messages)) {
        try {
            $conn->beginTransaction();

            $id = unique_id();
            $query = "INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id, $add_name, $add_email, $add_password]);

            $conn->commit();
            $success_msg[] = "Utilizatorul a fost adaugat!";

            header('location: admin_users.php');
        } catch (PDOException $e) {
            $conn->rollback();
            echo "Error adding user: " . $e->getMessage();
            $messages[] = "Error: " . $e->getMessage();
        }
    }
}

//delete user without image in the table
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `users` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);

        $success_msg[] = "Utilizatorul a fost șters!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        $messages[] = $e->getMessage();
    }
}
//-------------------------------------------------
//delete announcement


//edit announcement

//edit user
if (isset($_POST['update_user'])) {
    try {
        // Connect to the database
        // Begin a transaction
        $conn->beginTransaction();

        // Get the values from the form
        $id = $_POST['user_id'];
        $name = $_POST['add_name'];
        $email = $_POST['add_email'];
        $password = $_POST['add_password'];
        $confirm_password = $_POST['add_confirm_password'];
        $user_type = "";

        // validate user type field
        if (empty($_POST["user_type"])) {
            $user_type_err = "User type is required";
        } else {
            $user_type = test_input($_POST["user_type"]);
        }

        // Check if the passwords match
        if ($password != $confirm_password) {
            throw new Exception("Passwords do not match.");
        }

        // Check if the email already exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND id != :id");
        $stmt->execute(['email' => $email, 'id' => $id]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("Email is already taken.");
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update the user in the database
        $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email, password = :password, user_type=:user_type WHERE id = :id");
        $stmt->execute(['name' => $name, 'email' => $email, 'password' => $hashed_password, 'user_type' => $user_type, 'id' => $id]);

        // Commit the transaction
        $conn->commit();

        // Redirect to the user list page
        header("Location: admin_users.php");
    } catch (Exception $e) {
        // Roll back the transaction
        $conn->rollBack();

        // Display the error message
        echo "Error at update: " . $e->getMessage();
        $messages[] = "Error at update: " . $e->getMessage();
    }
}

//for chart
try {
    // Assuming your table structure has a `created_at` field for the date
    $stmt = $conn->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS record_count FROM users GROUP BY month");
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

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="../css/style.css">
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css"> -->


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script> -->


    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <style>
        .pagination {
            list-style: none;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            margin-top: 20px;
            /* Add some margin at the top */
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination li a {
            text-decoration: none;
            color: #333;
            padding: 6px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .pagination li a.active {
            background-color: var(--light-green);
            color: #333;
            border-color: var(--dark-olive);
        }

        .pagination li a:hover {
            background-color: var(--green);
        }
    </style>

</head>

<body>

    <!-- HEADER SECTION -->
    <section>
        <?php include '../components/admin/header.php'; ?>
    </section>

    <main class="main" style="margin-top: 50px; ">
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

                                <div class="widget setting-widget">
                                    <div class="flex">

                                        <div class="small-widget">
                                            <i class='bx bx-cog'></i>
                                        </div>
                                        <a href="admin_users.php">
                                            <h3 style="color: var(--cart)"> Utilizatori</h3>
                                        </a>
                                    </div>

                                    <div class="widget user-widget jump" id="user-widget">
                                        <div class="flex">
                                            <div class="small-widget">
                                                <i class='bx bx-user'></i>
                                            </div>
                                            <h4> adaugă </h4>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="myChart" style="max-width:400px"></canvas>

                            </section>

                            <!--Add New User Modal box -->
                            <section class="modal" id="user-modal">
                                <div class="modal-content">
                                    <span class="close" id="close-modal">&times;</span>
                                    <div class="form-container">
                                        <h2>Utilizator nou</h2>

                                        <form class="Form" onsubmit="return validateForm()" action="admin_users.php" method="post" enctype="multipart/form-data">
                                            <label for="add-name">Name:</label>
                                            <input type="text" name="add_name" id="add-name" required>
                                            <span id="nameError"></span>

                                            <label for="add-email">Email:</label>
                                            <input type="email" name="add_email" id="add-email" required><span id="emailError" class="error"></span>
                                            <span id="emailError"></span>

                                            <label for="add-password">Password:</label>
                                            <input type="password" name="add_password" id="add-password" required><span id="passwordError" class="error"></span>
                                            <span id="passwordError"></span>

                                            <label for="add-confirm-password">Confirm Password:</label>
                                            <input type="password" name="add_confirm_password" id="add-confirm-password" required><span id="confirmPasswordError" class="error"></span>
                                            <span id="confirmPasswordError"></span>
                                            <input class="form-button" type="submit" name="add_user" id="add_user" value="ÎNREGISTREAZĂ">
                                        </form>
                                    </div>

                                </div>
                            </section>
                            <!-- Add the search input2 field -->
                            <!-- <input type="text" id="search-input2" placeholder="Search the whole table..."> -->

                            <!-- SHOW USERS TABLE with FILTER  -->
                            <section>
                                <?php
                                $limit = 7; // Number of records per page
                                $page = isset($_GET['page']) ? $_GET['page'] : 1; // Get the current page number
                                $offset = ($page - 1) * $limit; // Calculate the offset for the SQL query
                                try {
                                    // Prepare the query with LIMIT and OFFSET clauses
                                    $stmt = $conn->prepare("SELECT * FROM users LIMIT :limit OFFSET :offset");
                                    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                ?>
                                    <div class="product-table-container">
                                        <table id="product-table" class="product-table">
                                            <thead>
                                                <tr>
                                                    <th>Nr.</th>
                                                    <th class=" sortable" data-sort="string" data-column="name">Name</th>
                                                    <th class="sortable" data-sort="string" data-column="email">Email</th>
                                                    <th class="sortable" data-sort="string" data-column="user_type">User Type</th>

                                                    <th>Actions</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php


                                                if (count($users) > 0) {
                                                    $nr = 1;
                                                    foreach ($users as $user) {
                                                ?>
                                                        <tr class="filter">
                                                            <td><?php echo $nr;
                                                                $nr++; ?></td>
                                                            <td><?php echo $user['name']; ?></td>
                                                            <td><?php echo $user['email']; ?></td>
                                                            <td><?php echo $user['user_type']; ?></td>
                                                            <td>
                                                                <form method="post" action="admin_users.php">
                                                                    <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                                                    <a href="admin_edit_user.php?edit=<?php echo $user['id']; ?>" class="edit" id="edit"><i class=" fas fa-edit" title="Editează"></i></a>
                                                                    <a href="admin_users.php?delete=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('Dorești să ștergi utilizatorul <?php echo $user['name']; ?> ?');"><i class="fas fa-trash-alt" title="Șterge"></i></a>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                        <!-- //REVIEWS TABLE -->

                                                <?php
                                                    }
                                                } else {
                                                    echo '
                                                    <tr>
                                                        <td colspan="5" class="empty">
                                                            <p>No users yet</p>
                                                        </td>
                                                    </tr>
                                                ';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php
                                } catch (PDOException $e) {
                                    // Handle any errors that may occur during the database query
                                    echo "Error: " . $e->getMessage();
                                }
                                ?>
                            </section>

                            <!-- //END TABLE  -->

                            <!-- PAGINATION -->
                            <div class="table-pagination">
                                <?php
                                // Get the total number of records in the users table
                                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
                                $stmt->execute();
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                $total_records = $result['total'];

                                // Calculate the total number of pages
                                $total_pages = ceil($total_records / $limit);

                                echo '<ul class="pagination admin-pagination">';
                                if ($page > 1) {
                                    echo '<li><a href="admin_users.php?page=' . ($page - 1) . '">Prev</a></li>';
                                }
                                for ($i = 1; $i <= $total_pages; $i++) {
                                    $active = ($i == $page) ? "active" : "";
                                    echo '<li><a href="admin_users.php?page=' . $i . '" class="' . $active . '">' . $i . '</a></li>';
                                }
                                if ($page < $total_pages) {
                                    echo '<li><a href="admin_users.php?page=' . ($page + 1) . '">Next</a></li>';
                                }
                                echo '</ul>';
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- END PAGINATION -->
                </div>
            </div>
            </div>
            </div>
        </section>
    </main>
    <?php include '../components/alert.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <script src="../script.js"></script>
    <script src="../formValidation.js"></script>
    <script src="../js/searchCard.js"></script>

    <script>
        // Function to open the modal
        $("#user-widget").click(function() {
            $("#user-modal").show();
        });

        // Function to close the modal
        $("#close-modal").click(function() {
            $("#user-modal").hide();
        });

        // Function to save the announcement
        $("#add_user").click(function() {

            $("#user-modal").hide();
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
                    backgroundColor: "rgba(194, 56, 56, 1.0)",
                    borderColor: "rgba(194, 56, 56,0.1)",
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

    <!-- <script>
        //aceasta functioneaza cu data-sort pe row, include data table cu css dar daca am paginare ia doar atat.
        $(document).ready(function() {
            // Initialize DataTable for the user-table
            const dataTable = $("#product-table").DataTable();

            // Function to handle the search with search-input2
            $("#search-input2").on("input", function() {
                const keyword = $(this).val();

                // Perform AJAX request to search_users.php
                $.ajax({
                    type: "POST",
                    url: "search_users.php",
                    data: {
                        keyword: keyword
                    },
                    dataType: "json",
                    success: function(response) {
                        // Clear the current table data
                        dataTable.clear().draw();

                        // Add the retrieved data to the DataTable
                        dataTable.rows.add(response).draw();
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                    }
                });
            });
        });
    </script> -->

    <!-- <script>
        // Initialize DataTables with server-side processing and AJAX call
        $(document).ready(function() {
            $('#user-table').DataTable({
                "serverSide": true, // Enable server-side processing
                "ajax": {
                    "url": "search_users2.php ", // Replace with the URL of your PHP script
                    "type": "GET",
                    "data": function(d) {
                        // Include additional parameters if needed
                        d.start = 0;
                        d.length = 7;
                        d.search.value = $('#search-input2').val(); // Add the search keyword to the AJAX request
                    }
                },
                "columns": [{
                        "data": "name"
                    },
                    {
                        "data": "email"
                    },
                    // Add more columns as needed
                ]
            });
        });
    </script> -->
    <!-- <script>
        //acesta functioneaza daca scot din tabel nr si actions, DAR tot de pe aceeasi pagina imi sorteaza
        $(document).ready(function() {
            $('#product-table').DataTable({
                serverSide: true, // Enable server-side processing
                ajax: {
                    url: 'search_users.php', // Replace with the URL of your PHP script
                    type: 'GET',
                    data: function(data) {
                        // Additional parameters for your PHP script
                        // For example, you can pass the search keyword entered by the user
                        data.search = {
                            value: $('#search-input').val()
                        };
                    }
                },
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'user_type'
                    },

                ]
                // Add any other DataTables options you need
            });
        });
    </script> -->

</body>

</html>