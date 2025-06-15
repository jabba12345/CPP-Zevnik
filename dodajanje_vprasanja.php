<?php
require_once "povezava.php";
include_once "seja.php";

if (isset($_POST['sub'])) {



$vprasanje =$_POST['vprasanje'];
$kategorija_id = $_POST['kategorije'];
$tocke_vprasanja = $_POST['tocke_vprasanja'];


$st_pravilnih = 0;

for($i=1;$i<=4;$i++){
    $pravilen=$_POST['pravilen'.$i];
    if($pravilen == 1){
        $st_pravilnih++;
    }
}

$tip_vprasanja = 0;
if ($st_pravilnih <= 1) {
    $tip_vprasanja = 1;
} elseif ($st_pravilnih > 1) {
    $tip_vprasanja = 2;
}

    $sql = "INSERT INTO vprasanja (vprasanje, tipi_vprasanja_id, tocke_vprasanja) VALUES ('$vprasanje', $tip_vprasanja, '$tocke_vprasanja')";
    $result = mysqli_query($link, $sql);

    $vprasanje_id = mysqli_insert_id($link);



switch($kategorija_id){
    case 'A': 
        $kategorije_vprasanja_sql="INSERT INTO kategorije_vprasanja(kategorije_id,vprasanja_id) VALUES('1',$vprasanje_id)";
        $result = mysqli_query($link, $kategorije_vprasanja_sql);
        break;
    case 'B': 
        $kategorije_vprasanja_sql="INSERT INTO kategorije_vprasanja(kategorije_id,vprasanja_id) VALUES('2',$vprasanje_id)";
        $result = mysqli_query($link, $kategorije_vprasanja_sql);
        break;
    case 'C': 
        $kategorije_vprasanja_sql="INSERT INTO kategorije_vprasanja(kategorije_id,vprasanja_id) VALUES('3',$vprasanje_id)";
        $result = mysqli_query($link, $kategorije_vprasanja_sql);
        break;
    case 'D': 
        $kategorije_vprasanja_sql="INSERT INTO kategorije_vprasanja(kategorije_id,vprasanja_id) VALUES('4',$vprasanje_id)";
        $result = mysqli_query($link, $kategorije_vprasanja_sql);
        break;
    case 'Vse':
        for($i=1; $i<=4; $i++) { 
            $kategorije_vprasanja_sql = "INSERT INTO kategorije_vprasanja(kategorije_id,vprasanja_id) VALUES($i,$vprasanje_id)";
            $result=mysqli_query($link, $kategorije_vprasanja_sql);
        }
        break;
}
for($i=1;$i<=4;$i++){
    $odgovor = $_POST['odgovor'.$i];

    if (!empty($odgovor)) {
        $pravilen = $_POST['pravilen'.$i];
        $tocke_odgovor = $_POST['tocke_odgovor'.$i];
        $sql = "INSERT INTO odgovori (odgovor, je_pravilen, vprasanja_id, odgovori_tocke)
                VALUES ('$odgovor', '$pravilen', $vprasanje_id, '$tocke_odgovor')";
        $result = mysqli_query($link, $sql);
    }
}





if ($_FILES['slika'] && $_FILES['slika']['error'] === UPLOAD_ERR_OK) {
    $imgData = file_get_contents($_FILES['slika']['tmp_name']);
    $imgData = mysqli_real_escape_string($link, $imgData);
    $slika_sql = "INSERT INTO slike (vprasanja_id, slika) VALUES ($vprasanje_id, '$imgData')";
    $result=mysqli_query($link, $slika_sql);
}

header("Location: dodajanje_vprasanja.php?success=1");
exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dodajanje_vprasanj.css">
    <script src="dodajanje_vprasanj.js" defer></script>
    <title>Dodajanje Vprasanja</title>

    <button class="nazaj"onclick="location.href='index.php'">Nazaj</button>
</head>
<body>
    <?php include_once 'glava.php'; 

      $uporabnik_id = $_SESSION['idu'];

        $sql = "SELECT * FROM uporabniki WHERE uporabniki_id = $uporabnik_id AND tip_uporabnika_id = 1";
        $result = mysqli_query($link, $sql);
        if (mysqli_num_rows($result) == 0) {
            header("Location: index.php");
            exit(); 
        }
    ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="container">
        
            <div class="vprasanje-row">
                <label for="vprasanje" class="vprasanje-label">Vprasanje</label> 
                <input type="text" name="vprasanje" id="vprasanje_id" class="vprasanje-input" required>

                <label for="tock_vprasanje" class="tocke_vprasanja_label">Tocke:</label>
                <input type="number" name="tocke_vprasanja" class="tocke_vprasanja">
</div>
                <br>
                <div id="odgovori-fields">
                    <div class="odgovor-row">
                    <label for="odgovor1" class>Odgovor1: </label>
                    <input type="text" name="odgovor1" id="odgovor1_id" class="odgovor">
                    <label for="tocke_odgovor" class="tocke_odgovor_label">Tocke</label>
                    <input type="number" name="tocke_odgovor1" id="tocke_odgovor1" class="tocke_odgovor">
                    <select name="pravilen1" id="pravilen1" class="pravilen-select">
                        <option value="0">False</option>
                        <option value="1">True</option>
                    </select>
                    </div>
                    <br>

                <div class="odgovor-row">
                    <label for="odgovor2" class>Odgovor2: </label>
                    <input type="text" name="odgovor2" id="odgovor2_id" class="odgovor">
                    <label for="tocke_odgovor2" class="tocke_odgovor_label">Tocke</label>
                    <input type="number" name="tocke_odgovor2" id="tocke_odgovor2" class="tocke_odgovor">
                    <select name="pravilen2" id="pravilen2" class="pravilen-select">
                        <option value="0">False</option>
                        <option value="1">True</option>
                    </select>
                </div>
                    <br>

                    <div class="odgovor-row">
                    <label for="odgovor3" class>Odgovor3: </label>
                    <input type="text" name="odgovor3" id="odgovor3_id" class="odgovor">
                    <label for="tocke_odgovor3" class="tocke_odgovor_label">Tocke</label>
                    <input type="number" name="tocke_odgovor3" id="tocke_odgovor3" class="tocke_odgovor">
                    <select name="pravilen3" id="pravilen3" class="pravilen-select">
                        <option value="0">False</option>
                        <option value="1">True</option>
                    </select>
                    </div>
                    <br>

                    <div class="odgovor-row">
                    <label for="odgovor4" class>Odgovor4: </label>
                    <input type="text" name="odgovor4" id="odgovor4_id" class="odgovor">
                    <label for="tocke_odgovor4" class="tocke_odgovor_label">Tocke</label>
                    <input type="number" name="tocke_odgovor4" id="tocke_odgovor4" class="tocke_odgovor">
                    <select name="pravilen4" id="pravilen4" class="pravilen-select">
                        <option value="0">False</option>
                        <option value="1">True</option>
                    </select>
                    </div>
                    <br>
                </div>
                <br>
                


                

                <label for ="kategorija" class="kategorija">Izberi za katero kategorijo je</label>
                    <select name="kategorije" id="kategorije">
                        <option value="A">Kategorija A (Motorji)</option>
                    <option value="B">Kategorija B (Avti)</option>
                    <option value="C">Kategorija C (Tovornjaki)</option>
                    <option value="D">Kategorija D (Avtobusi)</option>
                    <option value="Vse" selected>Vse kategorije</option>
</select>
<br>

                <label for="nalaganje_slik" class="nalaganje_slik">Nalozi sliko (ce je potrbno)</label><br>
                <input type="file" id="slika_id" name="slika" class="slika-btn" accept="image/*"> <br>'

                <input type="submit" value="Dodaj odgovore" class="submit-btn" name="sub">'
    
        </div>
    </form>
</body>
<footer>
    <?php
        include_once 'noga.php';
    ?>
</footer>
</html>