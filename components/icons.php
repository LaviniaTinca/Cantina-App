<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-l3JI0KjJdRxp+Rld6cpZlkvKJdLcTjTn8WpC+nYvF+vAgxKjgNSs8W/2/wcwv7I0HNlSbS7VovQFlYAR57uxA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <title>Cantina</title>
    <style>
      .kitchen-icons {
      display: flex;
      justify-content: space-between;
      margin: 32px;
    }

    .kitchen-icon {
      width: 30%;
      text-align: center;
      transition: all 0.3s ease-in-out;
    }

    .kitchen-icon:hover {
      transform: translateY(-10px);
      box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.3);
    }

    .kitchen-icon i {
      font-size: 64px;
      color: olivedrab;
      margin: 16px;
      display: block;
    }

    .kitchen-icon h3 {
      font-size: 24px;
      margin: 10px;
      color: olivedrab;
    }

    .kitchen-icon p {
      font-size: 18px;
      line-height: 1.5;
      text-align: justify;
      margin: 10px;
    }
  </style>
</head>
<body>
  <section class="kitchen-icons">
    <div class="kitchen-icon">
      <i class="fas fa-pepper-hot" style="color: #E32227"></i>
      <h3>Spicy Flavors</h3>
      <p>We use a variety of spices to add depth and heat to our dishes, creating a flavor that will leave you wanting more.</p>
    </div>

    <div class="kitchen-icon">
      <i class="fas fa-seedling" style="color: green;"></i>
      <h3>Fresh Ingredients</h3>
      <p>We believe that the best dishes start with the freshest ingredients, which is why we source our produce locally and seasonally whenever possible.</p>
    </div>

    <div class="kitchen-icon">
      <i class="fas fa-utensils" style="color: #35424A";></i>
      <h3>Expert Craftsmanship</h3>
      <p>Our chefs are highly skilled and trained in the art of cooking, bringing their expertise and creativity to every dish on our menu.</p>
    </div>
  </section>
</body>
</html>