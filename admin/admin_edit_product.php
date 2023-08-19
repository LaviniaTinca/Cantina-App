<?php
include '../php/connection.php';
include '../php/session_handler.php';

//update product
if (isset($_POST['update_product'])) {
    // if ($_FILES['update_image']['error'] == UPLOAD_ERR_INI_SIZE || $_FILES['update_image']['error'] == UPLOAD_ERR_FORM_SIZE) {
    //     $messages[] = "The uploaded file is too large.";
    //     // } elseif ($_FILES['update_image']['error'] == UPLOAD_ERR_NO_FILE) {
    //     //     $messages[] = "No file was uploaded.";
    // } elseif ($_FILES['update_image']['error'] == UPLOAD_ERR_PARTIAL) {
    //     $messages[] = "The uploaded file was only partially uploaded.";
    // } elseif ($_FILES['update_image']['error'] == UPLOAD_ERR_NO_TMP_DIR || $_FILES['update_image']['error'] == UPLOAD_ERR_CANT_WRITE || $_FILES['update_image']['error'] == UPLOAD_ERR_EXTENSION) {
    //     $messages[] = "An error occurred while uploading the file. Please try again later.";
    // } elseif (!in_array($_FILES['update_image']['type'], ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
    //     $messages[] = "The uploaded file must be a JPEG, PNG, or GIF image.";
    // } else {
    $update_image_size = $_FILES['update_image']['size'];
    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_folder = '../image/' . $update_image;
    // }
    $update_id = htmlspecialchars($_POST['update_id'], ENT_QUOTES, 'UTF-8');
    $update_name = htmlspecialchars($_POST['update_name'], ENT_QUOTES, 'UTF-8');
    $update_detail = htmlspecialchars($_POST['update_detail'], ENT_QUOTES, 'UTF-8');
    $update_price = htmlspecialchars($_POST['update_price'], ENT_QUOTES, 'UTF-8');

    if (empty($messages)) {
        try {
            $conn->beginTransaction();
            $query = "SELECT image FROM `products` WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$update_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && !empty($update_image)) {
                unlink('image/' . $result['image']);
                move_uploaded_file($update_image_tmp_name, $update_image_folder);

                $query = "UPDATE `products` SET `name`=?, `price`=?, `product_detail`=?, `image`=? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$update_name, $update_price, $update_detail, $update_image, $update_id]);
            } else {
                $query = "UPDATE `products` SET `name`=?, `price`=?, `product_detail`=? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$update_name, $update_price, $update_detail, $update_id]);
            }

            $conn->commit();
            $success_msg[] = "Produsul a fost adaugat!";

            header('location: admin_products.php');
        } catch (PDOException $e) {
            $conn->rollback();
            $error_msg[] = "Eroare la actualizare: " . $e->getMessage();
        }
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

                            <!-- EDIT PRODUCT SECTION -->
                            <section class="update-container">
                                <h3 class="update">ACTUALIZEAZĂ PRODUSUL</h3>
                                <?php
                                try {
                                    if (isset($_GET['edit'])) {
                                        $edit_id = $_GET['edit'];
                                        $query = "SELECT * FROM `products` WHERE id = '$edit_id'";
                                        $stmt = $conn->prepare($query);
                                        $stmt->execute();
                                        $num_rows = $stmt->rowCount();

                                        if ($num_rows > 0) {
                                            while ($fetch_edit = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                                <div class="form-container">
                                                    <form class="Form" action="" method="post" enctype="multipart/form-data">
                                                        <div class="form-img">
                                                            <img src="../image/<?php echo $fetch_edit['image']; ?>" alt="product to be edited">
                                                        </div>
                                                        <input type="hidden" name="update_id" value="<?php echo $fetch_edit['id']; ?>"><br>
                                                        <label for="name">Nume:</label>
                                                        <input type="text" name="update_name" value="<?php echo $fetch_edit['name']; ?>" required>
                                                        <label for="product_detail">Descriere produs:</label>
                                                        <textarea name="update_detail" required><?php echo $fetch_edit['product_detail']; ?></textarea>
                                                        <label for="product-category">Categoria:</label>
                                                        <select name="update_category" id="product-category">
                                                            <option value="soup" <?php if ($fetch_edit['category'] === 'soup') echo 'selected'; ?>>Supă/Ciorbă</option>
                                                            <option value="principal" <?php if ($fetch_edit['category'] === 'principal') echo 'selected'; ?>>Garnitură/Fel principal</option>
                                                            <option value="desert" <?php if ($fetch_edit['category'] === 'desert') echo 'selected'; ?>>Desert</option>
                                                            <option value="beverages" <?php if ($fetch_edit['category'] === 'beverages') echo 'selected'; ?>>Băuturi</option>
                                                            <option value="altele" <?php if ($fetch_edit['category'] === 'altele') echo 'selected'; ?>>Altele</option>
                                                        </select>
                                                        </select>
                                                        <div class="flex" style="align-items: center; margin: .5rem ; width: 68%">
                                                            <div class="flex" style="margin: .5rem;">
                                                                <label style="margin-right: .5rem" for="measure">Măsură: </label>
                                                                <input type="text" name="update_measure" placeholder="unitatea de masura" id="measure" value="<?php echo $fetch_edit['measure']; ?>" required>
                                                            </div>
                                                            <div class="flex" style="margin: .5rem;">
                                                                <label style="margin-right: .5rem" for="price">Preț: </label>
                                                                <input type="number" placeholder="pret" name="update_price" min="0" value="<?php echo $fetch_edit['price']; ?>" required>
                                                            </div>
                                                        </div>
                                                        <input type="file" style="align-items: center; width: 68%" name="update_image" accept="image/jpg, image/jpeg, image/png, image/webp">
                                                        <input class="form-button" type="submit" name="update_product" value="ACTUALIZEAZĂ" onclick="closeForm()">
                                                    </form>
                                                </div>
                                <?php
                                            }
                                        } else {
                                            //no results found
                                        }
                                    }
                                } catch (PDOException $e) {
                                    echo "Eroare: " . $e->getMessage();
                                } catch (Exception $e) {
                                    echo "Eroare: " . $e->getMessage();
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

</body>

</html>