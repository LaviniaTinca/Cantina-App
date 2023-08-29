<?php
include '../config/connection.php';
include '../config/session.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - View Item</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .not-available {
            opacity: 0;
        }
    </style>
</head>

<body>
    <!-- HEADER SECTION -->
    <section>
        <?php include '../components/header.php'; ?>
    </section>

    <main class="main">
        <div class="banner">
            <h1 style="color: var(--green)">Detalii </h1>
        </div>
        <div class="title2">
            <a href="home.php">acasă </a><a href="view_menu.php">/ meniu </a><span>/ detalii produs</span>
        </div>
        <!-- VIEW MENU ITEM SECTION -->
        <section class="view_page">
            <?php
            if (isset($_GET['pid'])) {
                try {
                    $pid = $_GET['pid'];

                    $query1 = "SELECT id from `daily_menu` where `date` = CURDATE()";
                    $stmt1 = $conn->prepare($query1);
                    $stmt1->execute();
                    if ($stmt1->rowCount() > 0) {
                        $query = "SELECT products.*, dmi.id AS menu_id, dmi.qty AS qty
                                                    FROM daily_menu
                                                    JOIN daily_menu_items AS dmi ON dmi.daily_menu_id = daily_menu.id
                                                    JOIN products ON dmi.product_id = products.id
                                                    WHERE daily_menu.date = CURDATE() and products.id = ?";
                        $select_product = $conn->prepare($query);
                        $select_product->execute([$pid]);
                        if ($select_product->rowCount() > 0) {
                            $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);
                            $not_available = ($fetch_product['qty'] <= 0) ? 'not-available' : '';
                        } else {
                            $not_available = 'not-available';
                        }
                    } else {
                        $not_available = 'not-available';
                    }

                    $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = '$pid'");
                    $select_products->execute();
                    if ($select_products->rowCount() > 0) {
                        $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)
            ?>
                        <form action="view_menu.php" method="post" class="flex">
                            <img src="../public/image/<?php echo $fetch_products['image']; ?>">
                            <div class="detail">
                                <div class="name"><?php echo $fetch_products['name']; ?></div>
                                <div class="detail">
                                    <?php echo $fetch_products['product_detail']; ?>
                                </div>
                                <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">
                                <hr class="dotted-line">
                                <div>
                                    <p>Preț: <?= $fetch_products['price']; ?> Ron</p>
                                    <p>Cantitatea unei porții: <?= $fetch_products['measure']; ?></p>
                                    <br>
                                    <div class="<?php echo $not_available ?>">
                                        <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
                                        <button type="submit" name="add_to_cart" class="auth-button">Adaugă <i class="bx bx-cart"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>

            <?php
                    }
                } catch (PDOException $th) {
                    $error_msg = 'Eroare ' . $th->getMessage();
                } catch (Exception $th) {
                    $error_msg = 'Eroare' . $th->getMessage();
                }
            }
            ?>
        </section>
        <!-- END MAIN -->
    </main>

    <!-- FOOTER SECTION -->
    <section id="menu">
        <?php include '../components/footer.php'; ?>
    </section>
    <?php include '../components/alert.php'; ?>

    <script src="../js/script.js"></script>
</body>

</html>