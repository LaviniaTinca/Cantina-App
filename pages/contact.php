<?php
include '../config/connection.php';
include '../config/session.php';
include '../api/functions.php';

// Handle contact us 
if (isset($_POST['contact'])) {
    $id = unique_id();
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');
    $number = htmlspecialchars($_POST['number'], ENT_QUOTES, 'UTF-8');


    try {
        $stmt = $conn->prepare("INSERT INTO messages(`id`, `name`, `email`, `number`, `message`) VALUES (?,?,?,?,?)");
        if ($stmt->execute([$id, $name, $email, $number, $message])) {
            $success_msg[] = "Mesajul a fost trimis!";
        } else {
            $error_msg[] = "Eroare la trimiterea mesajului";
        }
    } catch (PDOException $th) {
        $error_msg = 'Eroare ' . $th->getMessage();
    } catch (Exception $th) {
        $error_msg = 'Eroare' . $th->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - contact</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</head>

<body>
    <!--NAVBAR  HEADER SECTION -->
    <section>
        <?php include '../components/header.php'; ?>
    </section>

    <main class="main">
        <div class="banner">
            <h1 style="color: var(--green)">contact</h1>
        </div>
        <div class="title2">
            <a href="../pages/home.php">acasă </a><span>/ contact</span>
        </div>

        <div class="form-container flex " style="width: 70vw;">
            <form method="post" action="../pages/contact.php">
                <div class="title">
                    <ul>
                        <li> <i class='bx bx-phone'></i>
                            0742 222 222</li>
                        <li><i class='bx bx-map'></i>Str. Episcop Nicolae Ivan, FN </li>
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
    <?php include '../components/footer.php'; ?>
    <?php include '../components/alert.php'; ?>

    <script src="../js/script.js"></script>
</body>

</html>