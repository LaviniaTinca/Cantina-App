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
                $query = "SELECT products.*, menu.qty AS qty, menu.id AS menu_id
                FROM menu
                JOIN products ON menu.product_id = products.id LIMIT 3";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($products)) {
                    foreach ($products as $product) {
            ?>
                        <div class="box">
                            <img src="image/<?php echo $product['image']; ?>" alt="product image" class="product-image">
                            <p>preț : <?php echo $product['price']; ?> lei</p>
                            <h4><?php echo $product['name']; ?></h4>
                            <!-- <details><?php echo $product['product_detail']; ?></details> -->
                        </div>
            <?php
                    }
                } else {
                    echo '
                                <div class="empty">
                                    <p>Nu sunt încă adăugate produse.</p>
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

<script>
    // //jquery for the popup
    // $(document).ready(function() {
    //     $('.product-image').on('click', function() {
    //         var src = $(this).attr('src');
    //         $('#popup-image').attr('src', src);
    //         $('#popup-container').fadeIn();
    //     });

    //     $('#popup-container').on('click', function() {
    //         $(this).fadeOut();
    //     });
    // });

    //js for read-more button
    document.getElementById("read-more-btn").addEventListener("click", function() {
        window.location.href = "view_menu.php";
    });
</script>