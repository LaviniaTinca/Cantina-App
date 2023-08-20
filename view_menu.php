<?php
include 'php/connection.php';
include 'php/session.php';


//adding products in the cart
if (isset($_POST['add_to_cart'])) {
    try {
        $product_id = $_POST['product_id'];

        $qty = $_POST['qty'];
        $qty = htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');

        $product = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
        $product->execute([$product_id]);
        $fetch_product = $product->fetch(PDO::FETCH_ASSOC);

        // Check if the product is already in the cart for the user
        $existingCartItem = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ? LIMIT 1");
        $existingCartItem->execute([$user_id, $product_id]);
        $fetch_existingCartItem = $existingCartItem->fetch(PDO::FETCH_ASSOC);

        if ($fetch_existingCartItem) {
            // Update the quantity of the existing cart item
            $newQty = $fetch_existingCartItem['qty'] + $qty;

            $update_cart = $conn->prepare("UPDATE `cart` SET qty = ? WHERE id = ?");
            $update_cart->execute([$newQty, $fetch_existingCartItem['id']]);

            if ($update_cart) {
                $success_msg[] = 'Cantitatea a fost modificată!';
            } else {
                $error_msg[] = 'Nu s-a putut efectuat modificarea cantității!';
            }
        } else {
            // Insert a new cart item
            $id = unique_id();

            $insert_cart = $conn->prepare("INSERT INTO `cart` (id, user_id, product_id, price, qty) VALUES (?, ?, ?, ?, ?)");
            $insert_cart->execute([$id, $user_id, $product_id, $fetch_product['price'], $qty]);

            if ($insert_cart) {
                $success_msg[] = 'Produsul a fost adaugat în coș';
            } else {
                $error_msg[] = 'Nu s-a putut adăuga produsul în coș';
            }
        }
    } catch (PDOException $th) {
        $error_msg = 'Eroare ' . $th->getMessage();
    } catch (Exception $th) {
        $error_msg = 'Eroare' . $th->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - products</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            <h1 style="color: var(--green)">Meniul zilei</h1>
        </div>
        <div class="title2">
            <a href="home.php">acasă </a><span>/ meniu</span>
        </div>

        <!-- SHOW PRODUCTS SECTION -->
        <div class="menu1">
            <div id="popup-container" style="display: none;">
                <img id="popup-image" src="" alt="popup image">
            </div>
            <section class="products">
                <div class="box-container">
                    <?php
                    try {
                        $query = "SELECT products.*, dmi.id AS menu_id, dmi.qty AS qty
                                                FROM daily_menu
                                                JOIN daily_menu_items AS dmi ON dmi.daily_menu_id = daily_menu.id
                                                JOIN products ON dmi.product_id = products.id
                                                WHERE daily_menu.date = CURDATE()";
                        $select_products = $conn->prepare($query);
                        $select_products->execute();
                        if ($select_products->rowCount() > 0) {
                            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                                $finished_class = ($fetch_products['qty'] <= 0) ? 'finished' : '';

                    ?>
                                <form action="view_menu.php" method="post" class="box <?php echo $finished_class; ?>">
                                    <div class="products-img-wrapper">
                                        <img src="image/<?= $fetch_products['image']; ?>" alt="product image" class="img product-image">
                                        <a href="view_item.php?pid=<?php echo $fetch_products['id']; ?>" class="far fa-eye eye-icon" title="Previzualizare"></a>
                                    </div>
                                    <br>
                                    <br>
                                    <h3 class="name"><?= $fetch_products['name']; ?></h3>
                                    <input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">
                                    <div class="flex">
                                        <p class="price"> <?= $fetch_products['price']; ?> Ron</p>
                                        <p class="price"> <?= $fetch_products['measure']; ?></p>

                                        <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
                                        <button type="submit" name="add_to_cart" class="menu-add-btn" title="Adaugă în coș"><i class="bx bx-cart"></i></button>
                                    </div>
                                    <?php
                                    if ($fetch_products['qty'] == 1) {
                                        echo '<p class="price" style="color: var(--cart)"> Ultima porție!</p>';
                                    } else {
                                        if ($fetch_products['qty'] > 0 && $fetch_products['qty'] < 6) {
                                            echo '<p class="price" style="color: var(--cart)"> Doar ' . $fetch_products['qty'] . ' porții rămase!</p>';
                                        }
                                    }
                                    ?>
                                </form>
                    <?php
                            }
                        } else {
                            echo '<p class="empty">nu au fost incă adăugate produse!</p>';
                        }
                    } catch (PDOException $th) {
                        $error_msg = 'Eroare ' . $th->getMessage();
                    } catch (Exception $th) {
                        $error_msg = 'Eroare' . $th->getMessage();
                    }
                    ?>
                </div>
            </section>
        </div>

        <!-- END MAIN -->
    </main>

    <!-- FOOTER SECTION -->
    <section id="menu">
        <?php include 'components/footer.php'; ?>
    </section>

    <?php include 'components/alert.php'; ?>

    <script src="js/script.js"></script>
    <!-- <script src="js/popup.js"></script> -->
</body>

</html>