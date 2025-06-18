<?php
include_once "seja.php";
require_once "povezava.php";

if (!isset($_SESSION['idu'])) {
    die("Napaka: uporabnik ni prijavljen.");
}

if (!isset($_SESSION['idv']) || !is_array($_SESSION['idv']) || count($_SESSION['idv']) === 0) { 
    die("Napaka: ni vprašanj za obdelavo.");
}

if (isset($_GET['kategorija'])) {
    $kategorija_id = (int)$_GET['kategorija'];
} else {
    $kategorija_id = 0;
}

$id_uporabnika = (int)$_SESSION['idu'];
$dobljene_tocke = 0;
$pravilni_odgovori = 0;


$vstavi_test_sql = "INSERT INTO testi (datum_cas, uporabniki_id, kategorije_id) VALUES (NOW(), $id_uporabnika, $kategorija_id)";
mysqli_query($link, $vstavi_test_sql);
$test_id = mysqli_insert_id($link);


foreach ($_SESSION['idv'] as $id_vprasanja) {
    if (!isset($_POST["vprasanje_$id_vprasanja"])) {
        continue; 
    }

    $izbrani_odgovori = $_POST["vprasanje_$id_vprasanja"];
    if (!is_array($izbrani_odgovori)) {
        $izbrani_odgovori = [$izbrani_odgovori];
    }

    foreach ($izbrani_odgovori as $odgovor_id) {
        $id_v = (int)$id_vprasanja;
        $od_v = (int)$odgovor_id;

        
        $sql = "SELECT 1 FROM odgovori WHERE vprasanja_id = $id_v AND odgovori_id = $od_v";
        $result = mysqli_query($link, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $vstavi_odgovor = "INSERT INTO odgovori_uporabnikov (testi_id, vprasanja_id, odgovori_id, uporabniki_id)
                            VALUES ($test_id, $id_v, $od_v, $id_uporabnika)";
            mysqli_query($link, $vstavi_odgovor);
        }
    }
}


$max_tocke = 0;
foreach ($_SESSION['idv'] as $id_v) {
    $id_v = (int)$id_v;
    $tocke_sql = "SELECT tocke_vprasanja FROM vprasanja WHERE vprasanja_id = $id_v";
    $result = mysqli_query($link, $tocke_sql);
    if ($result && $row = mysqli_fetch_array($result)) {
        $max_tocke += (float)$row['tocke_vprasanja'];
    }
}


foreach ($_SESSION['idv'] as $id_v) {
    $id_v = (int)$id_v;

    if (!isset($_POST["vprasanje_$id_v"])) {
        continue;
    }

    $odgovor = $_POST["vprasanje_$id_v"];

    if (is_array($odgovor)) {
        $odgovori = $odgovor;
    } else {
        $odgovori = array($odgovor);
    }



    $sql_odgovori = "SELECT odgovori_id, je_pravilen, odgovori_tocke FROM odgovori WHERE vprasanja_id = $id_v";
    $result = mysqli_query($link, $sql_odgovori);
    if (!$result) continue;

    $pravilni_ids = [];
    $vsi_odgovori = [];
    while ($row = mysqli_fetch_array($result)) {
        $vsi_odgovori[$row['odgovori_id']] = $row;
        if ($row['je_pravilen']) {
            $pravilni_ids[] = $row['odgovori_id'];
        }
    }


    $vprasanje_pravilno = true;
    foreach ($odgovori as $odg_id) {
        $odg_id = (int)$odg_id;

        if (!isset($vsi_odgovori[$odg_id]) || !$vsi_odgovori[$odg_id]['je_pravilen']) {
            $vprasanje_pravilno = false;
        } else {
            $dobljene_tocke += (float)$vsi_odgovori[$odg_id]['odgovori_tocke'];
        }
    }


    foreach ($pravilni_ids as $pravilen_id) {
        if (!in_array($pravilen_id, $odgovori)) {
            $vprasanje_pravilno = false;
        }
    }

    if ($vprasanje_pravilno) {
        $pravilni_odgovori++;
    }
}

if ($max_tocke > 0) {
    $procenti = round(($dobljene_tocke / $max_tocke) * 100);
} else {
    $procenti = 0;
}

?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Rezultat</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<h1 class="rezulat-h1">Vaš rezultat: <?php echo $procenti; ?>%</h1>
<p class="rezultat-p">Pravilno odgovorjenih vprašanj: <?php echo $pravilni_odgovori; ?> od <?php echo count($_SESSION['idv']); ?></p>
<p class="rezultat-p">Točke: <?php echo $dobljene_tocke; ?> / <?php echo $max_tocke; ?></p>
<p class="rezultat-p">Kategorija: <?php echo htmlspecialchars($kategorija_id); ?></p>

<form class="rezultat-form">
<?php
for ($i = 0; $i < count($_SESSION['idv']); $i++) {
    $id_v = (int)$_SESSION['idv'][$i];

    $vprasanje_sql = "SELECT v.vprasanje, t.ime AS tip_vprasanja FROM vprasanja v
                    LEFT JOIN tipi_vprasanja t ON v.tipi_vprasanja_id = t.tipi_vprasanja_id
                    WHERE v.vprasanja_id = $id_v";
    $vprasanje_result = mysqli_query($link, $vprasanje_sql);
    $vprasanje_data = mysqli_fetch_array($vprasanje_result);

    $odgovori_sql = "SELECT odgovori_id, odgovor, je_pravilen FROM odgovori WHERE vprasanja_id = $id_v";
    $odgovori_result = mysqli_query($link, $odgovori_sql);

    $izbrani_sql = "SELECT odgovori_id FROM odgovori_uporabnikov 
                    WHERE vprasanja_id = $id_v AND uporabniki_id = $id_uporabnika AND testi_id = $test_id";
    $izbrani_result = mysqli_query($link, $izbrani_sql);
    $izbrani_odgovori = [];
    while ($row = mysqli_fetch_array($izbrani_result)) {
        $izbrani_odgovori[] = $row['odgovori_id'];
    }

    echo '<div class="rezultat-div">';
    echo '<h3 class="rezultat-h3">' . htmlspecialchars($vprasanje_data['vprasanje']) . '</h3>';

    while ($odgovor = mysqli_fetch_array($odgovori_result)) {
        $is_checked = in_array($odgovor['odgovori_id'], $izbrani_odgovori);
        $class = '';

        if ($is_checked && $odgovor['je_pravilen']) {
            $class = 'pravilno';
        } elseif ($is_checked && !$odgovor['je_pravilen']) {
            $class = 'nepravilno';
        } elseif (!$is_checked && $odgovor['je_pravilen']) {
            $class = 'pravilen-odgovor';
        }

        if ($is_checked) {
            $checked = 'checked';
        } else {
            $checked = '';
        }

        if ($vprasanje_data['tip_vprasanja'] == 1) {
            $tip = 'radio';
        } else {
            $tip = 'checkbox';
        }

        if ($tip == 'radio') {
            $name = "vprasanje_" . $id_v;
        } else {
            $name = "vprasanje_" . $id_v . "[]";
        }
        $id = $odgovor['odgovori_id'];
        $odgovor_text = htmlspecialchars($odgovor['odgovor']);

        echo '<div class="rezultat-div">';
        echo '<input type="' . $tip . '" name="' . $name . '" value="' . $id . '" id="odgovor_' . $id . '" class="rezultat-input" disabled ' . $checked . '>';
        echo '<label class="rezulat-label ' . $class . '" for="odgovor_' . $id . '">' . $odgovor_text . '</label>';

        echo '</div>';
    }

    
    $pravilni_odgovori_sql = "SELECT odgovor FROM odgovori WHERE vprasanja_id = $id_v AND je_pravilen = 1";
    $pravilni_odgovori_result = mysqli_query($link, $pravilni_odgovori_sql);
    $pravilni_odgovori = [];
    while ($row = mysqli_fetch_array($pravilni_odgovori_result)) {
        $pravilni_odgovori[] = htmlspecialchars($row['odgovor']);
    }
    echo '<p class="pravilen-odgovor">Pravilen odgovor: ' . implode(', ', $pravilni_odgovori) . '</p>';
    echo '</div>';
}
?>
</form>

<div style="text-align: center; margin-top: 20px;">
    <a href="index.php" style="text-decoration: none;">
        <button class="rezultat-btn" type="button" style="padding: 10px 20px; background-color: #2196F3; color: white; border: none; border-radius: 5px; cursor: pointer;">Nazaj na izbiro kategorije</button>
    </a>
</div>

</body>
</html>

