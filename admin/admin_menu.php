<?php
include '../config/connection.php';
include '../config/session_admin.php';

//update product qty in menu 
if (isset($_POST['update_menu'])) {
    $menu_id = htmlspecialchars($_POST['menu_id'], ENT_QUOTES, 'UTF-8');
    $qty = htmlspecialchars($_POST['qty'], ENT_QUOTES, 'UTF-8');
    try {
        $stmt = $conn->prepare("SELECT id FROM daily_menu WHERE `date` = CURDATE()");
        $stmt->execute();
        $daily_menu_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
        $update_qty = $conn->prepare("UPDATE `daily_menu_items` SET qty = ? WHERE id = ? and daily_menu_id = ?");
        $update_qty->execute([$qty, $menu_id, $daily_menu_id]);

        $success_msg[] = 'cantitatea produsului din meniu a fost modificata!';
    } catch (PDOException $e) {
        $error_msg[] = "Eroare la actualizarea produsului: " . $e->getMessage();
    } catch (Exception $e) {
        $conn->rollback();
        $error_msg[] = "Eroare la actualizarea produsului: " . $e->getMessage();
    }
}

//delete menu item
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `daily_menu_items` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        $success_msg[] = 'produsul a fost sters din meniu';
    } catch (PDOException $e) {
        $error_msg[] = "Eroare la ștergerea produsului: " . $e->getMessage();
    } catch (Exception $e) {
        $error_msg[] = "Eroare la ștergerea produsului: " . $e->getMessage();
    }
}

//empty menu
if (isset($_POST['empty_menu'])) {
    try {
        $query = "DELETE FROM `daily_menu_items` WHERE daily_menu_id = CURDATE()";
        $stmt = $conn->prepare($query);
        $stmt->execute();

        $success_msg[] = 'datele din meniu au fost sterse';
        header('location: admin_menu.php');
    } catch (PDOException $e) {
        $error_msg[] = "Eroare: " . $e->getMessage();
    } catch (Exception $e) {
        $error_msg[] = "Eroare: " . $e->getMessage();
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
    <link rel="stylesheet" href="../css/dailymenu_boxes.css">
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

                            <!-- Banner Section - with image card -->
                            <section>
                                <div class="flex">
                                    <a href="admin_products.php">
                                        <h4 class="cart-btn"> + Adaugă produs nou / Actualizează produs</h4>
                                    </a>
                                </div>

                                <div class="category-box">
                                    <div class="filter-box" data-category="soup" title="Supe/Ciorbe">
                                    </div>
                                    <div class="filter-box" data-category="garniture" title="Garnituri">
                                    </div>
                                    <div class="filter-box" data-category="principal" title="Fel principal">
                                    </div>
                                    <div class="filter-box" data-category="salad" title="Salate">
                                    </div>
                                    <div class="filter-box" data-category="desert" title="Dulciuri">
                                    </div>
                                    <div class="filter-box" data-category="beverage" title="Bauturi/Ceai/Cafea">
                                    </div>
                                </div>
                            </section>

                            <!-- SHOW TABLE PRODUCTS WITH SORT AND FILTER-->
                            <section>
                                <div id="popup-container" style="display: none;">
                                    <img id="popup-image" src="" alt="popup image">
                                </div>
                                <div class="product-table-container">
                                    <table id="product-table" class="product-table">
                                        <thead>
                                            <tr>
                                                <th class="sortable" data-column="image">Imagine</th>
                                                <th class="sortable" data-column="name">Nume</th>
                                                <th class="sortable" data-column="price">Preț</th>
                                                <th class="sortable" data-column="category">Categorie</th>
                                                <th class="sortable" data-column="qty">Nr. de porții</th>
                                                <th>Cantitate</th>
                                                <th>Acțiuni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            try {
                                                $query = "SELECT products.*, dmi.id AS menu_id, dmi.qty AS qty
                                                FROM daily_menu
                                                JOIN daily_menu_items AS dmi ON dmi.daily_menu_id = daily_menu.id
                                                JOIN products ON dmi.product_id = products.id
                                                WHERE daily_menu.date = CURDATE()";

                                                $stmt = $conn->prepare($query);
                                                $stmt->execute();
                                                $fetch_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                if (count($fetch_products) > 0) {
                                                    foreach ($fetch_products as $product) {
                                            ?>
                                                        <tr>
                                                            <td> <img src="../public/image/<?php echo $product['image']; ?>" alt="product image" class="product-image"></td>
                                                            <!-- <td><?php echo $product['name']; ?></td> -->
                                                            <td title="<?php echo $product['name']; ?>"><a href="admin_view_product.php?pid=<?php echo $product['id']; ?>"><?php echo substr($product['name'], 0, 25) . '...'; ?></a></td>
                                                            <td><?php echo $product['price']; ?></td>
                                                            <td><?php echo $product['category']; ?></td>
                                                            <td><?php echo $product['qty']; ?></td>
                                                            <td><?php echo $product['measure']; ?></td>
                                                            <td>
                                                                <form action="admin_menu.php" method="post">
                                                                    <input type="hidden" name="menu_id" value="<?php echo $product['menu_id']; ?>">
                                                                    <input type="number" name="qty" class="qty edit" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="<?= $product['qty']; ?>">
                                                                    <button type="submit" name="update_menu" title="Update quantity">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <a href="admin_menu.php?delete=<?php echo $product['menu_id']; ?>" class="delete" onclick="return confirm('You really want to delete <?php echo $product['name']; ?> from the menu?');"><i class="fas fa-trash-alt" title="Delete"></i></a>
                                                                </form>
                                                            </td>
                                                        </tr>
                                            <?php

                                                    }
                                                } else {
                                                    echo '
                                                <tr>
                                                    <td colspan="5" rowspan="2" class="empty">
                                                        <p>Nu au fost adăugate produse în meniul zilei!</p>
                                                    </td>
                                                </tr>
                                            ';
                                                }
                                            } catch (PDOException $th) {
                                                $error_message[] = 'Eroare ' . $th->getMessage();
                                            } catch (Exception $th) {
                                                $error_message[] = 'Eroare ' . $th->getMessage();
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
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