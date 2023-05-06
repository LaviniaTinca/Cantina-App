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


	$varify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
	$varify_cart->execute([$user_id, $product_id]);

	$max_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
	$max_cart_items->execute([$user_id]);

	if ($varify_cart->rowCount() > 0) {
		$warning_msg[] = 'product already exist in your cart';
	} else if ($max_cart_items->rowCount() > 20) {
		$warning_msg[] = 'cart is full';
	} else {
		$select_price = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
		$select_price->execute([$product_id]);
		$fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);

		$insert_cart = $conn->prepare("INSERT INTO `cart`(id, user_id,product_id,price,qty) VALUES(?,?,?,?,?)");
		$insert_cart->execute([$id, $user_id, $product_id, $fetch_price['price'], $qty]);
		$success_msg[] = 'product added to cart successfully';
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
	<link rel="stylesheet" href="css/style.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

	<!-- HEADER SECTION -->
	<section>
		<?php include 'components/header.php'; ?>
	</section>

	<main class="main" style=" margin-top: 100px;">
		<div class="banner" style=" height: 200px; color: var(--olive); background: rgba(255, 255, 255, 0.9) url('https://thumbs.dreamstime.com/z/cooking-banner-background-spices-vegetables-top-view-cooking-banner-background-spices-vegetables-top-view-free-168096882.jpg') ; background-size:cover">
			<h1 style="color:var(--green)">today's menu</h1>
		</div>
		<div class="title2">
			<a href="home.php">home </a><span>/ menu</span>
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
								<a href="not_found.php?page=checkout?get_id=<?= $fetch_products['id']; ?>" class="add-btn">add</a>
								<div class="products-img-wrapper">
									<img src="image/<?= $fetch_products['image']; ?>" alt="product image" class="img product-image">

								</div>
								<br>
								<div class="button">
									<button type="submit" name="add_to_cart"><i class="bx bx-cart"></i></button>
									<button type="submit" name="add_to_wishlist"><i class="bx bx-heart"></i></button>
									<a href="not_found.php?page=details?pid=<?php echo $fetch_products['id']; ?>" class="bx bxs-show"></a>
								</div>
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
	<?php include 'components/alert.php'; ?>
</body>

</html>