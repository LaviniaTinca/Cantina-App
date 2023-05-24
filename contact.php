<?php
include 'php/connection.php';
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: login.php");
}

// Handle contact us 
if (isset($_POST['contact'])) {
    $id = unique_id();
    $email = $_POST['email'];
    $message = $_POST['message'];
    $number = $_POST['number'];


    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    $stmt = $conn->prepare("INSERT INTO messages(`id`, `user_id`, `message`, `number`) VALUES (?,?,?, ?)");
    if ($stmt->execute([$id, $user['id'], $message, $number])) {
        $success_msg[] = "Message sent!";
    } else {
        $error_msg[] = "Error sending mesaage ";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css">

    <title>Cantina - contact</title>
</head>

<body>
    <!--NAVBAR  HEADER SECTION -->
    <section>
        <?php include 'components/header.php'; ?>
    </section>

    <main class="main">
        <div class="banner" style="height:200px; ">
            <h1 style="color: var(--green)">contact us</h1>
        </div>
        <div class="title2">
            <a href="home.php">home </a><span>/ contact us</span>
        </div>

        <div class="form-container">
            <form method="post" action="contact.php">
                <div class="title">
                    <img src="images/download.png" class="logo-image">
                    <h1>leave a message</h1>
                </div>
                <div class="input-field">
                    <p>your name <sup>*</sup></p>
                    <input type="text" name="name">
                </div>
                <div class="input-field">
                    <p>your email <sup>*</sup></p>
                    <input type="email" name="email" required>
                </div>
                <div class="input-field">
                    <p>your number <sup>*</sup></p>
                    <input type="text" name="number">
                </div>
                <div class="input-field">
                    <p>your message <sup>*</sup></p>
                    <textarea name="message" required></textarea>
                </div>
                <button type="submit" name="contact" class="auth-button" style="background-color:var(--olive)">send message</button>
            </form>

        </div>

        <?php include 'components/footer.php'; ?>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="script.js"></script>
    <?php include 'components/alert.php'; ?>
</body>

</html>