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

// Handle contact us save announcement
if (isset($_POST['save-announcement'])) {
    $id = unique_id();
    $description = $_POST['description'];
    $category = $_POST['category'];

    try {
        $stmt = $conn->prepare("INSERT INTO announcements(`id`, `description`, `category`) VALUES (?,?,?)");
        if ($stmt->execute([$id, $description, $category])) {
            $success_msg[] = "Anunțul a fost salvat!";
        } else {
            $error_msg[] = "Eroare la salvarea anunțului!";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

//delete announcement
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `announcements` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        $success_msg[] = "Anunțul a fost șters!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

//edit announcement
if (isset($_POST['edit-announcement'])) {
    $update_id = $_POST['announcement_id'];
    $update_category = $_POST['category'];
    $update_description = $_POST['description'];

    try {
        $conn->beginTransaction();
        $query = "UPDATE `announcements` SET `category`=?, `description`=? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$update_category, $update_description, $update_id]);

        $conn->commit();
    } catch (PDOException $e) {
        $conn->rollback();
        echo "Error updating announcement: " . $e->getMessage();
    }
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
                            <div class="widget settings-widget">
                                <div class="flex">

                                    <div class="small-widget">
                                        <i class='bx bx-cog'></i>
                                    </div>
                                    <a href="admin_announcements.php">
                                        <h3 style="color: var(--cart);"> Anunțuri</h3>
                                    </a>
                                </div>

                                <div class="widget user-widget jump" id="announcement-widget">
                                    <div class="flex">
                                        <div class="small-widget">
                                            <i class='bx bx-news'></i>
                                        </div>
                                        <h4> adaugă anunț</h4>
                                    </div>
                                </div>
                            </div>
                            <div id="success-message"></div>

                            <div class="show-card box-container">
                                <?php
                                try {
                                    $query = "SELECT * FROM `announcements` WHERE is_set = 1";
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();
                                    $record = $stmt->fetch(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Error: " . $e->getMessage();
                                }
                                if (!empty($record)) {
                                ?>
                                    <div class="box featured-card" style="border: 1px solid var(--cart)">
                                        <h6>Anunț setat din categoria: <?php echo $record['category']; ?></h6>
                                        <div class="flex">
                                            <h4 class="announcement-content filter"><?php echo $record['description']; ?></h4>
                                            <div>
                                                <form method="post" action="admin_announcements.php">
                                                    <input type="hidden" name="record_id" value="<?= $record['id']; ?>">
                                                    <input type="checkbox" class="announcement-checkbox" title="Setează anunț" data-announcement-id="<?php echo $record['id']; ?>" <?php echo ($record['is_set'] == 1) ? 'checked' : ''; ?>>
                                                    <a href="#" class="edit edit-link" data-announcement-id="<?php echo $record['id']; ?>" data-category=" <?php echo $record['category']; ?>" data-description="<?php echo $record['description']; ?>">
                                                        <i class=" fas fa-edit" title="Editează"></i>
                                                    </a>
                                                    <a href="admin_announcements.php?delete=<?php echo $record['id']; ?>" class="delete" onclick="return confirm('Dorești să ștergi anuntul <?php echo $record['description']; ?> ?');"><i class="fas fa-trash-alt" title="Șterge"></i></a>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                        </section>

                        <!--Add New Announcement Modal box -->
                        <div class="modal" id="announcement-modal">
                            <div class="modal-content">
                                <span class="close" id="close-modal">&times;</span>
                                <h2>Anunț nou</h2>
                                <form action="admin_announcements.php" method="post">
                                    <select name="category" id="announcement-category">
                                        <option value="liber">Zile libere</option>
                                        <option value="orar">Schimbare program</option>
                                        <option value="vacanta">Vacanță</option>
                                        <option value="tehnic">Tehnic</option>
                                        <option value="altele">Altele</option>
                                    </select>

                                    <textarea name="description" id="announcement-text" required></textarea>
                                    <button type="submit" name="save-announcement" id="save-announcement" class="menu0-btn">Salvează</button>
                                </form>
                            </div>
                        </div>

                        <!-- Edit Announcement Modal Box -->

                        <div class="modal" id="edit-announcement-modal">
                            <div class="modal-content">
                                <span class="close edit-close-modal" id="edit-close-modal">&times;</span>

                                <h2>Modifică anunț</h2>
                                <form action="admin_announcements.php?edit_id={announcement_id}" method="post">
                                    <input type="hidden" name="announcement_id" id="edit-announcement-id">

                                    <select name="category" id="edit-announcement-category">
                                        <option value="liber">Zile libere</option>
                                        <option value="orar">Schimbare program</option>
                                        <option value="vacanta">Vacanță</option>
                                        <option value="tehnic">Tehnic</option>
                                        <option value="altele">Altele</option>
                                    </select>

                                    <textarea name="description" id="edit-announcement-text" required></textarea>
                                    <button type="submit" name="edit-announcement" id="edit-announcement" class="menu0-btn">Modifică</button>
                                </form>
                            </div>
                        </div>

                        <!-- SHOW ANNOUNCEMENTS CARD SECTION -->
                        <section class="show-card">
                            <div class="box-container">
                                <?php
                                try {
                                    $query = "SELECT * FROM `announcements` WHERE is_set = 0 ORDER BY created_at DESC";
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();
                                    $fetch_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Error: " . $e->getMessage();
                                }

                                if (!empty($fetch_records)) {
                                    foreach ($fetch_records as $record) {
                                ?>
                                        <div class="box">
                                            <h6><?php echo $record['category']; ?></h6>
                                            <div class="flex">
                                                <h4 class="announcement-content filter"><?php echo $record['description']; ?></h4>
                                                <div>
                                                    <form method="post" action="admin_announcements.php">
                                                        <input type="hidden" name="record_id" value="<?= $record['id']; ?>">
                                                        <input type="checkbox" class="announcement-checkbox" title="Setează anunț" data-announcement-id="<?php echo $record['id']; ?>" <?php echo ($record['is_set'] == 1) ? 'checked' : ''; ?>>
                                                        <a href="#" class="edit edit-link" data-announcement-id="<?php echo $record['id']; ?>" data-category=" <?php echo $record['category']; ?>" data-description="<?php echo $record['description']; ?>">
                                                            <i class=" fas fa-edit" title="Editează"></i>
                                                        </a>
                                                        <a href="admin_announcements.php?delete=<?php echo $record['id']; ?>" class="delete" onclick="return confirm('Dorești să ștergi anuntul <?php echo $record['description']; ?> ?');"><i class="fas fa-trash-alt" title="Șterge"></i></a>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                } else {
                                    echo '<div class="empty"><p>nu sunt anunturi</p></div>';
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
        // Function to open the modal
        $("#announcement-widget").click(function() {
            $("#announcement-modal").show();
        });

        // Function to close the modal
        $("#close-modal").click(function() {
            $("#announcement-modal").hide();
        });

        // Function to save the announcement
        $("#save-announcement").click(function() {

            $("#announcement-modal").hide();
        });
    </script>

    <script>
        //script to set the old data to the edit announcement modal
        $(document).ready(function() {
            // Function to open the modal
            $(".edit-link").click(function() {
                // Get the announcement ID from the data attribute
                const announcementId = $(this).data("announcement-id");
                $("#edit-announcement-id").val(announcementId);


                // Fetch the announcement details from the server using AJAX
                $.ajax({
                    type: "POST",
                    url: "get_announcement_details.php", // Replace with the PHP script that fetches the announcement details
                    data: {
                        id: announcementId
                    },
                    dataType: "json",
                    success: function(response) {
                        // Populate the modal form with the fetched details
                        $("#edit-announcement-modal").attr("data-announcement-id", response.id).show();
                        // Set the announcement ID value in the hidden input field
                        $("#edit-announcement-category option").each(function() {
                            if ($(this).val() === response.category) {
                                $(this).prop("selected", true);
                            } else {
                                $(this).prop("selected", false);
                            }
                        });
                        $("#edit-announcement-text").val(response.description);
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                    }
                });
            });

            // Function to close the modal
            $(".edit-close-modal").click(function() {
                $(this).closest(".modal").hide();
            });

            // Function to save the announcement (submit the form)
            $("#edit-announcement").click(function() {
                $(this).closest(".modal").hide();
            });
        });
    </script>
    <script>
        //for setting announcement through checkbox
        $(document).ready(function() {
            // Function to handle checkbox click event
            $(".announcement-checkbox").click(function() {
                const announcementId = $(this).data("announcement-id");
                const isSet = $(this).prop("checked") ? 1 : 0;

                // Send the AJAX request to update the is_set value in the database
                $.ajax({
                    type: "POST",
                    url: "set_announcement.php", // Replace with the PHP script that updates the database
                    data: {
                        id: announcementId,
                        is_set: isSet
                    },
                    dataType: "json",
                    success: function(response) {
                        // Handle the response if needed
                        if (response.success) {
                            $("#success-message").text("Anunțul a fost actualizat!"); // Set the success message
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
    </script>

</body>

</html>