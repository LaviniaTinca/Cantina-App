<header class="header" <?php if (basename($_SERVER['PHP_SELF']) == 'home.php') echo 'style="height:120px"'; ?>>
	<?php if (basename($_SERVER['PHP_SELF']) == 'home.php') include 'components/announcement.php'; ?>

	<div class="flex">
		<div class="logo-container">
			<a href="home.php"><img src="images/logo1.png" class="logo-image" alt="logo"></a>
			<a href="home.php">
				<h3 class="h3">CANTINA TEOLOGICĂ</h3>
			</a>
		</div>

		<nav class="navbar">
			<a href="home.php" <?php if (basename($_SERVER['PHP_SELF']) == 'home.php') echo 'class="active"'; ?>>acasa</a>
			<a href="view_menu.php" <?php if (basename($_SERVER['PHP_SELF']) == 'view_menu.php') echo 'class="active"'; ?>>meniu</a>
			<a href="contact.php" <?php if (basename($_SERVER['PHP_SELF']) == 'contact.php') echo 'class="active"'; ?>>contact</a>
			<?php
			if ($_SESSION['user_type'] == 'admin') { ?>
				<!-- <a href="view_products.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'view_products.php') ? 'class="active"' : ''; ?>>products</a> -->
				<a href="admin.php" <?php if (basename($_SERVER['PHP_SELF']) == 'admin.php') echo 'class="active"'; ?>>Admin</a>
			<?php } ?>
		</nav>
		<div class="icons">
			<!-- <i class="bx bxs-user" id="user-btn"></i> -->
			<!-- <?php
					$count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
					$count_wishlist_items->execute([$user_id]);
					$total_wishlist_items = $count_wishlist_items->rowCount();
					?>
			<a href="not_found.php?page=wishlist" class="cart-btn"><i class="bx bx-heart"></i><sup><?= $total_wishlist_items ?></sup></a> -->
			<?php
			$count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
			$count_cart_items->execute([$user_id]);
			$total_cart_items = $count_cart_items->rowCount();
			?>
			<a href="cart.php?page=cart"><i class="bx bx-cart-download"></i><sup><?= $total_cart_items ?></sup></a>
			<i class='bx bx-list-plus' id="menu-btn" style="font-size: 2rem;"></i>
			<i class="bx bxs-user" id="user-btn"></i>
		</div>
		<div class="user-box">
			<p>Buna ziua, <span><?php echo $_SESSION['user_name']; ?></span></p>
			<!-- <?php
					if ($_SESSION['user_type'] == 'admin') { ?>
				<a href="admin.php">Admin Section</a>
			<?php } ?> -->
			<a href="logout.php" class="logout-btn">Logout</a>
		</div>

	</div>
</header>