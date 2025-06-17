<?php 
include_once 'seja.php';
require_once "povezava.php";


$kategorija_id = isset($_GET['kategorija']) ? $_GET['kategorija'] : 0;

if ($kategorija_id > 0) {
    $vprasanja_sql = "SELECT v.vprasanja_id, v.vprasanje, v.tipi_vprasanja_id, v.tocke_vprasanja, 
                    s.slika, t.ime AS tip_vprasanja 
                FROM vprasanja v
                JOIN kategorije_vprasanja kv ON v.vprasanja_id = kv.vprasanja_id
                LEFT JOIN slike s ON v.vprasanja_id = s.vprasanja_id
                LEFT JOIN tipi_vprasanja t ON v.tipi_vprasanja_id = t.tipi_vprasanja_id
                WHERE kv.kategorije_id = $kategorija_id
                ORDER BY RAND()
                LIMIT 20";
    $vprasanja = mysqli_query($link, $vprasanja_sql);


    $_SESSION['idv'] = [];
    if ($vprasanja) {
        while ($row = mysqli_fetch_array($vprasanja)) {
            $_SESSION['idv'][] = $row['vprasanja_id'];
        }
        
        mysqli_data_seek($vprasanja, 0);
    }

    
} 
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="test.css">
    <title>Test</title>
</head>

<body>
<form action="rezultat.php?kategorija=<?php echo $kategorija_id; ?>" method="POST" class="uredi-form">

<?php
if (!empty($vprasanja)) {

    while ($vprasanje = mysqli_fetch_array($vprasanja)) {
        echo '<div class="vprasanje">';
        

        echo '<h3>' . $vprasanje['vprasanje'] . '</h3>';

        if ($vprasanje['slika']) {
            $slika = base64_encode($vprasanje['slika']);
            echo '<img src="data:image/png;base64,' . $slika . '" alt="Slika">';
        }

        $vprasanje_id = $vprasanje['vprasanja_id'];
        $tip_id = $vprasanje['tipi_vprasanja_id'];

        $odgovori_sql = "SELECT * FROM odgovori WHERE vprasanja_id = $vprasanje_id ORDER BY RAND()";
        $odgovori_result = mysqli_query($link, $odgovori_sql);
        $_SESSION['ido'][$vprasanje_id] = [];
        while ($odgovor = mysqli_fetch_array($odgovori_result)) {
            $_SESSION['ido'][$vprasanje_id][] = $odgovor;
        }

        foreach ($_SESSION['ido'][$vprasanje_id] as $odgovor) {
            echo '<div>';

            if ($tip_id == 1) {
                echo '<input type="radio" 
                    name="vprasanje_' . $vprasanje_id . '" 
                    value="' . $odgovor['odgovori_id'] . '" 
                    id="odgovor_' . $odgovor['odgovori_id'] . '">';
            } else if ($tip_id == 2) {
                echo '<input type="checkbox" 
                    name="vprasanje_' . $vprasanje_id . '[]" 
                    value="' . $odgovor['odgovori_id'] . '" 
                    id="odgovor_' . $odgovor['odgovori_id'] . '">';
            }

            echo '<label for="odgovor_' . $odgovor['odgovori_id'] . '">' . $odgovor['odgovor'] . '</label>';
            echo '</div>';
}

        echo '</div>';
    }

} else {
    echo '<p>Ni vprašanj za izbrano kategorijo.</p>';
}
?>

    <input type="submit" value="Oddaj test">
</form>
</body>
</html>