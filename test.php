<?php 
require_once "povezava.php";
include_once 'seja.php';

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


    $vprasanja_ids = [];
    if ($vprasanja) {
        while ($row = mysqli_fetch_array($vprasanja)) {
            $vprasanja_ids[] = $row['vprasanja_id'];
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
<form action="rezultat.php?kategorija=<?php echo $kategorija_id; ?>" method="POST">

        <?php if (!empty($vprasanja)): ?>
            <?php while($vprasanje = mysqli_fetch_array($vprasanja)): ?>
    <div class="vprasanje">
        <input type="hidden" name="vprasanja_ids[]" value="<?php echo $vprasanje['vprasanja_id']; ?>">

        <h3><?php echo $vprasanje['vprasanje']; ?></h3>
        <?php if ($vprasanje['slika']): ?>
            <img src="data:image/png;base64,<?php echo base64_encode($vprasanje['slika']); ?>" alt="Slika">
        <?php endif; ?>

        <?php 
        $vprasanje_id = $vprasanje['vprasanja_id'];
        $tip_id = $vprasanje['tipi_vprasanja_id'];
        $odgovori_sql = "SELECT * FROM odgovori WHERE vprasanja_id = $vprasanje_id ORDER BY RAND()";
        $odgovori_result = mysqli_query($link, $odgovori_sql);
        $odgovori = [];
        while ($odgovor = mysqli_fetch_array($odgovori_result)) {
            $odgovori[] = $odgovor;
        }
        ?>

        <?php foreach($odgovori as $odgovor): ?>
    
            <input type="hidden" name="odgovori_id[]" value="<?php echo $odgovor['odgovori_id']; ?>">
            <div>
                <?php if ($tip_id == 1): ?>
                    <input type="radio" 
                        name="vprasanje_<?php echo $vprasanje_id; ?>" 
                        value="<?php echo $odgovor['odgovori_id']; ?>" 
                        id="odgovor_<?php echo $odgovor['odgovori_id']; ?>">
                <?php elseif ($tip_id == 2): ?>
                    <input type="checkbox" 
                        name="vprasanje_<?php echo $vprasanje_id; ?>[]" 
                        value="<?php echo $odgovor['odgovori_id']; ?>" 
                        id="odgovor_<?php echo $odgovor['odgovori_id']; ?>">
                <?php endif; ?>
                <label for="odgovor_<?php echo $odgovor['odgovori_id']; ?>">
                    <?php echo $odgovor['odgovor']; ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
<?php endwhile; ?>

        <?php else: ?>
            <p>Ni vprašanj za izbrano kategorijo.</p>
        <?php endif; ?>

        
        <input type="submit" value="Oddaj test">
    </form>
</body>
</html>
