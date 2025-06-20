<?php

include 'povezava.php';
include 'seja.php';


if (!isset($_SESSION['idu'])) {
    header("Location: prijava.php");
    exit();
}


$uporabnik_id = $_SESSION['idu'];

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
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php include_once 'glava.php'; ?>

    <h2 class="prejsni_testi">Prejšnji testi</h2>

    <a href="test.php?kategorija=<?php echo $kategorija_id; ?>" style="text-decoration: none;">
        <button class="resi_test">Reši test</button>
    </a>

    <table border="1" cellpadding="10" class="tabela-prejsnji-test">
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

            if($procent >= 88){
                $status = "Opravil";
            } else {
                $status = "Ni opravil";
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
    <?php include_once 'noga.php'; ?>
</footer>
</html>

