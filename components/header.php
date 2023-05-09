<header class="header">
	<div class="flex">
		<div class="logo-container">
			<a href="home.php"><img src="images/logo1.png" class="logo-image" alt="logo"></a>
			<a href="home.php">
				<h3 class="h3">CANTINA</h3>
			</a>
			<a href="home.php"><img src="images/logo1.png" class="logo-image" alt="logo" style=" transform: rotate(45deg) scale(1.2);"></a>
		</div>
		<nav class=" navbar">
			<a href="home.php">home</a>
			<a href="view_products.php">menu</a>
		</nav>
		<div class="icons">
			<i class="bx bxs-user" id="user-btn"></i>
			<?php
			$count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
			$count_wishlist_items->execute([$user_id]);
			$total_wishlist_items = $count_wishlist_items->rowCount();
			?>
			<a href="not_found.php?page=wishlist" class="cart-btn"><i class="bx bx-heart"></i><sup><?= $total_wishlist_items ?></sup></a>
			<?php
			$count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
			$count_cart_items->execute([$user_id]);
			$total_cart_items = $count_cart_items->rowCount();
			?>
			<a href="not_found.php?page=cart" class="cart-btn"><i class="bx bx-cart-download"></i><sup><?= $total_cart_items ?></sup></a>
			<i class='bx bx-list-plus' id="menu-btn" style="font-size: 2rem;"></i>
		</div>
		<div class="user-box">
			<p>Hello, <span><?php echo $_SESSION['user_name']; ?></span></p>
			<p>Location, <span><?php echo $_SESSION['location']; ?></span></p>
			<?php
			if ($_SESSION['user_type'] == 'admin') { ?>
				<a href="admin.php">Admin Section</a>
			<?php } ?>
			<a href="logout.php" class="logout-btn">Logout</a>
		</div>

	</div>
</header>