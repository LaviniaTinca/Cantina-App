<?php
try {
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
		include 'home/announcement.php';
	}
} catch (PDOException $e) {
	$error_msg[] = "Eroare: " . $e->getMessage();
} catch (Exception $e) {
	$error_msg[] = "Eroare: " . $e->getMessage();
} ?>

	<div class="flex">
		<div class="logo-container">
			<a href="../pages/home.php"><img src="../public/images/logo-cantina2.png" class="logo-image" alt="logo"></a>
		</div>
		<nav class="navbar">
			<div class="nav-links" id="nav-links">
				<div>
					<a href="../pages/home.php" <?php if (basename($_SERVER['PHP_SELF']) == '../pages/home.php') echo 'class="active"'; ?>>acasa</a>
					<a href="../pages/view_menu.php" <?php if (basename($_SERVER['PHP_SELF']) == '../pages/view_menu.php') echo 'class="active"'; ?>>meniu</a>
					<a href="../pages/view_orders.php" <?php if (basename($_SERVER['PHP_SELF']) == '../pages/view_orders.php') echo 'class="active"'; ?>>comenzi</a>
					<a href="../pages/contact.php" <?php if (basename($_SERVER['PHP_SELF']) == '../pages/contact.php') echo 'class="active"'; ?>>contact</a>
					<?php if ($_SESSION['user_type'] == 'admin') { ?>
						<a href="../admin/admin.php" <?php if (basename($_SERVER['PHP_SELF']) == 'admin.php') echo 'class="active"'; ?>>
							Admin
						</a>
					<?php } ?>
				</div>
			</div>
		</nav>

		<div class="icons">
			<?php
			try {
				$count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
				$count_cart_items->execute([$user_id]);
				$total_cart_items = $count_cart_items->rowCount();
			?>
				<a href="cart.php?page=cart"><i class="bx bx-cart-download" title="Coș de cumpărături"></i><sup><?= $total_cart_items ?></sup></a>
				<i class="bx bxs-user" id="user-btn" title="Utilizator"></i>
			<?php
			} catch (PDOException $e) {
				$error_msg[] = "Eroare: " . $e->getMessage();
			} catch (Exception $e) {
				$error_msg[] = "Eroare: " . $e->getMessage();
			}
			?>
		</div>
		<div class="user-box">
			<p><span><?php echo $_SESSION['user_name']; ?></span></p>
			<a href="logout.php" class="logout-btn">Deconectare</a>
		</div>

	</div>
	</header>