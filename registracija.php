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

	$geslo2 = password_hash($geslo, PASSWORD_DEFAULT); 

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
    <link rel="stylesheet" href="style.css">
    <title>Registracija</title>
</head>
<body>
    <div class="registracija_container">
        <h1 class="naslov-registracija">REGISTRACIJA</h1>

        <?php if (!empty($error)){
            echo "<div class='error'>" . htmlspecialchars($error) . "</div>";
        }?>

        <form action="registracija.php" method="post">
            <label for="uporabnisko" class="registracija_label">Uporabniško ime</label>
            <input type="text" name="uporabnisko" class="input-registracija" required maxlength="50">

            <label for="mail" class="registracija_label">E-mail</label>
            <input type="email" name="mail" class="input-registracija" required maxlength="100">

            <label for="geslo" class="registracija_label">Geslo</label>
            <input type="password" name="geslo" class="input-registracija" required minlength="8">

            <button type="submit" name="sub" class="gumb-registracija">Registriraj se</button>
        </form>
        <a href="prijava.php" class="povezava-prijava">Nazaj na prijavo</a>
    </div>
</body>
</html>

