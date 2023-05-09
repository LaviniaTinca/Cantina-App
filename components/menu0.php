<div class="menu0">
    <h1 class="main-page-title" style="color: var(--olive)">Daily Menu</h1>

    <section class="show-products">
        <div id="popup-container" style="display: none;">
            <img id="popup-image" src="" alt="popup image">
        </div>
        <div class="box-container">
            <?php
            $query = "SELECT * FROM `products` LIMIT 3";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($products)) {
                foreach ($products as $product) {
            ?>
                    <div class="box">
                        <img src="image/<?php echo $product['image']; ?>" alt="product image" class="product-image">
                        <p>price : <?php echo $product['price']; ?> lei</p>
                        <h4><?php echo $product['name']; ?></h4>
                        <details><?php echo $product['product_detail']; ?></details>
                    </div>
            <?php
                }
            } else {
                echo '
                            <div class="empty">
                                <p>no products added yet</p>
                            </div>
                        ';
            }
            ?>
        </div>

        <button id="read-more-btn" class="menu0-btn">Read MORE.....</button>
    </section>
</div>





<script>
    //jquery for the popup
    $(document).ready(function() {
        $('.product-image').on('click', function() {
            var src = $(this).attr('src');
            $('#popup-image').attr('src', src);
            $('#popup-container').fadeIn();
        });

        $('#popup-container').on('click', function() {
            $(this).fadeOut();
        });
    });

    //js for read-more button
    document.getElementById("read-more-btn").addEventListener("click", function() {
        window.location.href = "view_products.php";
    });
</script>