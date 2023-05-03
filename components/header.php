<header class="header">
	<div class="flex">
	<!-- <div class="logo2 logo-image"></div> -->
		<div class="logo-container">
		<a href="index.php" class="logo-image"></a>
		<a href="index.php" style="text-decoration: none; color:teal; margin-left:10px;"><h3>CANTINA</h3></a>
		</div>
			<nav class="navbar">
			<a href="home.php">home</a>
			<a href="#about">about</a>
			<a href="#menu">menu</a>            

			<!-- <a href="view_products.php">products</a>
			<a href="order.php">orders</a>
			<a href="about.php">about us</a>
			<a href="contact.php">contact us</a> -->
		</nav>
		<div class="icons">
			<i class="bx bxs-user" id="user-btn"></i>
			<?php 
				$count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
				$count_wishlist_items->execute([$user_id]);
				$total_wishlist_items = $count_wishlist_items->rowCount();
			?>
			<a href="wishlist.php" class="cart-btn"><i class="bx bx-heart"></i><sup><?=$total_wishlist_items ?></sup></a>
			<?php 
				$count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
				$count_cart_items->execute([$user_id]);
				$total_cart_items = $count_cart_items->rowCount();
			?>
			<a href="cart.php" class="cart-btn"><i class="bx bx-cart-download"></i><sup><?=$total_cart_items ?></sup></a>
			<i class='bx bx-list-plus' id="menu-btn" style="font-size: 2rem;"></i>
		</div>
		<div class="user-box">
			<p>Hello, <span><?php echo $_SESSION['user_name']; ?></span></p>
			<?php
			if ($_SESSION['user_type']== 'admin') { ?>
            <a href="admin.php">Admin Section</a>
        	<?php } ?>
			<a href="logout.php" class="logout-btn">Logout</a>
		</div>

	</div>
</header>