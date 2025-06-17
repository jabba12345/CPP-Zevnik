<?php 
require_once "povezava.php";
include_once 'seja.php';

if(isset($_POST['sub'])){
    $error = '';
    $ime = '';
    $mail = mysqli_real_escape_string($link, $_POST['mail']);
    $geslo = mysqli_real_escape_string($link, $_POST['pas']);
	$geslo2=sha1($geslo);

    
    $sql = "SELECT * FROM uporabniki WHERE email='$mail' AND geslo='$geslo2';";
    $result = mysqli_query($link, $sql);

    if(mysqli_num_rows($result)===1){
        $row=mysqli_fetch_array($result);
        $_SESSION['name']=$row['ime'];
        $_SESSION['idu'] = $row['uporabniki_id'];
        $_SESSION['log']=TRUE;
        $ime=$_SESSION['name'];
		header("location:index.php");
    } else {
        $error = 'Napačno geslo ali email!!!';
    }
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Prijava</title>
</head>
<body>

    <div class="prijava_container">
        <h1 id="prijava_naslov">PRIJAVA</h1>
        <?php if (!empty($error)){
            echo "<div class='error'>" . htmlspecialchars($error) . "</div>";
        }?>

        <form method="post" action="prijava.php">
            <label for="mail" class="prijava-label">E-mail</label>
            <input type="email" name="mail" id="mail"class="prijava_input" required>

            <label for="pas" class="prijava-label">Geslo</label>
            <input type="password" name="pas" id="pas" class="prijava_input" required>

            <button type="submit" name="sub" id="prijava_gumb">Prijavi se</button>
        </form>
        <a href="registracija.php" id="registracija_link">Registracija</a>
    </div>
</body>
</html>
