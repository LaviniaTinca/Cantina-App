<?php
// Fetch the latest announcement from the database
// $query = "SELECT * FROM `announcements` ORDER BY created_at DESC LIMIT 1";
$query = "SELECT * FROM `announcements` WHERE is_set = 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$announcement = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<header class="header" <?php if ($announcement) {
							if (basename($_SERVER['PHP_SELF']) == 'home.php') {
								echo 'style="height:120px"';
							}
						} ?>>
	<?php if ($announcement && basename($_SERVER['PHP_SELF']) == 'home.php') {
		include 'components/announcement.php';
	} ?>


	<div class="flex">
		<div class="logo-container">
			<a href="home.php"><img src="images/logo-cantina1.png" class="logo-image" alt="logo"></a>
		</div>

		<nav class="navbar">
			<a href="home.php" <?php if (basename($_SERVER['PHP_SELF']) == 'home.php') echo 'class="active"'; ?>>acasa</a>
			<a href="view_menu.php" <?php if (basename($_SERVER['PHP_SELF']) == 'view_menu.php') echo 'class="active"'; ?>>meniu</a>
			<a href="view_orders.php" <?php if (basename($_SERVER['PHP_SELF']) == 'view_orders.php') echo 'class="active"'; ?>>comenzi</a>
			<a href="contact.php" <?php if (basename($_SERVER['PHP_SELF']) == 'contact.php') echo 'class="active"'; ?>>contact</a>
			<?php
			if ($_SESSION['user_type'] == 'admin') { ?>
				<a href="admin/admin.php" <?php if (basename($_SERVER['PHP_SELF']) == 'admin.php') echo 'class="active"'; ?>>Admin</a>
			<?php } ?>
		</nav>
		<div class="icons">
			<?php
			$count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
			$count_cart_items->execute([$user_id]);
			$total_cart_items = $count_cart_items->rowCount();
			?>
			<a href="cart.php?page=cart"><i class="far fa-heart" title="Wishlist"></i></a>
			<a href="cart.php?page=cart"><i class="far fa-bell" title="Notificări"></i></a>
			<a href="cart.php?page=cart"><i class="bx bx-cart-download" title="Coș de cumpărături"></i><sup><?= $total_cart_items ?></sup></a>

			<!-- <i class='bx bx-list-plus' id="menu-btn" style="font-size: 2rem;"></i> -->
			<i class="bx bxs-user" id="user-btn" title="Utilizator"></i>
		</div>
		<div class="user-box">
			<p><span><?php echo $_SESSION['user_name']; ?></span></p>
			<a href="logout.php" class="logout-btn">Deconectare</a>
		</div>

	</div>
</header>