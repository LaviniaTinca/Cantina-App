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

// // Get the requested page from the URL
// $request_uri = $_SERVER['REQUEST_URI'];

// // Check if the requested page or resource exists
// if (!file_exists($request_uri)) {
// 	// Redirect to the custom "not found" page
// 	header('Location: not_found.php');
// 	exit;
// }


// List of pages that don't exist yet
$not_found_pages = array(
	'/wishlist.php',
	'/cart.php'
);

// Get the requested page from the URL
$request_uri = $_SERVER['REQUEST_URI'];

// Check if the requested page is in the "not found" pages array
if (in_array($request_uri, $not_found_pages)) {
	// Redirect to the custom "not found" page
	header('Location: not_found.php');
	exit;
}



//add /remove item from wishlist

// // Check if the AJAX request has been made
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
// 	// Retrieve the product ID and action from the AJAX request
// 	$productId = $_POST['productId'];
// 	$action = $_POST['action'];

// 	// Perform actions based on the action value (add/remove)
// 	if ($action === 'add') {
// 		// Add the item to the wishlist
// 		// Your code to handle adding the item to the wishlist goes here
// 		// Example: $wishlist->addItem($productId);
// 		// Replace this with your actual code to add the item to the wishlist

// 		// Return success response
// 		$response = [
// 			'success' => true,
// 			'action' => 'add'
// 		];
// 	} elseif ($action === 'remove') {
// 		// Remove the item from the wishlist
// 		// Your code to handle removing the item from the wishlist goes here
// 		// Example: $wishlist->removeItem($productId);
// 		// Replace this with your actual code to remove the item from the wishlist

// 		// Return success response
// 		$response = [
// 			'success' => true,
// 			'action' => 'remove'
// 		];
// 	} else {
// 		// Invalid action, return error response
// 		$response = [
// 			'success' => false,
// 			'message' => 'Invalid action.'
// 		];
// 	}

// 	// Return the response as JSON
// 	header('Content-Type: application/json');
// 	echo json_encode($response);
// 	exit;
// }




// $responseData = []; // Initialize with an empty array
// // Retrieve the POST data
// $requestData = json_decode(file_get_contents('php://input'), true);
// var_dump($requestData);
// // Retrieve the product ID and action from the request
// $productId = $requestData['productId'];
// $action = $requestData['action'];

// // Perform the necessary database operations based on the action
// if ($action === 'add') {
// 	// Add the product to the wishlist table in the database
// 	$id = unique_id();
// 	$select_price = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
// 	$select_price->execute([$productId]);
// 	$fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);

// 	// $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(id, user_id,product_id,price) VALUES(?,?,?,?)");
// 	// $insert_wishlist->execute([$id, $user_id, $productId, $fetch_price['price']]);
// 	$insertQuery = "INSERT INTO wishlist (id, user_id, product_id, price) VALUES (?, ?, ?, ?)";
// 	$stmt = $conn->prepare($insertQuery);
// 	$stmt->bindParam(1, $id);
// 	$stmt->bindParam(2, $user_id);
// 	$stmt->bindParam(3, $productId);
// 	$stmt->bindParam(4, $fetch_price['price']);

// 	if ($stmt->execute()) {
// 		$responseData = [
// 			'success' => true
// 		];
// 	} else {
// 		$responseData = [
// 			'success' => false
// 		];
// 	}
// 	$success_msg[] = 'product added to wishlist successfully';


// 	// $stmt = $conn->prepare("INSERT INTO wishlist (product_id) VALUES (:productId)");
// 	// $stmt->bindParam(':productId', $productId);
// 	// if ($stmt->execute()) {
// 	// 	$responseData = [
// 	// 		'success' => true
// 	// 	];
// 	// } else {
// 	// 	$responseData = [
// 	// 		'success' => false
// 	// 	];
// 	// }
// } elseif ($action === 'remove') {
// 	// Remove the product from the wishlist table in the database
// 	$stmt = $conn->prepare("DELETE FROM wishlist WHERE product_id = :productId");
// 	$stmt->bindParam(':productId', $productId);
// 	if ($stmt->execute()) {
// 		$responseData = [
// 			'success' => true
// 		];
// 	} else {
// 		$responseData = [
// 			'success' => false
// 		];
// 	}
// }

// // Send the response as JSON
// header('Content-Type: application/json');
// echo json_encode($responseData);




//adding products in wishlist
if (isset($_POST['add_to_wishlist'])) {
	$id = unique_id();
	$product_id = $_POST['product_id'];

	$varify_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ? AND product_id = ?");
	$varify_wishlist->execute([$user_id, $product_id]);

	$cart_num = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
	$cart_num->execute([$user_id, $product_id]);

	if ($varify_wishlist->rowCount() > 0) {
		$warning_msg[] = 'product already exist in your wishlist';
	} else if ($cart_num->rowCount() > 0) {
		$warning_msg[] = 'product already exist in your cart';
	} else {
		$select_price = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
		$select_price->execute([$product_id]);
		$fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);

		$insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(id, user_id,product_id,price) VALUES(?,?,?,?)");
		$insert_wishlist->execute([$id, $user_id, $product_id, $fetch_price['price']]);
		$success_msg[] = 'product added to wishlist successfully';
	}
}
//adding products in cart
if (isset($_POST['add_to_cart'])) {
	$id = unique_id();
	$product_id = $_POST['product_id'];

	$qty = $_POST['qty'];
	$qty = htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');

	//---------
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
			$success_msg[] = 'Product quantity updated in cart successfully';
		} else {
			$error_msg[] = 'Failed to update product quantity in cart';
		}
	} else {
		// Insert a new cart item
		$id = unique_id();

		$insert_cart = $conn->prepare("INSERT INTO `cart` (id, user_id, product_id, price, qty) VALUES (?, ?, ?, ?, ?)");
		$insert_cart->execute([$id, $user_id, $product_id, $fetch_product['price'], $qty]);

		if ($insert_cart) {
			$success_msg[] = 'Product added to cart successfully';
		} else {
			$error_msg[] = 'Failed to add product to cart';
		}
	}

	//---------------


	// $varify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
	// $varify_cart->execute([$user_id, $product_id]);

	// $max_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
	// $max_cart_items->execute([$user_id]);

	// if ($varify_cart->rowCount() > 0) {
	// 	//update qty
	// 	$warning_msg[] = 'product already exist in your cart';
	// } else if ($max_cart_items->rowCount() > 20) {
	// 	$warning_msg[] = 'cart is full';
	// } else {
	// 	$product = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
	// 	$product->execute([$product_id]);
	// 	$fetch_product = $product->fetch(PDO::FETCH_ASSOC);

	// 	$insert_cart = $conn->prepare("INSERT INTO `cart`(id, user_id,product_id,price,qty) VALUES(?,?,?,?,?)");
	// 	$insert_cart->execute([$id, $user_id, $product_id, $fetch_product['price'], $qty]);
	// 	$success_msg[] = 'product added to cart successfully';
	// }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Cantina - products</title>
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="css/style.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>

	<!-- HEADER SECTION -->
	<section>
		<?php include 'components/header.php'; ?>
	</section>

	<main class="main">
		<div class="banner" style=" height: 200px; ">
			<h1 style="color: var(--green)">all products in the db shop</h1>
		</div>
		<div class="title2">
			<a href="home.php">home </a><span>/ products</span>
		</div>

		<!-- SHOW PRODUCTS SECTION -->

		<div class="menu1">
			<div id="popup-container" style="display: none;">
				<img id="popup-image" src="" alt="popup image">
			</div>
			<section class="products">
				<div class="box-container">
					<?php
					$select_products = $conn->prepare("SELECT * FROM `products`");
					$select_products->execute();
					if ($select_products->rowCount() > 0) {
						while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {

					?>
							<form action="" method="post" class="box">
								<!-- <a href="not_found.php?page=checkout?get_id=<?= $fetch_products['id']; ?>" class="add-btn">add</a> -->
								<div class="products-img-wrapper">
									<img src="image/<?= $fetch_products['image']; ?>" alt="product image" class="img product-image">

								</div>
								<br>
								<div class="button">
									<button type="submit" name="add_to_cart"><i class="bx bx-cart"></i></button>
									<!-- <button type="submit" name="add_to_wishlist" id="wishlistButton"><i class="bx bx-heart"></i></button> -->
									<button type="submit" name="wishlist" class="wishlistButton" id="wishlistButton" data-product-id="<?= $fetch_products['id']; ?>"><i class="bx bx-heart"></i></button>
									<!-- <button type="submit" name="wishlist" class="wishlistButton"><i class="bx bx-heart"></i></button> -->

									<!-- <a href="not_found.php?page=details?pid=<?php echo $fetch_products['id']; ?>" class="bx bxs-show"></a> -->
									<a href="view_item.php?pid=<?php echo $fetch_products['id']; ?>" class="bx bxs-show"></a>
									<!-- <a href="view_page.php?pid=<?php echo $fetch_products['id']; ?>" class="bx bxs-show"></a> -->

								</div>
								<!-- <button type="submit" name="wishlist" class="wishlistButton" id="wishlistButton"><i class="bx bx-heart"></i></button> -->

								<br>
								<h3 class="name"><?= $fetch_products['name']; ?></h3>
								<input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">
								<div class="flex">
									<p class="price">price <?= $fetch_products['price']; ?> Ron</p>
									<input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
								</div>

							</form>
					<?php
						}
					} else {
						echo '<p class="empty">no products added yet!</p>';
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

	<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
	<script src="script.js"></script>
	<script src="popup.js"></script>

	<script>
		//asta e bun doar pt toggle culoare
		$(document).ready(function() {
			$(".wishlistButton").click(function(event) {
				event.preventDefault(); // Prevent form submission

				$(this).toggleClass("active");
			});
		});
	</script>

	<!-- <script>
		// acest script e legat de wishlist cu add/remove si schimbarea culorii - nu merge inca
		$(".wishlistButton").click(function() {
			// event.preventDefault(); // Prevent form submission
			var button = $(this);
			var productId = button.data("product-id");
			console.log(productId)

			// Determine the current action based on the button's state
			var currentAction = button.hasClass("added-to-wishlist") ? "remove" : "add";

			// Send AJAX request to the server
			$.ajax({
				url: "view_products.php",
				type: "POST",
				dataType: "json",
				contentType: "application/json",
				data: JSON.stringify({
					productId: productId,
					action: currentAction
				}),
				success: function(data) {
					// Handle the server response
					if (data.success) {
						// Item was added/removed successfully, update button appearance
						if (currentAction === "add") {
							button.addClass("added-to-wishlist");
						} else if (currentAction === "remove") {
							button.removeClass("added-to-wishlist");
						}
					} else {
						// Display an error message or handle the error case
					}
				},
				error: function(error) {
					console.error("Error:", error);
				}
			});
		});
	</script> -->
	<!-- <script>
		$(".wishlistButton").click(function() {
			var button = $(this);
			var productId = button.data("product-id");
			var currentAction = button.hasClass("added-to-wishlist") ? "remove" : "add";

			var requestData = {
				productId: productId,
				action: currentAction
			};

			// Send AJAX request to the server
			$.ajax({
				url: "view_products.php",
				type: "POST",
				dataType: "json",
				data: JSON.stringify(requestData),
				success: function(data) {
					// Handle the server response
					if (data.success) {
						// Item was added/removed successfully, update button appearance
						if (currentAction === "add") {
							button.addClass("added-to-wishlist");
						} else if (currentAction === "remove") {
							button.removeClass("added-to-wishlist");
						}
					} else {
						// Display an error message or handle the error case
					}
				},
				error: function(error) {
					console.error("Error:", error);
				}
			});
		});
	</script> -->
	<!-- <script>
		$(".wishlistButton").click(function(event) {
			event.preventDefault(); // Prevent form submission

			var button = $(this);
			var productId = button.data("product-id");
			var currentAction = button.hasClass("added-to-wishlist") ? "remove" : "add";

			// Create form data
			var formData = new FormData();
			formData.append('productId', productId);
			formData.append('action', currentAction);

			// Send AJAX request to the server
			$.ajax({
				url: "view_products.php",
				type: "POST",
				data: formData,
				processData: false,
				contentType: false,
				success: function(data) {
					// Handle the server response
					if (data.success) {
						// Item was added/removed successfully, update button appearance
						if (currentAction === "add") {
							button.addClass("added-to-wishlist");
						} else if (currentAction === "remove") {
							button.removeClass("added-to-wishlist");
						}
					} else {
						// Display an error message or handle the error case
					}
				},
				error: function(error) {
					console.error("Error:", error);
				}
			});
		});
	</script> -->
	<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

	<?php include 'components/alert.php'; ?>
</body>

</html>