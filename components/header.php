<header class="header">
	<div class="flex">
		<div class="logo-container">
			<a href="home.php" class="logo-image"></a>
			<a href="home.php" style="text-decoration: none; color:var(--olive); margin-left:10px;">
				<h3>CANTINA</h3>
			</a>
			<a href="home.php" class="logo-image" style="background-image: url('https://thumbs.dreamstime.com/z/four-bowls-saffron-pepper-ras-en-hanout-fresh-mint-garlic-teak-wood-table-top-66262939.jpg');     transform: rotate(90deg) scale(1.2); width:50px; height:40px"></a>
			<!-- <a href="home.php" class="logo-image" style="background-image: url('https://thumbs.dreamstime.com/z/rosemary-spices-isolates-white-12048699.jpg'); "></a> -->
		</div>
		<nav class=" navbar">
			<a href="home.php">home</a>
			<a href="#about">about</a>
			<a href="#menu">menu</a>
		</nav>
		<div class="icons">
			<i class="bx bxs-user" id="user-btn"></i>
			<?php
			$count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
			$count_wishlist_items->execute([$user_id]);
			$total_wishlist_items = $count_wishlist_items->rowCount();
			?>
			<a href="wishlist.php" class="cart-btn"><i class="bx bx-heart"></i><sup><?= $total_wishlist_items ?></sup></a>
			<?php
			$count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
			$count_cart_items->execute([$user_id]);
			$total_cart_items = $count_cart_items->rowCount();
			?>
			<a href="cart.php" class="cart-btn"><i class="bx bx-cart-download"></i><sup><?= $total_cart_items ?></sup></a>
			<i class='bx bx-list-plus' id="menu-btn" style="font-size: 2rem;"></i>
		</div>
		<div class="user-box">
			<p>Hello, <span><?php echo $_SESSION['user_name']; ?></span></p>
			<?php
			if ($_SESSION['user_type'] == 'admin') { ?>
				<a href="admin.php">Admin Section</a>
			<?php } ?>
			<a href="logout.php" class="logout-btn">Logout</a>
		</div>

	</div>
</header>