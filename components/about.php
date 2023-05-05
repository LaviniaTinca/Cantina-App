<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - My Website</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
    }

    .about-us-container {
      width: 97%;
      margin: 0 auto;
      padding: 2rem;
      background-color: white;
      box-shadow: 0 0 1rem var(--olive);
    }

    .main-page-title {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 2rem;
      color: teal;
    }

    .about-us-content {
      font-size: 1.2rem;
      line-height: 1.5;
      margin-bottom: 2rem;
      text-align: justify;
    }

    /* .about-us-img {
      float: left;
      margin-right: 1rem;
      margin-bottom: 1rem;
      width: 500px;
      height: 500px;
      border-radius: 50%;
      border: 3px solid teal;
    } */
    .about-us-img {
      float: left;
      margin-right: 1rem;
      margin-bottom: 1rem;
      width: 500px;
      height: auto;
      border-radius: 20%;
      border: 1px solid var(--olive);
      background: url('public/assets/staff.webp') no-repeat center center;
      background-size: cover;

    }

    .about-us-contact {
      font-size: 1.2rem;
      line-height: 1.5;
      margin-bottom: 2rem;
      text-align: justify;
    }

    .about-us-contact a {
      color: teal;
      text-decoration: none;
      font-weight: bold;
    }

    .about-us-contact a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <!-- <div class="about-us-container" style="background-image: url('https://thumbs.dreamstime.com/z/cooking-banner-background-spices-vegetables-top-view-cooking-banner-background-spices-vegetables-top-view-free-168096882.jpg');"> -->
  <div class="about-us-container">

    <h1 class=" main-page-title" style="color: var(--olive)">About Us</h1>

    <img src="public/assets/staff.webp" alt="Our Team" class="about-us-img">

    <p class="about-us-content">
      Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec et tortor quis nisl efficitur semper eu ac odio.
      Nullam eget velit tincidunt, blandit ipsum nec, viverra lorem. Vestibulum posuere quam a vestibulum finibus.
      Aenean aliquam sapien at ante sollicitudin commodo. Fusce quis imperdiet magna, nec egestas ipsum.
      Morbi faucibus enim vitae urna commodo, at semper nisi luctus. Sed pharetra erat eu diam commodo, id viverra eros tristique.
    </p>

    <p class="about-us-content">
      Morbi in quam auctor, elementum libero eu, tincidunt odio. Etiam sit amet purus et sapien rhoncus consectetur.
      Fusce vel lacinia sapien. Nullam laoreet sem vel mauris pharetra dictum. Praesent vitae massa sed ante luctus rutrum vel eu enim.
      Praesent id dolor ut velit tincidunt tincidunt ut at erat. Nullam ultrices nulla et sagittis elementum.
      Phasellus euismod, leo ut auctor dignissim, lorem eros sagittis turpis, sit amet suscipit nulla lacus a est.
    </p>

    <p class="about-us-contact">
      Come to visit us and take the <a href="#menu"><strong>lunch meal!</strong></a>.
    </p>
  </div>
</body>

</html>