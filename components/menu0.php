<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu summary</title>
    <style>
        .menu0-btn {
            width: 20%;
            border: none;
            padding: 15px 20px;
            background-color: var(--olive);
            color: white;
            cursor: pointer;
            margin: 20px;
            display: block;
            margin: 0 auto;
        }

        .menu0 .box-container {
            padding: 1% 5%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }

        .menu0 .box {
            background: #fff;
            border: 0px solid var(--olive);
            /* border: 2px solid #045d4c; */
            box-shadow: 2px 2px 5px rgb(107, 142, 32, 0.6);
            padding: 1rem;
            text-align: center;
            border-radius: 5px;
            margin: 1rem;
            max-width: 200px;
            flex-basis: calc((100% / 4) - 1rem);
            /* Adjust to the number of boxes */
        }

        #popup-container {
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: 9999;
            background-color: rgba(0, 0, 0, 0.8);
        }

        #popup-image {
            max-width: 80%;
            max-height: 80%;
        }


        /* ----din admin dashboard preluat---------e mai mult decat trebuie aici */
        .show-products {
            position: relative;
            margin-bottom: 30px;
        }

        .show-products::before {
            top: -100px;
        }

        .show-products .box-container {
            grid-template-columns: repeat(auto-fit, minmax(20rem, 1fr));
        }

        .box-container .box img {
            width: 100%;
            margin-bottom: 1rem;
        }

        .edit,
        .delete {
            color: #000;
            padding: .5rem 1.5rem;
            text-transform: capitalize;
            line-height: 2;
        }
    </style>
</head>

<body>
    <br><br><br><br><br>
    <h1 class="main-page-title" style="color: var(--olive)">Daily Menu</h1>
    <div id="menu-summary" class="menu0">
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

        document.getElementById("read-more-btn").addEventListener("click", function() {
            window.location.href = "view_products.php";
        });
    </script>
    <script>
        // // PENTRU TABELUL IN PLUS PE CARE INCERC SA APLIC SORTARE
        // $(document).ready(function() {

        // // Search by keyword
        // $('#search-input').on('keyup', function() {
        // var value = $(this).val().toLowerCase();
        // $('#products-table tbody tr').filter(function() {
        //     $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        // });
        // });

        // // Sorting by columns
        // $('.sortable').click(function() {
        // var column = $(this).data('column');
        // var order = $(this).hasClass('asc') ? 'desc' : 'asc';
        // $('.sortable').removeClass('asc').removeClass('desc');
        // $(this).addClass(order);
        // var rows = $('#products-table tbody tr').toArray();
        // rows.sort(compare(column, order));
        // $('#products-table tbody').empty().append(rows);
        // });

        // function compare(column, order) {
        // return function(a, b) {
        //     var aValue = $(a).find('td').eq(getColumnIndex(column)).text();
        //     var bValue = $(b).find('td').eq(getColumnIndex(column)).text();
        //     var result = aValue.localeCompare(bValue, undefined, {
        //     numeric: true,
        //     sensitivity: 'base'
        //     });
        //     return order === 'asc' ? result : -result;
        // }
        // }

        // function getColumnIndex(column) {
        // return $('.sortable').index($('[data-column="' + column + '"]'));
        // }

        // });
    </script>

</body>

</html>