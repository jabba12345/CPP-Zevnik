<?php  
require_once "povezava.php";
include_once 'seja.php';

$error = '';

if (isset($_POST['sub'])) {
    $ime = $_POST['uporabnisko'];
    $mail = $_POST['mail'];
    $geslo = $_POST['geslo'];
	$geslo2=sha1($geslo);

    $checkQuery = "SELECT * FROM uporabniki WHERE email='$mail'";
    $checkResult = mysqli_query($link, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $error = "Uporabnik že obstaja!!!";
    } else {
        $sql = "INSERT INTO uporabniki (ime, geslo, email) VALUES ('$ime', '$geslo2', '$mail')";
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
    <title>Registracija</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #00AEEF, #1A1A2E);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            text-align: left;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            display: block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
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
