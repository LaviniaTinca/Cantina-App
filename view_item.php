<?php
include 'php/connection.php';
include 'php/session.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - View Item</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- HEADER SECTION -->
    <section>
        <?php include 'components/header.php'; ?>
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
                $pid = $_GET['pid'];
                $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = '$pid'");
                $select_products->execute();
                if ($select_products->rowCount() > 0) {
                    $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)
            ?>
                    <form action="view_menu.php" method="post">
                        <img src="image/<?php echo $fetch_products['image']; ?>">
                        <div class="detail">
                            <div class="name"><?php echo $fetch_products['name']; ?></div>
                            <div class="detail">
                                <?php echo $fetch_products['product_detail']; ?>
                            </div>
                            <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">
                            <div class="button">
                                <input type="hidden" name="qty" value="1" min="0" class="quantity">
                            </div>
                            <div class="flex">
                                <p class="price"> <?= $fetch_products['price']; ?> Ron</p>
                                <p class="price"> <?= $fetch_products['measure']; ?></p>

                                <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
                                <button type="submit" name="add_to_cart" class="menu0-btn">Adaugă <i class="bx bx-cart"></i></button>
                            </div>
                        </div>
                    </form>
            <?php
                }
            }
            ?>
        </section>
        <!-- END MAIN -->
    </main>

    <!-- FOOTER SECTION -->
    <section id="menu">
        <?php include 'components/footer.php'; ?>
    </section>
    <?php include 'components/alert.php'; ?>

    <script src="js/script.js"></script>
</body>

</html>