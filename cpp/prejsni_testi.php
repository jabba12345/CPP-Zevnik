<?php
// Vključi datoteke za povezavo z bazo in sejo
include 'povezava.php';
include 'seja.php';

// Preveri, če je uporabnik prijavljen
if (!isset($_SESSION['idu'])) {
    header("Location: prijava.php");
    exit();
}

// Dobi ID uporabnika iz seje
$uporabnik_id = $_SESSION['idu'];

// Preveri, če je poslana kategorija
if (isset($_GET['kategorija'])) {
    $kategorija_id = (int)$_GET['kategorija'];
} else {
    echo "Kategorija ni bila izbrana.";
    exit();
}

// Pripravi poizvedbo za iskanje prejšnjih testov tega uporabnika za to kategorijo
$poizvedba = "
    SELECT 
        testi.testi_id,
        testi.datum_cas,
        SUM(odgovori.odgovori_tocke) AS dosezene_tocke,
        SUM(vprasanja.tocke_vprasanja) AS max_tocke
    FROM testi
    JOIN odgovori_uporabnikov ON testi.testi_id = odgovori_uporabnikov.testi_id
    JOIN odgovori ON odgovori_uporabnikov.odgovori_id = odgovori.odgovori_id
    JOIN vprasanja ON odgovori_uporabnikov.vprasanja_id = vprasanja.vprasanja_id
    JOIN kategorije_vprasanja ON vprasanja.vprasanja_id = kategorije_vprasanja.vprasanja_id
    WHERE testi.uporabniki_id = $uporabnik_id AND kategorije_vprasanja.kategorije_id = $kategorija_id
    GROUP BY testi.testi_id, testi.datum_cas
    ORDER BY testi.datum_cas DESC
    LIMIT 5;
";

$rezultat = mysqli_query($link, $poizvedba);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Prejšnji testi</title>
    <link rel="stylesheet" href="prejsni_testi.css"> <!-- Povezava na CSS datoteko -->
</head>
<body>

<h2>Prejšnji testi</h2>

<!-- Gumb za reševanje novega testa -->
<a href="test.php?kategorija=<?php echo $kategorija_id; ?>" style="text-decoration: none;">
    <button>Reši test</button>
</a>

<!-- Tabela z rezultati -->
<table border="1" cellpadding="10">
    <tr>
        <th>Datum</th>
        <th>Točke</th>
        <th>Procent</th>
        <th>Status</th>
    </tr>

    <?php
    while ($vrstica = mysqli_fetch_array($rezultat)) {
        $datum = $vrstica['datum_cas'];
        $tocke = $vrstica['dosezene_tocke'];
        $max_tocke = $vrstica['max_tocke'];
        $procent = 0;

        if ($max_tocke > 0) {
            $procent = ($tocke / $max_tocke) * 100;
        }

        if($procent>=88){
            $status_class="status-opravil";
        }else{
            $status_class="status-ni-opravil";
        }

        if($procent>=88){
            $status="Opravil";
        }else{
            $status="Ni opravil";
        }

        

        echo "<tr>";
        echo "<td>$datum</td>";
        echo "<td>" . round($tocke, 2) . " / " . round($max_tocke, 2) . "</td>";
        echo "<td>" . round($procent, 2) . "%</td>";
        echo "<td class='$status_class'>$status</td>";
        echo "</tr>";
    }
    ?>

</table>

</body>
</html>
