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
        $success_msg[] = "Mesajul a fost trimis!";
    } else {
        $error_msg[] = "Eroare la trimiterea mesajului";
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
            <h1 style="color: var(--green)">contact</h1>
        </div>
        <div class="title2">
            <a href="home.php">acasă </a><span>/ contact</span>
        </div>

        <div class="form-container flex">
            <form method="post" action="contact.php">

                <div class="title">
                    <img src="images/download.png" alt="contact" class="logo-image">
                    <ul>
                        <li> <box-icon name='phone'></box-icon>
                            0742 222 222</li>
                        <li><box-icon name='map'></box-icon>Str. Episcop Nicolae Ivan, FN </li>
                        <li> Cluj-Napoca, RO</li>
                    </ul>
                    <hr class="dotted-line">
                    <h3 style="color: var(--dark-olive);">...sau trimite un mesaj</h3>
                </div>
                <div class="input-field contact">
                    <p>nume <sup>*</sup></p>
                    <input class="" type="text" name="name">
                </div>
                <div class="input-field">
                    <p>email <sup>*</sup></p>
                    <input type="email" name="email" required>
                </div>
                <div class="input-field">
                    <p>număr de telefon<sup>*</sup></p>
                    <input type="text" name="number">
                </div>
                <div class="input-field">
                    <p>mesajul tău <sup>*</sup></p>
                    <textarea name="message" required></textarea>
                </div>
                <button type="submit" name="contact" class="menu0-btn">Trimite</button>
            </form>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d21862.041338626434!2d23.578225580439714!3d46.76972218871818!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47490c212e3fdddd%3A0x7952f3e44e14cf24!2sFaculty%20of%20Orthodox%20Theology!5e0!3m2!1sen!2sro!4v1690386891307!5m2!1sen!2sro" width="600" height="600" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </main>
    <?php include 'components/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <script src="script.js"></script>
    <?php include 'components/alert.php'; ?>
</body>

</html>