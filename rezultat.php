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

// Ustvarimo nov test v bazi
$vstavi_test_sql = "INSERT INTO testi (datum_cas, uporabniki_id,kategorije_id) VALUES(NOW(), $id_uporabnika,$kategorija_id)";
if (!mysqli_query($link, $vstavi_test_sql)) {
    die("Napaka pri shranjevanju testa: " . mysqli_error($link));
}
$test_id = mysqli_insert_id($link);

// Izračun maksimalnega števila točk
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

// Obdelava vsakega vprašanja
foreach ($vprasanja_ids as $id_v) {
    $id_v = (int)$id_v;

    if (!isset($_POST["vprasanje_$id_v"])) {
        continue;
    }

    $odgovor = $_POST["vprasanje_$id_v"];
    $odgovori = is_array($odgovor) ? $odgovor : [$odgovor];

    // Pridobimo vse odgovore na to vprašanje
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

    // Preverimo, če so vsi označeni pravilni
    $vprasanje_pravilno = true;
    foreach ($odgovori as $odg_id) {
        $odg_id = (int)$odg_id;

        if (!isset($vsi_odgovori[$odg_id]) || !$vsi_odgovori[$odg_id]['je_pravilen']) {
            $vprasanje_pravilno = false;
        } else {
            $dobljene_tocke += (float)$vsi_odgovori[$odg_id]['odgovori_tocke'];
        }
    }

    // Preverimo, ali je kakšen pravilen odgovor izpuščen
    foreach ($pravilni_ids as $pravilen_id) {
        if (!in_array($pravilen_id, $odgovori)) {
            $vprasanje_pravilno = false;
        }
    }

    if ($vprasanje_pravilno) {
        $pravilni_odgovori++;
    }
}

// Izračun procenta
$procenti = $max_tocke > 0 ? round(($dobljene_tocke / $max_tocke) * 100) : 0;


?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezultat</title>
    <link rel="stylesheet" href="rezultat.css"> <!-- Povezava na CSS datoteko -->
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

            // Pridobimo podatke o vprašanju
            $vprasanje_sql = "SELECT v.vprasanje, t.ime AS tip_vprasanja 
                            FROM vprasanja v
                            LEFT JOIN tipi_vprasanja t ON v.tipi_vprasanja_id = t.tipi_vprasanja_id
                            WHERE v.vprasanja_id = $id_v";
            $vprasanje_result = mysqli_query($link, $vprasanje_sql);
            $vprasanje_data = mysqli_fetch_array($vprasanje_result);

            // Pridobimo odgovore za vprašanje
            $odgovori_sql = "SELECT odgovori_id, odgovor, je_pravilen 
                            FROM odgovori 
                            WHERE vprasanja_id = $id_v";
            $odgovori_result = mysqli_query($link, $odgovori_sql);

            // Pridobimo odgovore, ki jih je uporabnik izbral
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
                            $class = 'pravilno'; // Uporabnik je pravilno odgovoril
                        } elseif ($is_checked && !$odgovor['je_pravilen']) {
                            $class = 'nepravilno'; // Uporabnik je napačno odgovoril
                        } elseif (!$is_checked && $odgovor['je_pravilen']) {
                            $class = 'pravilen-odgovor'; // Prikaz pravilnega odgovora
                        }
                        ?>
                        <?php if ($vprasanje_data['tip_vprasanja'] == 1): ?>
                            <!-- Radio button za tip vprašanja 1 -->
                            <input type="radio" 
                                name="vprasanje_<?php echo $id_v; ?>" 
                                value="<?php echo $odgovor['odgovori_id']; ?>" 
                                id="odgovor_<?php echo $odgovor['odgovori_id']; ?>" 
                                disabled <?php echo $is_checked ? 'checked' : ''; ?>>
                        <?php elseif ($vprasanje_data['tip_vprasanja'] == 2): ?>
                            <!-- Checkbox za tip vprašanja 2 -->
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

                <!-- Prikaz pravilnih odgovorov -->
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

    <!-- Gumba za reši nov test in nazaj na izbiro kategorije -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="izbira_kategorije.php" style="text-decoration: none;">
            <button type="button" style="padding: 10px 20px; background-color: #2196F3; color: white; border: none; border-radius: 5px; cursor: pointer;">Nazaj na izbiro kategorije</button>
        </a>
    </div>
</body>
</html>
