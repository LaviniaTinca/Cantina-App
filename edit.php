<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - edit</title>
</head>
<body>
        <!-- EDIT PRODUCT SECTION -->
        <section class="update-container">
        <?php 
            if (isset($_GET['edit'])) {
            $edit_id = $_GET['edit'];
            $query = "SELECT * FROM `products` WHERE id = '$edit_id'";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $fetch_edit = $stmt->fetch(PDO::FETCH_ASSOC);
            // $edit_query = mysqli_query($conn, $query) or die('query failed');
            
            if (count($fetch_products) > 0) {
                while ($fetch_edit ) {
        ?>

        <div class="form-container">
            <form action="" method="post" enctype="multipart/form-data">
                <img src="image/<?php echo $fetch_edit['image'];?>" alt="product to be edited">
                <input type="hidden" name="update_id" value="<?php echo $fetch_edit['id'];?>"><br>
                <label for="name">Name:</label>
                <input type="text" name="update_name" value="<?php echo $fetch_edit['name'];?>">
                <label for="price">Price:</label>
                <input type="number" name="update_price" min="0" value="<?php echo $fetch_edit['price'];?>">
                <label for="product_detail">Product Detail:</label>
                <textarea name="update_detail"><?php echo $fetch_edit['product_detail'];?></textarea>
                <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png, image/webp">
                <div class="button-container">
                    <input type="submit" name="update_product" value="Update" class="edit" onclick="closeForm()">
                    <button type="button" class="close-btn" onclick="closeForm()">Close</button>
                </div>
            </form>
        </div>
        <?php
                }
            }
            }
        ?>
    </section>


    <!-- EDIT VAR2 <FORM></FORM> -->
    <!-- <form action="update_product.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update_id" value="<?php echo $product['id'] ?>">
        <label for="update_name">Product Name:</label>
        <input type="text" name="update_name" id="update_name" value="<?php echo $product['name'] ?>" required>
        <br>
        <label for="update_detail">Product Detail:</label>
        <textarea name="update_detail" id="update_detail" rows="5" required><?php echo $product['product_detail'] ?></textarea>
        <br>
        <label for="update_price">Product Price:</label>
        <input type="number" name="update_price" id="update_price" value="<?php echo $product['price'] ?>" required>
        <br>
        <label for="update_image">Product Image:</label>
        <input type="file" name="update_image" id="update_image">
        <br>
        <input type="submit" name="update_product" value="Update Product">
    </form> -->
</body>
</html>