		<!-- <header class="header" style="background: rgba(255, 255, 255, 0.9) url('images/banner1.png') ; background-size: cover"> -->
		<header class="header">
			<div class="flex">

				<div class="logo-container">
					<a href="../home.php"><img src="../images/logo1.png" class="logo-image" alt="logo"></a>
					<a href="../home.php">
						<h3 class="h3">CANTINA TEOLOGICĂ</h3>
					</a>
				</div>
				<nav class=" navbar">
					<input type="text" id="search-input" placeholder="Caută după cuvânt cheie..." style="width:min-content">
					<!-- <input type="text" id="search-input" placeholder="Search by keyword..." style="width:min-content"> -->
				</nav>
				<div class="icons">
					<i class="bx bxs-user" id="user-btn"></i>
				</div>
				<div class="user-box">
					<p><span><?php echo $_SESSION['user_name']; ?></span></p>
					<a href="../logout.php" class="logout-btn">Deconectare</a>
				</div>

			</div>
		</header>