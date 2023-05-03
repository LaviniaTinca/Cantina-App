	<!-- <header class="header" style="background-image: url('https://media.istockphoto.com/photos/healthy-food-background-picture-id515273512');"> -->
	<!-- <header class="header" style="background-image: url('https://media.gettyimages.com/photos/fruits-and-vegetables-disposed-on-a-half-frame-shape-picture-id139726927?b=1&k=6&m=139726927&s=612x612&w=0&h=RMwLTN6eHgZ2uI0ebPvQHXnUePyIVa8osZ4vDwtcOuI=');"> -->
	<!-- <header class="header" style="background-image: url('https://static.wixstatic.com/media/883d86_05bed599d0ac48d7b7535bd6d4e67dd3~mv2.png/v1/fill/w_980,h_653,al_c,usm_0.66_1.00_0.01/883d86_05bed599d0ac48d7b7535bd6d4e67dd3~mv2.png');"> -->
	<!-- <header class="header" style="background-image: url('https://image.freepik.com/free-photo/fresh-vegetables-frame-white-wood-with-copy-space_116547-627.jpg');"> -->
	<!-- <header class="header" style="background-image: url('https://thumbs.dreamstime.com/z/vegetables-top-view-kitchen-table-white-wooden-natural-vegetable-garden-bio-product-concept-copy-space-template-vegetables-top-103025196.jpg');"> -->
	<!-- <header class="header" style="background-image: url('https://thumbs.dreamstime.com/z/collection-fresh-green-vegetables-white-rustic-background-lettuce-celery-beans-capsicum-peppers-peas-brussels-sprouts-77627101.jpg');"> -->
	<!-- <header class="header" style="background-image: url('https://thumbs.dreamstime.com/z/minced-meat-frame-white-bowl-white-wooden-table-45427435.jpg');"> -->
	<!-- <header class="header" style="background-image: url('https://thumbs.dreamstime.com/z/glass-green-healthy-juice-fruits-vegetables-glass-green-healthy-juice-fruits-vegetables-table-110670507.jpg');"> -->
	<header class="header" style="background-image: linear-gradient(to top, rgba(255,255,255,0.7), rgba(255,255,255,0.1)),
	url('https://thumbs.dreamstime.com/z/variety-autumn-harvest-vegetables-variety-autumn-harvest-vegetables-carrot-parsnip-chard-paprika-hokkaido-pumpkin-mushrooms-99887900.jpg');">
		<!-- <header class="header" style="background-image: url('https://image.shutterstock.com/z/stock-photo-organic-food-background-high-resolution-product-studio-photo-of-different-vegetables-on-wooden-330712538.jpg');"> -->
		<!-- <header class="header" style="background-image: linear-gradient(to top, rgba(255,255,255,0.7), rgba(255,255,255,0.1)),
	url('https://i.pinimg.com/originals/a1/12/15/a112152ef5b6d8fe425dd3cf0c3c7dd7.jpg');"> -->
		<!-- <header class="header" style="background-image: url('https://thumbs.dreamstime.com/z/vegetables-fruit-wood-background-healthy-food-salad-tomato-bell-pepper-parsley-egg-orange-kiwi-copy-space-concept-diet-80960046.jpg');"> -->

		<div class="flex">
			<!-- <div class="logo2 logo-image"></div> -->

			<div class="logo-container">
				<!-- <a href="home.php" class="logo-image"></a>
			<a href="home.php" style="text-decoration: none; color:var(--teal); margin-left:10px;"> -->
				<!-- <a href="home.php" style="text-decoration: none; color:#003300; margin-left:60px; margin-top: 20px">
					<h3>DASHBOARD</h3>
				</a> -->
			</div>
			<nav class="navbar">
				<!-- <a href="home.php">home</a>
			<a href="admin.php">dashboard</a> -->
				<!-- <a href="admin_product.php">products</a>
			<a href="admin_user.php">users</a> -->

				<!-- <a href="logout.php" class="logout-btn">Logout</a> -->

				<!-- <a href="view_products.php">products</a>
			<a href="order.php">orders</a>
			<a href="about.php">about us</a>
			<a href="contact.php">contact us</a> -->
			</nav>
			<div class="icons">
				<i class="bx bxs-user" id="user-btn"></i>
			</div>
			<div class="user-box">
				<p>Hello, <span><?php echo $_SESSION['user_name']; ?></span></p>
				<a href="logout.php" class="logout-btn">Logout</a>
			</div>

		</div>
	</header>