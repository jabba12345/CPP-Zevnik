<?php
include_once 'povezava.php';
include_once 'seja.php';

if(isset($_SESSION['idu'])){
    $uporabnik_id = $_SESSION['idu'];
    $sql = "SELECT * FROM uporabniki WHERE uporabniki_id = $uporabnik_id AND tip_uporabnika_id = 1";
    $result = mysqli_query($link, $sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Naslov strani</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="glava">
    <?php
    if (isset($_SESSION['idu'])) {
        $ime = $_SESSION['name'];
        echo "<p id='ime'>$ime</p>";
        echo "
            <a href='odjava.php'>
                <button id='odjava' type='button'>Odjava</button>
            </a>
        ";

        if (mysqli_num_rows($result) > 0) {
            echo '<div id="admin-btna">
                <button id="dodaj" class="gumbeki" onclick="location.href=\'dodajanje_vprasanja.php\'">Dodaj Vprasanja</button>
                <button id="brisi" class="gumbeki" onclick="location.href=\'brisanje_vprasanj.php\'">Uredi vprasanja</button>
            </div>';
        }

        echo "<button id='domov' class='gumbeki' onclick=\"location.href='index.php'\">Domov</button>";
    } else {
        
        echo " <p id='ime'>Niste prijavljeni</p> ";
        echo "
            <a href='prijava.php'>
                <button id='Prijava-btn' type='button'>Prijava</button>
            </a>
        ";
    }
    ?>
</div>
</body>
</html>
