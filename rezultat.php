<?php
require_once "povezava.php";
include_once "seja.php";

$vprasanja_ids = $_POST['vprasanja_ids'] ?? [];
$odgovor_id = $_POST['odgovori_id'] ?? [];

$kategorija_id = (int)$_GET['kategorija'];

if (!isset($_SESSION['idu'])) {
    die("Napaka: uporabnik ni prijavljen.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
    die("Napaka: ni podatkov.");
}

$id_uporabnika = (int)$_SESSION['idu'];
$dobljene_tocke = 0;
$pravilni_odgovori = 0;


$vstavi_test_sql = "INSERT INTO testi (datum_cas, uporabniki_id,kategorije_id) VALUES(NOW(), $id_uporabnika,$kategorija_id);";
$result = mysqli_query($link, $vstavi_test_sql);
$test_id = mysqli_insert_id($link);

//
foreach ($vprasanja_ids as $id_vprasanja) {
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

//
$max_tocke = 0;
foreach ($vprasanja_ids as $id_v) {
    $id_v = (int)$id_v;
    $tocke_sql = "SELECT tocke_vprasanja FROM vprasanja WHERE vprasanja_id = $id_v";
    $result = mysqli_query($link, $tocke_sql);
    if ($result) {
        $row = mysqli_fetch_array($result);
        $max_tocke += (float)$row['tocke_vprasanja'];
    }
}


foreach ($vprasanja_ids as $id_v) {
    $id_v = (int)$id_v;

    if (!isset($_POST["vprasanje_$id_v"])) {
        continue;
    }

    $odgovor = $_POST["vprasanje_$id_v"];
    $odgovori = is_array($odgovor) ? $odgovor : [$odgovor];

    $sql_odgovori = "SELECT odgovori_id, je_pravilen, odgovori_tocke 
                    FROM odgovori 
                    WHERE vprasanja_id = $id_v";
    $result = mysqli_query($link, $sql_odgovori);
    if (!$result) {
        continue;
    }

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

$procenti = $max_tocke > 0 ? round(($dobljene_tocke / $max_tocke) * 100) : 0;


?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezultat</title>
    <link rel="stylesheet" href="rezultat.css"> 
</head>
<body>
    <h1>Vaš rezultat: <?php echo $procenti; ?>%</h1>
    <p>Pravilno odgovorjenih vprašanj: <?php echo $pravilni_odgovori; ?> od <?php echo count($vprasanja_ids); ?></p>
    <p>Točke: <?php echo $dobljene_tocke; ?> / <?php echo $max_tocke; ?></p>
    <p>Kategorija: <?php $kategorija_id?></p>
    <form>
        <?php foreach ($vprasanja_ids as $id_v): ?>
            <?php
            $id_v = (int)$id_v;

            
            $vprasanje_sql = "SELECT v.vprasanje, t.ime AS tip_vprasanja 
                            FROM vprasanja v
                            LEFT JOIN tipi_vprasanja t ON v.tipi_vprasanja_id = t.tipi_vprasanja_id
                            WHERE v.vprasanja_id = $id_v";
            $vprasanje_result = mysqli_query($link, $vprasanje_sql);
            $vprasanje_data = mysqli_fetch_array($vprasanje_result);

            
            $odgovori_sql = "SELECT odgovori_id, odgovor, je_pravilen 
                            FROM odgovori 
                            WHERE vprasanja_id = $id_v";
            $odgovori_result = mysqli_query($link, $odgovori_sql);

    
            $izbrani_sql = "SELECT odgovori_id 
                            FROM odgovori_uporabnikov 
                            WHERE vprasanja_id = $id_v AND uporabniki_id = $id_uporabnika AND testi_id = $test_id";
            $izbrani_result = mysqli_query($link, $izbrani_sql);
            $izbrani_odgovori = [];
            while ($row = mysqli_fetch_array($izbrani_result)) {
                $izbrani_odgovori[] = $row['odgovori_id'];
            }
            ?>

            <div>
                <h3><?php echo htmlspecialchars($vprasanje_data['vprasanje']); ?></h3>
                <?php while ($odgovor = mysqli_fetch_array($odgovori_result)): ?>
                    <div>
                        <?php
                        $is_checked = in_array($odgovor['odgovori_id'], $izbrani_odgovori);
                        $class = '';
                        if ($is_checked && $odgovor['je_pravilen']) {
                            $class = 'pravilno'; 
                        } elseif ($is_checked && !$odgovor['je_pravilen']) {
                            $class = 'nepravilno'; 
                        } elseif (!$is_checked && $odgovor['je_pravilen']) {
                            $class = 'pravilen-odgovor'; 
                        }
                        ?>
                        <?php if ($vprasanje_data['tip_vprasanja'] == 1): ?>
                            
                            <input type="radio" 
                                name="vprasanje_<?php echo $id_v; ?>" 
                                value="<?php echo $odgovor['odgovori_id']; ?>" 
                                id="odgovor_<?php echo $odgovor['odgovori_id']; ?>" 
                                disabled <?php echo $is_checked ? 'checked' : ''; ?>>
                        <?php elseif ($vprasanje_data['tip_vprasanja'] == 2): ?>
                            
                            <input type="checkbox" 
                                name="vprasanje_<?php echo $id_v; ?>[]" 
                                value="<?php echo $odgovor['odgovori_id']; ?>" 
                                id="odgovor_<?php echo $odgovor['odgovori_id']; ?>" 
                                disabled <?php echo $is_checked ? 'checked' : ''; ?>>
                        <?php endif; ?>
                        <label for="odgovor_<?php echo $odgovor['odgovori_id']; ?>" class="<?php echo $class; ?>">
                            <?php echo htmlspecialchars($odgovor['odgovor']); ?>
                        </label>
                    </div>
                <?php endwhile; ?>

            
                <p class="pravilen-odgovor">
                    Pravilen odgovor: 
                    <?php
                    $pravilni_odgovori_sql = "SELECT odgovor 
                                            FROM odgovori 
                                            WHERE vprasanja_id = $id_v AND je_pravilen = 1";
                    $pravilni_odgovori_result = mysqli_query($link, $pravilni_odgovori_sql);
                    $pravilni_odgovori = [];
                    while ($row = mysqli_fetch_array($pravilni_odgovori_result)) {
                        $pravilni_odgovori[] = htmlspecialchars($row['odgovor']);
                    }
                    echo implode(', ', $pravilni_odgovori);
                    ?>
                </p>
            </div>
        <?php endforeach; ?>
    </form>

    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php" style="text-decoration: none;">
            <button type="button" style="padding: 10px 20px; background-color: #2196F3; color: white; border: none; border-radius: 5px; cursor: pointer;">Nazaj na izbiro kategorije</button>
        </a>
    </div>
</body>
</html>
