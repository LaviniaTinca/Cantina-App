		<header class="header">
			<div class="flex">
				<div class="logo-container">
					<a href="../home.php"><img src="../images/logo-cantina2.png" class="logo-image" alt="logo"></a>
				</div>
				<nav class=" navbar">
					<input type="text" id="search-input" placeholder="Caută după cuvânt cheie..." style="width:min-content">
				</nav>
				<div class="icons">
					<i class="fas fa-bars" id="collapse-btn"></i>
					<i class="bx bxs-user" id="user-btn"></i>
				</div>
				<div class="user-box">
					<p><span><?php echo $_SESSION['user_name']; ?></span></p>
					<a href="../logout.php" class="logout-btn">Deconectare</a>
				</div>
			</div>
		</header>