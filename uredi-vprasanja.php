<?php
include_once 'seja.php';
include_once 'povezava.php';

$vprasanje_id = $_POST['vprasanja_id'];

$vprasanje_sql = "SELECT v.vprasanje,v.tocke_vprasanja FROM vprasanja v WHERE v.vprasanja_id = $vprasanje_id";
$result_vprasanja = mysqli_query($link, $vprasanje_sql);
$row = mysqli_fetch_array($result_vprasanja);
$vprasanje = $row['vprasanje'];
$tocke_vprasanja= $row['tocke_vprasanja'];

$odgovori_sql = "SELECT o.odgovori_id, o.odgovor,o.odgovori_tocke,o.je_pravilen FROM odgovori o WHERE o.vprasanja_id = $vprasanje_id";
$result_odgovori = mysqli_query($link, $odgovori_sql);
$row_odgovori = mysqli_fetch_array($result_odgovori);
$je_pravilen= $row_odgovori['je_pravilen'];

$slika_sql = "SELECT s.slike_id, s.slika FROM slike s WHERE s.vprasanja_id = $vprasanje_id";
$result_slika = mysqli_query($link, $slika_sql);
$row_slika = mysqli_fetch_array($result_slika);


$kategorije_sql = "SELECT k.kategorije_id FROM kategorije_vprasanja k WHERE k.vprasanja_id = $vprasanje_id";
$result_kategorije = mysqli_query($link, $kategorije_sql);

$kategorija_sql="SELECT k.kategorije_id,k.ime FROM kategorije k";
$result_kategorija = mysqli_query($link, $kategorija_sql);



if(isset($row_slika)){
$slika = $row_slika['slika'];
}

if (isset($_POST["sub"])) {
    $odgovori = $_POST['odgovori'];
    $odgovori_id = $_POST['odgovori_id'];
    $vprasanje = $_POST['vprasanje'];

    $update_vprasanje_sql = "UPDATE vprasanja SET vprasanje = '$vprasanje' WHERE vprasanja_id = $vprasanje_id";
    mysqli_query($link, $update_vprasanje_sql);

    for ($i = 0; $i < count($odgovori); $i++) {
        $odgovor = mysqli_real_escape_string($link, $odgovori[$i]);
        $odgovori_tocke= $_POST['tocke_odgovora'][$i];
        $odg_je_pravilen = $_POST['je_pravilen'][$i];
        $id = intval($odgovori_id[$i]);
        $update_odgovor_sql = "UPDATE odgovori SET odgovor = '$odgovor' WHERE odgovori_id = $id";
        mysqli_query($link, $update_odgovor_sql);

        $update_odgovor_sql = "UPDATE odgovori SET odgovori_tocke = '$odgovori_tocke' WHERE odgovori_id = $id";
        mysqli_query($link, $update_odgovor_sql);

        $update_odgovor_sql = "UPDATE odgovori SET je_pravilen = '$odg_je_pravilen' WHERE odgovori_id = $id";
        mysqli_query($link, $update_odgovor_sql);
    }

    if ($_FILES['slika'] && $_FILES['slika']['error'] === UPLOAD_ERR_OK) {
    $slika = file_get_contents($_FILES['slika']['tmp_name']);
    $slika= mysqli_real_escape_string($link, $slika);

    if (isset($row_slika)) {
        $slika_sql = "UPDATE slike SET slika = '$slika' WHERE vprasanja_id = $vprasanje_id";
        mysqli_query($link, $slika_sql);
    } else {
        $slika_sql = "INSERT INTO slike (slike,vprasanja_id) VALUES ('$slika',$vprasanje_id)";
        mysqli_query($link, $slika_sql);
    }
    
}


    header("Location: brisanje_vprasanj.php");
    exit;


}

?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <?php include_once 'glava.php'?>
    <title>Urejanje Vprašanj</title>
</head>
<body>
    
    <form action="uredi-vprasanja.php" method="post" enctype="multipart/form-data" class="uredi-form">
        <input type="hidden" name="vprasanja_id" value="<?= intval($vprasanje_id)?>">

        <label for="vprasanje">Vprašanje:</label>
        <input type="text" name="vprasanje" value="<?php echo htmlspecialchars($vprasanje) ?>">

        <label for="tocke_vprasanje">Točke</label>
        <input type="text" name="tocke_vprasanja" value="<?php echo intval($tocke_vprasanja)?>">

        <?php
        foreach ($result_odgovori as $odgovor) {
            echo "<div class='odgovor'>";
            echo "<label class='uredi-label'>Odgovor:</label>";
            echo "<input type='hidden' name='odgovori_id[]' class='uredi-input' value='" . intval($odgovor['odgovori_id'])."'>";
            echo "<input type='text' name='odgovori[]' class='uredi-input' value='" . htmlspecialchars($odgovor['odgovor'])."'>";

            echo "<label class='uredi-label'>Točke odgovora:</label>";
            echo "<input type='text' name='tocke_odgovora[]' class='uredi-input' value='" . intval($odgovor['odgovori_tocke'])."'>";

            echo "<label class='uredi-label'>Je pravilen:</label>";
            echo "<select name='je_pravilen[]' class='uredi-select'>";
            if($odgovor['je_pravilen'] == 1) {
                echo "<option value='1' selected>Da</option>";
                echo "<option value='0'>Ne</option>";
            } else {
                echo "<option value='1'>Da</option>";
                echo "<option value='0' selected>Ne</option>";
            }
            echo "</select>";
            
            echo "</div>";
        }
        ?>

<label for="kategorije" class="uredi-label">Kategorije:</label>
<select name="kategorije" id="kategorija_id"class="uredi-select">
    <?php
        foreach($result_kategorija as $kategorija){
            echo "<option value='" . intval($kategorija['kategorije_id']) . "'>" . htmlspecialchars($kategorija['ime']) . "</option>";
        }
    ?>
</select>

        <label for="slika" class="uredi-label">Trenutna slika:</label>
        <img class="uredi-img"src="data:image/png;base64,<?php echo base64_encode($slika); ?>" alt="Slika" style="max-width: 200px; max-height: 200px;">
        <label for="slika" class="uredi-label">Spremeni sliko</label>
        <input type="file" name="slika" class="uredi-input">
        
        <button class="uredi-btn" type="submit" name="sub">Shrani spremembe</button>
    </form>
</body>
<footer>
    <?php
        include_once 'noga.php';
    ?>
</footer>
</html>
