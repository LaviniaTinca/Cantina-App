<?php
include 'php/connection.php';
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: login.php");
}

//adding products to database
if (isset($_POST['add_product'])) {
    // $product_name = mysqli_real_escape_string($conn, $_POST['name']);
    // $product_detail = mysqli_real_escape_string($conn, $_POST['detail']);
    // $product_price = mysqli_real_escape_string($conn, $_POST['price']);
    // $image = $_FILES['image']['name'];
    // $image_size = $_FILES['image']['size'];
    // $image_tmp_name = $_FILES['image']['tmp_name'];
    // $image_folder = 'image/'.$image;

    // $query1 = "SELECT name FROM `products` WHERE name = '$product_name'";
    // $select_product_name = mysqli_query($conn, $query1) or die ('query failed');
    // if (mysqli_num_rows($select_product_name)>0){
    //     $message[] ='product name already exist';
    // }else{
    //     $query2 = "INSERT INTO `products`(`name`, `price`, `product_detail`, `image`) VALUES ('$product_name', '$product_price', '$product_detail', '$image')";
    //     $insert_product = mysqli_query($conn, $query2) or die ('query failed');
    //     if ($insert_product){
    //         if ($image_size>2000000){
    //             $message[] = 'image size is too large';
    //         }else{
    //             move_uploaded_file($image_tmp_name, $image_folder);
    //             $message[] = 'product added successfully';
    //         }
    //     }
    // }

    //filter by category  this should be in the html
    // $category = "category_name"; // Replace with the name of the category you want to filter by

    // $query = "SELECT * FROM `products` WHERE `category` = '$category'";
    // $select_products = mysqli_query($conn, $query) or die ('query failed');
}
//delete products from database
//delete user without image in the table
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "SELECT image FROM `products` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            unlink('image/' . $result['image']);

            $query = "DELETE FROM `products` WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$delete_id]);

            $query = "DELETE FROM `wishlist` WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$delete_id]);

            $query = "DELETE FROM `cart` WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$delete_id]);
        }

        header('location: admin_user.php');
    } catch (PDOException $e) {
        echo "Error deleting product: " . $e->getMessage();
    }
}

//update product
// if (isset($_POST['update_product'])){
//     $update_id = $_POST['update_id'];
//     $update_name = $_POST['update_name'];
//     $update_detail = $_POST['update_detail'];
//     $update_price = $_POST['update_price'];
//     $update_image = $_FILES['update_image']['name'];
//     $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
//     $update_image_folder = 'image/'.$update_image;

//     $query1 = "UPDATE `products` SET `id`='$update_id', `name`='$update_name', `price`='$update_price', `product_detail`='$update_detail',
//                 `image`='$update_image' where id = '$update_id'";
//     $update_query = mysqli_query($conn, $query1) or die ('query failed');
//     if ($update_query){
//         move_uploaded_file($update_image_tmp_name, $update_image_folder);
//         header('location: admin_product.php');
//     }
// }

//update product
//update product
if (isset($_POST['update_product'])) {
    $update_id = $_POST['update_id'];
    $update_name = $_POST['update_name'];
    $update_detail = $_POST['update_detail'];
    $update_price = $_POST['update_price'];
    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_folder = 'image/' . $update_image;

    try {
        $conn->beginTransaction();
        $query = "SELECT image FROM `products` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$update_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && !empty($update_image)) {
            unlink('image/' . $result['image']);
            move_uploaded_file($update_image_tmp_name, $update_image_folder);

            $query = "UPDATE `products` SET `name`=?, `price`=?, `product_detail`=?, `image`=? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$update_name, $update_price, $update_detail, $update_image, $update_id]);
        } else {
            $query = "UPDATE `products` SET `name`=?, `price`=?, `product_detail`=? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$update_name, $update_price, $update_detail, $update_id]);
        }

        $conn->commit();
        header('location: admin_product.php');
    } catch (PDOException $e) {
        $conn->rollback();
        echo "Error updating product: " . $e->getMessage();
    }
}


//update product with validation, shoud add old image and transaction
if (isset($_POST['update_product2'])) {
    $update_id = $_POST['update_id'];
    $update_name = $_POST['update_name'];
    $update_detail = $_POST['update_detail'];
    $update_price = $_POST['update_price'];

    // Validate input
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $update_name)) {
        $error_message = "Product name can only contain alphanumeric characters and spaces";
        error_log($error_message);
        header('location: admin_product.php?error=' . urlencode($error_message));
        exit();
    }
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $update_price)) {
        $error_message = "Price must be a valid decimal number";
        error_log($error_message);
        header('location: admin_product.php?error=' . urlencode($error_message));
        exit();
    }

    // Validate file type
    $allowed_file_types = array('image/jpeg', 'image/png', 'image/webp');
    if (!in_array($_FILES['update_image']['type'], $allowed_file_types)) {
        $error_message = "Invalid file type. Please upload a JPEG, PNG, or WEBP image";
        error_log($error_message);
        header('location: admin_product.php?error=' . urlencode($error_message));
        exit();
    }

    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_folder = 'image/' . $update_image;

    try {
        $query = "UPDATE `products` SET `name`=?, `price`=?, `product_detail`=?, `image`=? WHERE `id`=?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$update_name, $update_price, $update_detail, $update_image, $update_id]);

        if ($stmt->rowCount() > 0) {
            move_uploaded_file($update_image_tmp_name, $update_image_folder);
            header('location: admin_product.php');
        } else {
            $error_message = "Product update failed";
            error_log($error_message);
            header('location: admin_product.php?error=' . urlencode($error_message));
            exit();
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
        error_log($error_message);
        header('location: admin_product.php?error=' . urlencode($error_message));
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/admin.css">
    <!-- <link rel="stylesheet" href="styleAdmin2.css"> -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- <style>
        .show-products .box-container {
            padding: 1% 5%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }

       .show-products .box {
            background: #fff;
            border: 2px solid #045d4c;
            box-shadow: 2px 2px 5px rgb(6, 122, 101, 0.4);
            padding: 1rem;
            text-align: center;
            border-radius: 5px;
            margin: 1rem;
            max-width: 200px;
            flex-basis: calc((100% / 4) - 1rem); /* Adjust to the number of boxes */
        }
        #search-input {
            padding: 10px;
            border: 1px solid #045d4c;
            border-radius: 5px;
            font-size: 14px;
            margin: 16px 30px;
        }
        #popup-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            }

        .popup-content {
            background-color: #fff;
            padding: 20px;
            max-width: 80%;
            max-height: 80%;
            overflow-y: auto;
            }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 10px;
            background-color: #fff;
            border: none;
            cursor: pointer;
            }

    </style> -->
    <title>Cantina - admin</title>
</head>

<body>
    <!-- HEADER SECTION -->
    <section>
        <?php include 'components/admin/header.php'; ?>
    </section>

    <div class="main" style="margin-top: 50px; ">
        <!-- SIDEBAR AND PANEL-CONTAINER  SECTION-->
        <section>
            <div class="a-container">
                <div class="admin-container">
                    <?php include('components/admin/sidebar.php'); ?>
                    <div class="panel-container">
                        <!-- <div class="banner-container"> -->
                        <div class="title2">
                            <a href="admin.php" style="color: var(--green);">admin </a><span>/ view products</span>
                            <input type="text" id="search-input" placeholder="Search by keyword..." style="width:min-content">

                        </div>
                        <!-- </div> -->

                        <div class="content">
                            <!-- //MESSAGES -->
                            <div class="detail">
                                <?php
                                if (isset($message)) {
                                    foreach ($message as $message) {
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
                            <!-- SHOW TABLE PRODUCTS WITH SORT AND ORDER-->
                            <section>
                                <div class="product-table-container">
                                    <table id="product-table" class="product-table">
                                        <thead>
                                            <tr>
                                                <th class="sortable" data-column="image">Image</th>
                                                <th class="sortable" data-column="name">Name</th>
                                                <th class="sortable" data-column="price">Price</th>
                                                <th class="sortable" data-column="product_detail">Details</th>
                                                <!-- <th>Image</th> -->
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT * FROM `products`";
                                            $stmt = $conn->prepare($query);
                                            $stmt->execute();
                                            $fetch_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            if (count($fetch_products) > 0) {
                                                foreach ($fetch_products as $product) {
                                            ?>
                                                    <tr>
                                                        <td><img src="image/<?php echo $product['image']; ?>" alt="product image"></td>
                                                        <td><?php echo $product['name']; ?></td>
                                                        <td><?php echo $product['price']; ?></td>
                                                        <td><?php echo $product['product_detail']; ?></td>
                                                        <td>
                                                            <a href="admin_product.php?edit=<?php echo $product['id']; ?>" class="edit" id="edit">edit</a>
                                                            <a href="admin_product.php?delete=<?php echo $product['id']; ?>" class="delete" onclick="return confirm('You really want to delete this user?');">delete</a>
                                                        </td>
                                                    </tr>
                                            <?php
                                                }
                                            } else {
                                                echo '
                                                        <tr>
                                                            <td colspan="5" class="empty">
                                                                <p>No products added yet</p>
                                                            </td>
                                                        </tr>
                                                    ';
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



    <!-- SHOW PRODUCT CARD SECTION -->
    <!-- <section class="show-products">
        <div class="box-container">
            <?php
            $query = "SELECT * FROM `products`";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $fetch_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($products)) {
                foreach ($fetch_products as $product) {
            ?>
                    <div class="box">
                        <img src="image/<?php echo $product['image']; ?>" alt="product image">
                        <p>price : <?php echo $product['price']; ?> lei</p>
                        <h4><?php echo $product['name']; ?></h4>
                        <details> <?php echo $product['product_detail']; ?> </details>
                        <a href="admin_product.php?edit=<?php echo $product['id']; ?>" class="edit">edit</a>
                        <a href="admin_product.php?delete=<?php echo $product['id']; ?>" class="delete" onclick="
                        return confirm('You really want to delete this product?'); ">delete</a>
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
    </section> -->
    </div>


    <hr>
    <!-- EDIT PRODUCT SECTION -->
    <!-- <section class="update-container">
        <?php
        if (isset($_GET['edit'])) {
            $edit_id = $_GET['edit'];
            $query = "SELECT * FROM `products` WHERE id = '$edit_id'";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $num_rows = $stmt->rowCount();

            if ($num_rows > 0) {
                while ($fetch_edit = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
                    <div class="form-container">
                        <form action="" method="post" enctype="multipart/form-data">
                            <img src="image/<?php echo $fetch_edit['image']; ?>" alt="product to be edited">
                            <input type="hidden" name="update_id" value="<?php echo $fetch_edit['id']; ?>"><br>
                            <label for="name">Name:</label>
                            <input type="text" name="update_name" value="<?php echo $fetch_edit['name']; ?>">
                            <label for="price">Price:</label>
                            <input type="number" name="update_price" min="0" value="<?php echo $fetch_edit['price']; ?>">
                            <label for="product_detail">Product Detail:</label>
                            <textarea name="update_detail"><?php echo $fetch_edit['product_detail']; ?></textarea>
                            <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png, image/webp">
                            <div class="button-container">
                                <input type="submit" name="update_product" value="Update" class="edit" onclick="closeForm()">
                                <button type="button" class="close-btn" onclick="closeForm()">Close</button>
                            </div>
                        </form>
                    </div>
        <?php
                }
            } else {
                //no results found
            }
        }
        ?>
    </section> -->



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


    <script>
        var addProductBtn = document.getElementById('add-product-btn');
        var addProductSection = document.querySelector('.add-products');

        addProductBtn.addEventListener('click', function() {
            if (addProductSection.style.display === 'none') {
                addProductSection.style.display = 'block';
            } else {
                addProductSection.style.display = 'none';
            }
        });

        var showProductBtn = document.getElementById('show-product-btn');
        var showProductSection = document.querySelector('.show-all-products');

        showProductBtn.addEventListener('click', function() {
            if (showProductSection.style.display === 'none') {
                showProductSection.style.display = 'block';
            } else {
                showProductSection.style.display = 'none';
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#show-product-btn").click(function() {
                $(".product-table-container").toggle();
            });
        });
    </script>
    <!-- <script>
  $(document).ready(function() {
    $("#filter-form").submit(function(e) {
      e.preventDefault();
      var formData = $(this).serialize();
      $.ajax({
        type: "POST",
        url: "filter_products.php", // Replace with the URL of your PHP script that handles the filter
        data: formData,
        success: function(data) {
          $(".product-table-container").html(data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(textStatus, errorThrown);
        }
      });
    });
  });
</script> -->
    <script>
        $(document).ready(function() {
            // Hide the reviews table by default
            $(".review-table").hide();

            // Add a click event listener to the toggle button
            $("#toggle-reviews-btn").click(function() {
                // Toggle the visibility of the reviews table
                $(".review-table").toggle();
                //     $(this).next(".review-table").toggle(); //this is to set a unique id but something is not working properly

            });
        });
    </script>
    <!-- <script>
  const closeBtn = document.querySelector('.close-btn');
  const updateContainer = document.querySelector('.update-container');

  closeBtn.addEventListener('click', () => {
    updateContainer.style.display = 'none';
  });
</script> -->
    <!-- <script>
    // select the elements
const updateContainer = document.querySelector('.update-container');
const closeButton = document.querySelector('.update-container .close-btn');

// add event listeners
if (updateContainer && closeButton) {
  closeButton.addEventListener('click', () => {
    updateContainer.classList.remove('show');
  });
  
  if (updateContainer.classList.contains('show')) {
    // if update-container is shown, push content down
    document.body.style.paddingTop = `${updateContainer.offsetHeight}px`;
  }
}
</script> -->
    <!-- <script>
    const updateContainer = document.querySelector('.update-container');
    const closeButton = document.querySelector('.close-btn');
    const updateButton = document.querySelector('.edit');
    updateButton.addEventListener('click', function() {
        updateContainer.style.display = 'none';
    });
    closeButton.addEventListener('click', function() {
        updateContainer.style.display = 'none';
    });
    updateContainer.style.display = 'flex';
</script> -->
    <!-- <script>
  document.addEventListener("click", function(event) {
    var updateContainer = document.querySelector('.update-container');
    var formContainer = document.querySelector('.form-container');
    if (event.target.closest('.update-container') === null) {
      updateContainer.style.display = 'none';
    }
  });
</script> -->
    <!-- <script>
    function closeForm() {
        document.querySelector('.update-container').style.display = 'none';
        location.reload();
    }
</script> -->
    <script>
        $(document).ready(function() {
            //for the image popup
            // Show popup when the image is clicked
            $(".form-container img").on("click", function() {
                $("#popup-image").attr("src", $(this).attr("src"));
                $("#popup-container").fadeIn();
            });

            // Close popup when the close button is clicked
            $(".close-popup-btn").on("click", function() {
                $("#popup-container").fadeOut();
            });

            // Close popup when the update button is clicked
            $(".edit").on("click", function() {
                $("#popup-container").fadeOut();
            });
        });

        //to show the edit popup
        $(document).ready(function() {
            $('.edit-btn').click(function(e) {
                e.preventDefault(); // prevent form submission
                $('#edit-form-container').show(); // display the popup
            });
        });

        //Add jQuery code to handle the close button click event and hide the popup
        $(document).ready(function() {
            $('.edit-btn').click(function(e) {
                e.preventDefault(); // prevent form submission
                $('#edit-form-container').show(); // display the popup
            });

            $('#close-btn').click(function() {
                $('#edit-form-container').hide(); // hide the popup
            });
        });


        ///fade in and out, maybe duplicate see above
        $(document).ready(function() {
            $('#edit-btn').click(function() {
                $('#popup-container').fadeIn();
            });

            $('.close-btn').click(function() {
                $('#popup-container').fadeOut();
            });
        });
    </script>

    <!-- for sorting and searching -->
    <!-- <script>
        $(document).ready(function() {

            // Search functionality
            $("#searchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("table.product-table tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Sorting functionality
            var sortDirection = 1; // 1 = Ascending, -1 = Descending
            $(".sortable").on("click", function() {
                var column = $(this).data("column");
                sortDirection *= -1; // Flip the direction
                var rows = $("table.product-table tbody tr").get();
                rows.sort(function(a, b) {
                    var aText = $(a).children("td").eq(column).text().toUpperCase();
                    var bText = $(b).children("td").eq(column).text().toUpperCase();
                    if (aText < bText) {
                        return -1 * sortDirection;
                    }
                    if (aText > bText) {
                        return 1 * sortDirection;
                    }
                    return 0;
                });
                $.each(rows, function(index, row) {
                    $("table.product-table tbody").append(row);
                });
            });

        });
    </script> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- <script>
    // PENTRU TABELUL IN PLUS PE CARE INCERC SA APLIC SORTARE
    $(document).ready(function() {

    // Search by keyword
    $('#search-input').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('#product-table tbody tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
    });

    // Sorting by columns
    $('.sortable').click(function() {
    var column = $(this).data('column');
    var order = $(this).hasClass('asc') ? 'desc' : 'asc';
    $('.sortable').removeClass('asc').removeClass('desc');
    $(this).addClass(order);
    var rows = $('#product-table tbody tr').toArray();
    rows.sort(compare(column, order));
    $('#product-table tbody').empty().append(rows);
    });

    function compare(column, order) {
    return function(a, b) {
        var aValue = $(a).find('td').eq(getColumnIndex(column)).text();
        var bValue = $(b).find('td').eq(getColumnIndex(column)).text();
        var result = aValue.localeCompare(bValue, undefined, {
        numeric: true,
        sensitivity: 'base'
        });
        return order === 'asc' ? result : -result;
    }
    }

    function getColumnIndex(column) {
    return $('.sortable').index($('[data-column="' + column + '"]'));
    }

    });

</script> -->
    <script src="script.js"></script>


</body>

</html>