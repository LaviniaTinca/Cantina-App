<?php
// Handle email subscription
if (isset($_POST['email'])) {
  $email = $_POST['email'];
  $id = unique_id();

  // Prepare and bind statement
  $stmt = $conn->prepare("INSERT INTO subscribers (id, email) VALUES (?,?)");
  // $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
  // $stmt->bind_param("s", $email);
  // $insert_user = $conn->prepare("INSERT INTO `users`(id,name,email,password) VALUES(?,?,?,?)");
  // 			$insert_user->execute([$id,$name,$email,$pass]);
  $stmt->execute([$id, $email]);

  // Execute statement
  if ($stmt->execute()) {
    // echo "Subscribed with email: " . $email;
    $messages = "Error subscribing with email: " . $email;
  } else {
    $messages = "Error subscribing with email: " . $email;
  }

  $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cantina- newsletter</title>
  <style>
    .subscribe-container {
      width: 80%;
      margin: 110px auto;
      text-align: center;
      padding: 30px;
    }

    .subscribe-title {
      font-size: 2rem;
      display: flex;
      align-items: baseline;
      justify-content: space-around;
      color: var(--olive);
    }

    .subscribe-icon {
      font-size: 3.5rem;
      margin-right: 20px;
    }

    .subscribe-description {
      font-size: 1rem;
      margin-bottom: 40px;
    }

    .subscribe-input-container {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .subscribe-input {
      padding: 10px;
      margin-right: 10px;
      width: 75%;
      background-color: #f2f2f2;
      border: none;
      border-radius: 5px 0 0 5px;
    }

    .subscribe-button {
      padding: 10px 20px;
      background-color: var(--olive);
      border: none;
      border-radius: 0 5px 5px 0;
      color: white;
      cursor: pointer;
      transition: all 0.3s ease-in-out;
    }

    .subscribe-button:hover {
      background-color: #008b8b;
    }
  </style>
</head>

<body>

  <div class="subscribe-container" style="background: rgba(255, 255, 255, 0.1) url('https://thumbs.dreamstime.com/z/cooking-banner-background-spices-vegetables-top-view-cooking-banner-background-spices-vegetables-top-view-free-168096882.jpg') ; background-size:cover">
    <h3 class="subscribe-title">
      Newsletter
    </h3>
    <p class="subscribe-description">for the daily menu</p>
    <div class="subscribe-input-container">
      <box-icon name='envelope'></box-icon>
      <input type="text" class="subscribe-input" placeholder="Enter email here..." />
      <button class="subscribe-button">Subscribe</button>
    </div>
  </div>

  <script>
    const emailInput = document.querySelector(".subscribe-input");
    const subscribeButton = document.querySelector(".subscribe-button");

    subscribeButton.addEventListener("click", () => {
      const email = emailInput.value.trim();

      if (email !== "") {
        // Send email to server to subscribe to newsletter
        console.log("Subscribed with email:", email);
      } else {
        alert("Please enter a valid email address.");
      }
    });
  </script>
  <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

</body>

</html>