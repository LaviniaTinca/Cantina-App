<div class="menu0">
    <div class="banner">
        <h1 class="main-page-title" style="color: var(--olive)">Meniul zilei</h1>
    </div>

    <section class="show-products">
        <div id="popup-container" style="display: none;">
            <img id="popup-image" src="" alt="popup image">
        </div>
        <div class="box-container">
            <?php
            try {
                $query = "SELECT products.*, dmi.id AS menu_id, dmi.qty AS qty
                                                FROM daily_menu AS dm
                                                JOIN daily_menu_items AS dmi ON dmi.daily_menu_id = dm.id
                                                JOIN products ON dmi.product_id = products.id 
                                                WHERE dm.date = CURDATE() LIMIT 3";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($products)) {
                    foreach ($products as $product) {
            ?>
                        <div class="box">
                            <img src="../public/image/<?php echo $product['image']; ?>" alt="product image" class="product-image">
                            <p>Preț : <?php echo $product['price']; ?> lei</p>
                            <h4><?php echo $product['name']; ?></h4>
                        </div>
            <?php
                    }
                } else {
                    echo '
                                <div class="empty">
                                    <p>Nu sunt încă adăugate produse în meniul zilei.</p>
                                </div>
                            ';
                }
            } catch (PDOException $e) {
                $error_msg[] = "Eroare: " . $e->getMessage();
            } catch (Exception $e) {
                $error_msg[] = "Eroare: " . $e->getMessage();
            }
            ?>
        </div>

        <button id="read-more-btn" class="menu0-btn">Citește MAI MULT.....</button>
    </section>
</div>