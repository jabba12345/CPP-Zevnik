<?php

include 'povezava.php';
include 'seja.php';


if (!isset($_SESSION['idu'])) {
    header("Location: prijava.php");
    exit();
}


$uporabnik_id = $_SESSION['idu'];

// Preveri, če je poslana kategorija
if (isset($_GET['kategorija'])) {
    $kategorija_id = (int)$_GET['kategorija'];
} else {
    echo "Kategorija ni bila izbrana.";
    exit();
}


$poizvedba = "
SELECT 
    t.testi_id,
    t.datum_cas,
    SUM(odg.odgovori_tocke) AS dosezene_tocke,
    SUM(v.tocke_vprasanja) AS max_tocke
FROM odgovori_uporabnikov odgu
INNER JOIN vprasanja v ON odgu.vprasanja_id = v.vprasanja_id
INNER JOIN testi t ON odgu.testi_id = t.testi_id
INNER JOIN odgovori odg ON odgu.odgovori_id = odg.odgovori_id
WHERE t.uporabniki_id = $uporabnik_id AND t.kategorije_id = $kategorija_id
GROUP BY t.testi_id, t.datum_cas 
ORDER BY t.datum_cas DESC
LIMIT 5;
";

$rezultat = mysqli_query($link, $poizvedba);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Prejšnji testi</title>
    <link rel="stylesheet" href="prejsni_test.css"> 
    <button class="nazaj"onclick="location.href='index.php'">Nazaj</button>

</head>
<body>
  <?php include_once 'glava.php'; ?>
<h2>Prejšnji testi</h2>


<a href="test.php?kategorija=<?php echo $kategorija_id; ?>" style="text-decoration: none;">
    <button>Reši test</button>
</a>


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
        echo "<td>$status</td>";
        echo "</tr>";
    }
    ?>

</table>

</body>
<footer>
    <?php
        include_once 'noga.php';
    ?>
</footer>
</html>
