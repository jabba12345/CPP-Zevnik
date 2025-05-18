<?php
require_once "povezava.php";
include_once "seja.php";
include_once 'test.php';

// Preverjanje prijave
if (!isset($_SESSION['idu'])) {
    die("Napaka: uporabnik ni prijavljen.");
}

// Preverjanje metode in podatkov
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
    die("Napaka: ni podatkov.");
}

// Inicializacija
$id_uporabnika = (int)$_SESSION['idu'];
$kategorija_id = (int)($_POST['kategorija_id'] ?? 0);
$tocke_uporabnika = 0;
$pravilni_odgovori = 0;

// Ustvarimo nov test v bazi
$vstavi_test_sql = "INSERT INTO testi (datum_cas, uporabniki_id) VALUES(NOW(), $id_uporabnika)";
if (!mysqli_query($link, $vstavi_test_sql)) {
    die("Napaka pri shranjevanju testa: " . mysqli_error($link));
}
$test_id = mysqli_insert_id($link);

// Izračunaj skupne točke za test
$skupne_tocke = 0;
if (!empty($GLOBALS['vprasanja_ids'])) {
    $sql_max = "SELECT SUM(odgovori_tocke) as max FROM odgovori o 
            INNER JOIN vprasanja v ON o.vprasanja_id = v.vprasanja_id 
            WHERE o.je_pravilen = 1 AND v.vprasanja_id IN (" . implode(',', $GLOBALS['vprasanja_ids']) . ")";
    $result_max = mysqli_query($link, $sql_max);
    if ($result_max) {
        $row_max = mysqli_fetch_assoc($result_max);
        $skupne_tocke = (float)$row_max['max'];
    }
}

// Obdelava vsakega vprašanja
foreach ($GLOBALS['vprasanja_ids'] as $id_v) {
    $id_v = (int)$id_v;
    
    if (!isset($_POST["vprasanje_$id_v"])) {
        continue;
    }

    $odgovor = $_POST["vprasanje_$id_v"];
    $odgovori = is_array($odgovor) ? $odgovor : [$odgovor];
    
    // Pridobimo vse odgovore za to vprašanje
    $sql_odgovori = "SELECT odgovori_id, je_pravilen, odgovori_tocke 
                    FROM odgovori 
                    WHERE vprasanja_id = $id_v";
    $result = mysqli_query($link, $sql_odgovori);
    
    if (!$result) {
        continue;
    }

    $pravilni_ids = [];
    $vsi_odgovori = [];
    $max_tocke_vprasanja = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        $vsi_odgovori[$row['odgovori_id']] = $row;
        if ($row['je_pravilen']) {
            $pravilni_ids[] = $row['odgovori_id'];
            $max_tocke_vprasanja += $row['odgovori_tocke'];
        }
    }

    // Preverjanje pravilnosti odgovorov
    $dobljene_tocke = 0;
    $vse_pravilne = true;
    
    foreach ($odgovori as $odg) {
        $odg = (int)$odg;
        if (!isset($vsi_odgovori[$odg])) {
            $vse_pravilne = false;
            continue;
        }
        
        if ($vsi_odgovori[$odg]['je_pravilen']) {
            $dobljene_tocke += $vsi_odgovori[$odg]['odgovori_tocke'];
        } else {
            $vse_pravilne = false;
        }
    }

    // Dodajanje točk
    if ($vse_pravilne && count($odgovori) === count($pravilni_ids)) {
        $pravilni_odgovori++;
        $tocke_uporabnika += $max_tocke_vprasanja;
    } else {
        $tocke_uporabnika += $dobljene_tocke;
    }

    // Shranjevanje odgovorov
    foreach ($odgovori as $odg) {
        $odg = (int)$odg;
        $sql = "INSERT INTO odgovori_uporabnikov 
            (uporabniki_id, vprasanja_id, odgovori_id, testi_id) 
            VALUES ($id_uporabnika, $id_v, $odg, $test_id)";
        mysqli_query($link, $sql);
    }
}

// Preverjanje, ali stolpec `tocke` obstaja
$check_column_sql = "SHOW COLUMNS FROM testi LIKE 'tocke'";
$check_column_result = mysqli_query($link, $check_column_sql);

if (mysqli_num_rows($check_column_result) > 0) {
    // Če stolpec obstaja, posodobimo točke
    $sql_update = "UPDATE testi 
                SET tocke = $tocke_uporabnika 
                WHERE testi_id = $test_id AND uporabniki_id = $id_uporabnika";
    mysqli_query($link, $sql_update);
} else {
    // Če stolpec ne obstaja, preskočimo posodobitev
    error_log("Stolpec `tocke` ne obstaja v tabeli `testi`.");
}

// Izračun uspešnosti
$procent = $skupne_tocke > 0 ? round(($tocke_uporabnika / $skupne_tocke) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Rezultat testa</title>
    <link rel="stylesheet" href="rezultat.css">
</head>
<body>
    <h1>Rezultat testa</h1>
    <p>Skupaj točk: <?= $tocke_uporabnika ?> od <?= $skupne_tocke ?></p>
    <p>Uspešnost: <?= $procent ?>%</p>
    <p><?= $procent >= 88 ? "✅ Test si uspešno opravil!" : "❌ Test ni uspešno opravljen." ?></p>

    <hr>
    <h2>Podrobnosti po vprašanjih:</h2>
    <?php 
    foreach ($GLOBALS['vprasanja_ids'] as $vprasanje_id): 
        $vprasanje_id = (int)$vprasanje_id;
        
        // Pridobimo podatke o vprašanju
        $vprasanje_q = mysqli_query($link, "SELECT vprasanje FROM vprasanja WHERE vprasanja_id = $vprasanje_id");
        $vprasanje_data = mysqli_fetch_assoc($vprasanje_q);
        $vprasanje_txt = $vprasanje_data['vprasanje'] ?? 'Neznano vprašanje';
        
        // Pridobimo pravilne odgovore
        $pravilni_q = mysqli_query($link, "SELECT odgovori_id FROM odgovori WHERE vprasanja_id = $vprasanje_id AND je_pravilen = 1");
        $pravilni_ids = [];
        while ($row = mysqli_fetch_assoc($pravilni_q)) {
            $pravilni_ids[] = $row['odgovori_id'];
        }
        
        // Pridobimo uporabnikove odgovore
        $uporabnikovi_q = mysqli_query($link, "SELECT odgovori_id FROM odgovori_uporabnikov WHERE vprasanja_id = $vprasanje_id AND uporabniki_id = $id_uporabnika AND testi_id = $test_id");
        $uporabnikovi_ids = [];
        while ($row = mysqli_fetch_assoc($uporabnikovi_q)) {
            $uporabnikovi_ids[] = $row['odgovori_id'];
        }
    ?>
        <div class="vprasanje-box">
            <h3><?= htmlspecialchars($vprasanje_txt) ?></h3>
            <ul>
                <?php
                $odgovori_q = mysqli_query($link, "SELECT * FROM odgovori WHERE vprasanja_id = $vprasanje_id");
                while ($o = mysqli_fetch_assoc($odgovori_q)):
                    $id = $o['odgovori_id'];
                    $je_pravilen = in_array($id, $pravilni_ids);
                    $je_izbran = in_array($id, $uporabnikovi_ids);
                ?>
                    <li class="<?= $je_izbran ? ($je_pravilen ? 'pravilno' : 'nepravilno') : ($je_pravilen ? 'neizbrano' : '') ?>">
                        <?= htmlspecialchars($o['odgovor']) ?>
                        <?php if ($je_izbran && $je_pravilen): ?>
                            - ✅ Pravilen odgovor (izbran)
                        <?php elseif ($je_izbran && !$je_pravilen): ?>
                            - ❌ Napačen odgovor (izbran)
                        <?php elseif (!$je_izbran && $je_pravilen): ?>
                            - ℹ️ Pravilen odgovor (ni izbran)
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</body>
</html>