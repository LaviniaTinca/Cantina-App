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
$message = array();


//delete user GPT
// if (isset($_GET['delete'])){
//     $delete_id = $_GET['delete'];
//     $stmt = $pdo->prepare("SELECT image FROM `users` where id = :delete_id");
//     $stmt->execute(['delete_id' => $delete_id]);
//     $fetched_delete_image = $stmt->fetch(PDO::FETCH_ASSOC);
//     unlink('image/'.$fetched_delete_image['image']);

//     $stmt = $pdo->prepare("DELETE FROM `users` where id = :delete_id");
//     $stmt->execute(['delete_id' => $delete_id]);

//     $stmt = $pdo->prepare("DELETE FROM `reviews` where user_id = :delete_id");
//     $stmt->execute(['delete_id' => $delete_id]);

//     // $stmt = $pdo->prepare("DELETE FROM `wishlist` where user_id = :delete_id");
//     // $stmt->execute(['delete_id' => $delete_id]);

//     header('location: admin_user.php');
// }


//delete user without image in the table
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "DELETE FROM `users` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);

        $query = "DELETE FROM `wishlist` WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        $message[] = 'User deleted!';

        header('location: admin_user.php');
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        $message[] = $e->getMessage();
    }
}


//delete user with image
// if (isset($_GET['delete'])){
//     $delete_id = $_GET['delete'];
//     $query = "SELECT image FROM `users` WHERE id = ?";
//     $stmt = $conn->prepare($query);
//     //$stmt->bind_param("i", $delete_id);
//     $stmt->execute([$delete_id]);
//     $result = $stmt->fetch(PDO::FETCH_ASSOC);

//     // if ($result->num_rows > 0) {
//     if ($result->rowCount() > 0) {

//         // $fetched_delete_image = $result->fetch_assoc();
//         // unlink('image/'.$fetched_delete_image['image']);

//         $query = "DELETE FROM `users` WHERE id = ?";
//         $stmt = $conn->prepare($query);
//         // $stmt->bind_param("i", $delete_id);
//         $stmt->execute([$delete_id]);

//         // $query = "DELETE FROM `reviews` WHERE user_id = ?";
//         // $stmt = $conn->prepare($query);
//         // // $stmt->bind_param("i", $delete_id);
//         // $stmt->execute([$delete_id]);

//         $query = "DELETE FROM `wishlist` WHERE user_id = ?";
//         $stmt = $conn->prepare($query);
//         // $stmt->bind_param("i", $delete_id);
//         $stmt->execute([$delete_id]);
//     }

//     header('location: admin_user.php');
// }
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $query = "SELECT image FROM `users` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$delete_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $query = "DELETE FROM `users` WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$delete_id]);

            $query = "DELETE FROM `wishlist` WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$delete_id]);
        }

        header('location: admin_user.php');
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


//update and keep old image if necessary
if (isset($_POST['update_user2'])) {
    $update_id = $_POST['update_id'];
    $update_firstName = $_POST['update_firstName'];
    $update_lastName = $_POST['update_lastName'];
    $update_email = $_POST['update_email'];
    $update_password = $_POST['update_password'];
    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];

    try {
        $conn->beginTransaction();

        if (!empty($update_image)) {
            $update_image_folder = 'image/' . $update_image;
            move_uploaded_file($update_image_tmp_name, $update_image_folder);
            $query = "UPDATE `users` SET `firstName`=?, `lastName`=?, `email`=?, `password`=?, `image`=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$update_firstName, $update_lastName, $update_email, $update_password, $update_image, $update_id]);

            // Delete the old image
            $old_image = $_POST['old_image'];
            if (!empty($old_image)) {
                unlink('image/' . $old_image);
            }
        } else {
            $query = "UPDATE `users` SET `firstName`=?, `lastName`=?, `email`=?, `password`=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$update_firstName, $update_lastName, $update_email, $update_password, $update_id]);
        }

        $conn->commit();
        header('location: admin_user.php');
    } catch (PDOException $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}

//update simple var1
if (isset($_POST['update_user2'])) {
    $update_id = $_POST['update_id'];
    $update_firstName = $_POST['update_name'];
    $update_email = $_POST['update_email'];
    $update_password = $_POST['update_password'];

    $query = "UPDATE `users` SET `name`=?, `email`=?, `password`=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$update_name, $update_email, $update_password, $update_id]);

    header('location: admin_user.php');
}

//another update for the form
if (isset($_POST['update_user'])) {
    $update_id = $_POST['edit_id'];
    $update_name = $_POST['edit_name'];
    $update_email = $_POST['edit_email'];
    $update_password = $_POST['edit_password'];

    try {
        $query = "UPDATE `users` SET `name`=?, `email`=?, `password`=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$update_name, $update_email, $update_password, $update_id]);
        header('location: admin_user.php');
    } catch (PDOException $e) {
        $message[] = "Error: " . $e->getMessage();
    }
}


// //update user
// if (isset($_POST['update_user2'])){
//     $update_id = $_POST['update_id'];
//     $update_firstName = $_POST['update_firstName'];
//     $update_lastName = $_POST['update_lastName'];
//     $update_email = $_POST['update_email'];
//     $update_password = $_POST['update_password'];
//     // $update_image = $_FILES['update_image']['name'];
//     // $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
//     // $update_image_folder = 'image/'.$update_image;

//     $query1 = "UPDATE `users` SET `id`='$update_id', `firstName`='$update_firstName', `lastName`='$update_lastName', `email`='$update_email', `password`='$update_password',
//                 `image`='$update_image' where id = '$update_id'";
//     $update_query = mysqli_query($conn, $query1) or die ('query failed');
//     // if ($update_query){
//     //     move_uploaded_file($update_image_tmp_name, $update_image_folder);
//     //     header('location: admin_user.php');
//     // }
// }

//update user
// if (isset($_POST['update_user'])){
//     $update_id = $_POST['update_id'];
//     $update_firstName = $_POST['update_firstName'];
//     $update_lastName = $_POST['update_lastName'];
//     $update_email = $_POST['update_email'];
//     $update_password = $_POST['update_password'];

//     $query1 = "UPDATE `users` SET `firstName`='$update_firstName', `lastName`='$update_lastName', `email`='$update_email', `password`='$update_password'
//                WHERE `id` = '$update_id'";
//     $update_query = mysqli_query($conn, $query1) or die ('query failed');

//     if ($update_query){
//         header('location: admin_user.php');
//     }
// }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <style>
        /* #popup-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
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
  } */

        /* Style for the popup overlay */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: none;
        }

        /* Style for the popup container */
        .popup-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.5);
        }

        /* Style for the popup title */
        .popup-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Style for the form input fields */
        .popup-input {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 3px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        /* Style for the form submit button */
        .popup-submit {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        /* Style for the close button */
        .popup-close {
            position: absolute;
            top: 5px;
            right: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #ccc;
            cursor: pointer;
        }
    </style>
    <title>Cantina - admin</title>
</head>

<body>
    <?php include 'components/admin/header.php'; ?>
    <div class="main">
        <div class="detail">
            <h1>Admin Dashboard</h1>
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

        <!-- SHOW USERS TABLE sort order  -->

        <a href="#" id="show-product-btn">
            <h1>Show all users with search and order</h1>
        </a>
        <input type="text" id="search-input" placeholder="Search by keyword...">
        <section>
            <div class="product-table-container">
                <table id="product-table" class="product-table">
                    <thead>
                        <tr>
                            <th class="sortable" data-column="user_type">User Type</th>
                            <th class="sortable" data-column="name">Name</th>
                            <th class="sortable" data-column="email">Email</th>
                            <!-- <th>Image</th> -->
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // $max_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                        // $max_cart_items->execute([$user_id]);
                        // $select_price = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
                        // $select_price->execute([$product_id]);
                        // $fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);
                        $query = "SELECT * FROM `users`";
                        $stmt = $conn->prepare($query);
                        // $stmt = $conn->prepare("SELECT * FROM `users`");
                        $stmt->execute();
                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($users) > 0) {
                            foreach ($users as $user) {
                        ?>
                                <tr>
                                    <td><?php echo $user['user_type']; ?></td>
                                    <td><?php echo $user['name']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <!-- <td><img src="image/<?php echo $user['image']; ?>" alt="product image"></td> -->

                                    <td>
                                        <a href="admin_user.php?edit=<?php echo $user['id']; ?>" onclick="showPopup()" class="edit" id="edit">edit</a>
                                        <a href="admin_user.php?delete=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('You really want to delete this user?');">delete</a>
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

        <!-- EDIT USER POPUP -->
        <!-- Add popup HTML -->
        <!-- <div id="edit-user-popup" class="popup">
    <div class="popup-content">
        <span class="close">&times;</span>
        <h2>Edit User</h2>
        <form action="" method="POST" enctype="multipart/form-data" id="edit-user-form">
        <input type="hidden" id="update_id" name="update_id" value="">
        <label for="update_firstName">First Name:</label>
        <input type="text" id="update_firstName" name="update_firstName" required>
        <label for="update_lastName">Last Name:</label>
        <input type="text" id="update_lastName" name="update_lastName" required>
        <label for="update_email">Email:</label>
        <input type="email" id="update_email" name="update_email" required>
        <label for="update_password">Password:</label>
        <input type="password" id="update_password" name="update_password" required>
        <label for="update_image">Image:</label>
        <input type="file" id="update_image" name="update_image">
        <input type="hidden" id="old_image" name="old_image" value="">
        <input type="submit" value="Update User" name="update_user2">
        </form>
    </div>
    </div> -->

        <!-- Popup Form -->
        <!-- <div class="popup-form">
    <div class="form-container">
        <span class="close-btn">&times;</span>
        <form id="edit-form" method="post" enctype="multipart/form-data">
            <h2>Edit User</h2>
            <input type="hidden" name="update_id" id="update_id">
            <div class="form-group">
                <label for="update_firstName">First Name</label>
                <input type="text" name="update_firstName" id="update_firstName" required>
            </div>
            <div class="form-group">
                <label for="update_lastName">Last Name</label>
                <input type="text" name="update_lastName" id="update_lastName" required>
            </div>
            <div class="form-group">
                <label for="update_email">Email</label>
                <input type="email" name="update_email" id="update_email" required>
            </div>
            <div class="form-group">
                <label for="update_password">Password</label>
                <input type="password" name="update_password" id="update_password" required>
            </div>
            <div class="form-group">
                <label for="update_image">Image</label>
                <input type="file" name="update_image" id="update_image">
                <span></span>
            </div>
            <div class="form-group">
                <button type="submit" name="update_user2">Update</button>
            </div>
        </form>
    </div>
</div> -->

        <div class="popup">
            <div class="popup-content">
                <span class="close">&times;</span>
                <h2>Edit User</h2>
                <form method="post" action="">
                    <input type="hidden" id="edit-id" name="edit_id">
                    <label for="edit-name">Name:</label>
                    <input type="text" id="edit-name" name="edit_name"><br>
                    <label for="edit-email">Email:</label>
                    <input type="email" id="edit-email" name="edit_email"><br>
                    <label for="edit-password">Password:</label>
                    <input type="password" id="edit-password" name="edit_password"><br>
                    <input type="submit" value="Update" name="update_user">
                </form>
            </div>
        </div>




        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            // PENTRU TABELUL IN PLUS PE CARE INCERC SA APLIC SORTARE
            // $(document).ready(function() {

            // // Search by keyword
            // $('#search-input').on('keyup', function() {
            // var value = $(this).val().toLowerCase();
            // $('#product-table tbody tr').filter(function() {
            //     $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            // });
            // });

            // // Sorting by columns
            // $('.sortable').click(function() {
            // var column = $(this).data('column');
            // var order = $(this).hasClass('asc') ? 'desc' : 'asc';
            // $('.sortable').removeClass('asc').removeClass('desc');
            // $(this).addClass(order);
            // var rows = $('#product-table tbody tr').toArray();
            // rows.sort(compare(column, order));
            // $('#product-table tbody').empty().append(rows);
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

            //pentru EDIT USER POPUP
            // const closeButton = document.querySelector('.close-btn');
            // closeButton.addEventListener('click', function() {
            // popupContainer.style.display = 'none';
            // });
        </script>

        <script>
            //     // Get the popup container and the update form
            //     const popupContainer = document.getElementById('popup-container');
            //     const editButton = document.getElementById('edit');
            //     const updateForm = document.getElementById('update-form');


            //     // Add event listener to the edit button to show the popup
            //     editButton.addEventListener('click', function() {
            //     popupContainer.style.display = 'block';
            //     });

            //     // // Add event listener to the update and close buttons
            //     // updateForm.addEventListener('submit', function(event) {
            //     //   event.preventDefault(); // prevent default form submission behavior
            //     //   popupContainer.style.display = 'none';
            //     // });

            //     const closeButton = document.querySelector('.close-button');
            //     closeButton.addEventListener('click', function() {
            //    // popupContainer.style.display = 'none';
            //     });

            //     updateForm.addEventListener('submit', function(event) {
            //     // event.preventDefault(); // prevent default form submission behavior

            //     // Handle form submission logic here
            //     // ...

            //     //popupContainer.style.display = 'none'; // hide the popup after form submission
            //     alert('Update successful.');
            // });

            // // Hide the popup and show a success message after a short delay
            // // setTimeout(function() {
            // //   popupContainer.style.display = 'none';
            // //   alert('Update successful.');
            // // }, 3000);
        </script>

        <script>
            //     function showPopup() {
            //   document.getElementById("popup-container").style.display = "block";
            // }

            // function hidePopup() {
            //   document.getElementById("popup-container").style.display = "none";
            // }

            // function submitForm() {
            //   hidePopup();
            // }
        </script>
        <script src="script.js"></script>
        <script>
            // // Get the popup
            // var editUserPopup = document.getElementById("edit-user-popup");

            // // Get the button that opens the popup
            // var editUserBtn = document.getElementById("edit");

            // // Get the <span> element that closes the popup
            // var editUserClose = document.getElementsByClassName("close")[0];

            // // When the user clicks on the button, open the popup
            // editUserBtn.onclick = function() {
            //   // Get user details to populate the form
            //   var id = this.getAttribute("data-id");
            //   var firstName = this.getAttribute("data-firstName");
            //   var lastName = this.getAttribute("data-lastName");
            //   var email = this.getAttribute("data-email");
            //   var password = this.getAttribute("data-password");
            //   var oldImage = this.getAttribute("data-image");

            //   document.getElementById("update_id").value = id;
            //   document.getElementById("update_firstName").value = firstName;
            //   document.getElementById("update_lastName").value = lastName;
            //   document.getElementById("update_email").value = email;
            //   document.getElementById("update_password").value = password;
            //   document.getElementById("old_image").value = oldImage;

            //   // Show the popup
            //   editUserPopup.style.display = "block";
            // }

            // // When the user clicks on <span> (x), close the popup
            // editUserClose.onclick = function() {
            //   editUserPopup.style.display = "none";
            // }

            // // When the user clicks anywhere outside of the popup, close it
            // window.onclick = function(event) {
            //   if (event.target == editUserPopup) {
            //     editUserPopup.style.display = "none";
            //   }
            // }




            // var2 oare? continuare nu pare a fi
            // JavaScript code for displaying the popup window
            // const editBtns = document.querySelectorAll('.edit');
            // const closeBtn = document.querySelector('.close-btn');
            // const popupForm = document.querySelector('.popup-form');

            // editBtns.forEach(editBtn => {
            //     editBtn.addEventListener('click', () => {
            //         const userId = editBtn.dataset.id;
            //         const firstName = editBtn.dataset.firstname;
            //         const lastName = editBtn.dataset.lastname;
            //         const email = editBtn.dataset.email;
            //         const password = editBtn.dataset.password;
            //         const image = editBtn.dataset.image;

            //         populateEditForm(userId, firstName, lastName, email, password, image);
            //         popupForm.classList.add('active');
            //     });
            // });

            // closeBtn.addEventListener('click', () => {
            //     popupForm.classList.remove('active');
            // });

            // function populateEditForm(id, firstName, lastName, email, password, image) {
            //     const form = document.querySelector('#edit-form');
            //     const imageInput = form.querySelector('#update_image');

            //     form.setAttribute('action', `admin_user.php?edit=${id}`);
            //     form.querySelector('#update_id').value = id;
            //     form.querySelector('#update_firstName').value = firstName;
            //     form.querySelector('#update_lastName').value = lastName;
            //     form.querySelector('#update_email').value = email;
            //     form.querySelector('#update_password').value = password;

            //     if (image) {
            //         imageInput.parentNode.querySelector('span').innerText = image;
            //     }
            // }


            // Get the popup
            var popup = document.querySelector('.popup');

            // Get the edit button
            var editBtns = document.querySelectorAll('.edit');

            // Get the close button
            var closeBtn = popup.querySelector('.close');

            // Add event listener to edit buttons
            editBtns.forEach(function(editBtn) {
                editBtn.addEventListener('click', function() {
                    // Get the user's ID
                    var id = this.dataset.id;
                    // Get the user's name
                    var name = this.dataset.name;
                    // Get the user's email
                    var email = this.dataset.email;

                    // Fill in the form fields with the user's data
                    document.getElementById('edit-id').value = id;
                    document.getElementById('edit-name').value = name;
                    document.getElementById('edit-email').value = email;

                    // Show the popup
                    popup.style.display = "block";
                });
            });

            // Add event listener to close button
            closeBtn.addEventListener('click', function() {
                // Hide the popup
                popup.style.display = "none";
            });

            // Close popup when user clicks outside of it
            window.addEventListener('click', function(event) {
                if (event.target == popup) {
                    popup.style.display = "none";
                }
            });
        </script>


</body>

</html>