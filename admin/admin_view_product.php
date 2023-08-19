<?php
include '../php/connection.php';
include '../php/session_handler.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - View Item</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">

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

                            <!-- VIEW MENU ITEM SECTION -->
                            <section>
                                <?php
                                try {
                                    if (isset($_GET['pid'])) {

                                        $pid = $_GET['pid'];
                                        $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
                                        $select_products->execute([$pid]);
                                        if ($select_products->rowCount() > 0) {
                                            $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)
                                ?>

                                            <div class="banner" style="height:50px; ">
                                                <h1 style="color: var(--green)">detalii produs - <?php echo $fetch_products['name'] ?></h1>
                                            </div>
                                            <div class="title2">
                                                <a href="admin_products.php">produse </a><span>/ detalii produs cu seria: <?php echo $_GET['pid'] ?></span>
                                            </div>
                                            <div class="view_page">
                                                <form action="admin_products.php" method="post">
                                                    <img src="../image/<?php echo $fetch_products['image']; ?>">
                                                    <div class="detail">
                                                        <div class="name"><?php echo $fetch_products['name']; ?></div>
                                                        <div class="detail">
                                                            <?php echo $fetch_products['product_detail']; ?>
                                                        </div>
                                                        <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">

                                                        <div class="flex">
                                                        </div>
                                                        <hr class="dotted-line">
                                                        <p>Pret: <?= $fetch_products['price']; ?> Ron</p>
                                                        <p>Unitatea de măsură: <?= $fetch_products['measure']; ?></p>
                                                        <p> Data adăugării: <?= $fetch_products['created_at']; ?></p>

                                                        <div class="form-container">
                                                            <br>
                                                            <form method="post" action="admin_products.php">
                                                                <input type="hidden" name="user_id" value="<?= $fetch_products['id']; ?>">
                                                                <a href="admin_edit_product.php?edit=<?php echo $fetch_products['id']; ?>" class="edit" id="edit"><i class=" fas fa-edit" title="Editează"></i></a>
                                                                <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="delete" onclick="return confirm('Dorești să ștergi produsul <?php echo $fetch_products['name']; ?> ?');"><i class="fas fa-trash-alt" title="Șterge"></i></a>
                                                            </form>
                                                        </div>


                                                    </div>
                                                </form>
                                            </div>

                                <?php
                                        }
                                    }
                                } catch (PDOException $e) {
                                    $error_msg[] = "Eroare: " . $e->getMessage();
                                } catch (Exception $e) {
                                    $error_msg[] = "Eroare: " . $e->getMessage();
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
</body>

</html>