<?php 
require_once "povezava.php";
include_once 'seja.php';

if(isset($_POST['sub'])){
    $error = '';
    $ime = '';
    $mail = $_POST['mail'];
    $geslo = $_POST['pas'];
	$geslo2=sha1($geslo);
    
    $sql = "SELECT * FROM uporabniki WHERE email='$mail' AND geslo='$geslo2';";
    $result = mysqli_query($link, $sql);

    if(mysqli_num_rows($result)===1){
        $row=mysqli_fetch_array($result);
        $_SESSION['name']=$row['ime'];
        $_SESSION['idu'] = $row['uporabniki_id'];
        $_SESSION['log']=TRUE;
        $ime=$_SESSION['name'];
		header("location:izbira_kategorije.php");
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
    <title>Prijava</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #1A1A2E, #00AEEF);
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
        }
        h1 {
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        a {
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
        .display_ime {
            position: absolute;
            top: 10px;
            left: 20px;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <?php if (!empty($ime)): ?>
        <div class="display_ime"><?php echo htmlspecialchars($ime); ?></div>
    <?php endif; ?>

    <div class="container">
        <h1>PRIJAVA</h1>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="prijava.php">
            <label for="mail">E-mail</label>
            <input type="email" name="mail" id="mail" required>

            <label for="pas">Geslo</label>
            <input type="password" name="pas" id="pas" required>

            <button type="submit" name="sub">Prijavi se</button>
        </form>
        <a href="registracija.php">Registracija</a>
    </div>
</body>
</html>
