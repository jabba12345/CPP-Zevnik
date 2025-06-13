<?php  
require_once "povezava.php";
include_once 'seja.php';

$error = '';

if (isset($_POST['sub'])) {
    $ime = filter_var($_POST['uporabnisko'], FILTER_SANITIZE_SPECIAL_CHARS);
    $mail = filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL);
    $geslo = $_POST['geslo'];

    $ime = mysqli_real_escape_string($link, $ime);
    $mail = mysqli_real_escape_string($link, $mail);
    $geslo = mysqli_real_escape_string($link, $geslo);

	$geslo2=sha1($geslo);

    $checkQuery = "SELECT * FROM uporabniki WHERE email='$mail'";
    $checkResult = mysqli_query($link, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $error = "Uporabnik že obstaja!!!";
    } else {
        $sql = "INSERT INTO uporabniki (ime, geslo, email,tip_uporabnika_id) VALUES ('$ime', '$geslo2', '$mail',2)";
        $result = mysqli_query($link, $sql);
        header("Location: prijava.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="registracija.css">
    <title>Registracija</title>
    
</head>
<body>
    <div class="container">
        <h1>REGISTRACIJA</h1>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="registracija.php" method="post">
            <label for="uporabnisko">Uporabniško ime</label>
            <input type="text" name="uporabnisko" id="uporabnisko" required maxlength="50">

            <label for="mail">E-mail</label>
            <input type="email" name="mail" id="mail" required maxlength="100">

            <label for="geslo">Geslo</label>
            <input type="password" name="geslo" id="geslo" required minlength="8">

            <button type="submit" name="sub">Registriraj se</button>
        </form>
        <a href="prijava.php">Nazaj na prijavo</a>
    </div>
</body>
</html>
