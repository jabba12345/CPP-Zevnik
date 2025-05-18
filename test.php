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

    // Create an array to store the IDs of the questions
    $vprasanja_ids = [];
    if ($vprasanja) {
        while ($row = mysqli_fetch_array($vprasanja)) {
            $vprasanja_ids[] = $row['vprasanja_id'];
        }
        // Reset the pointer of the result set
        mysqli_data_seek($vprasanja, 0);
    }
    // Make $vprasanja_ids globally accessible
    
} 
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test</title>
    <style>
        /* Splošni stil za telo */
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }

        /* Glavni naslov */
        h1, h3 {
            text-align: center;
            color: #4CAF50;
            margin-top: 20px;
        }

        /* Kontejner za vprašanja */
        form {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Vsako vprašanje */
        .vprasanje {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        
        /* Naslov vprašanja */
        .vprasanje h3 {
            margin: 0 0 10px;
            color: #333;
        }

        /* Slike */
        img {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Radio in checkbox */
        input[type="radio"],
        input[type="checkbox"] {
            margin-right: 10px;
        }

        /* Oznake za odgovore */
        label {
            font-size: 1em;
            cursor: pointer;
            display: inline-block;
            margin-bottom: 5px;
        }

        /* Gumb za oddajo */
        input[type="submit"] {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s, transform 0.2s;
            display: block;
            width: 100%;
        }

        input[type="submit"]:hover {
            background: #45a049;
            transform: scale(1.02);
        }

        /* Sporočilo, če ni vprašanj */
        p {
            text-align: center;
            font-size: 1.2em;
            color: #F44336;
            margin-top: 20px;
        }
    </style>
</head>

<body>
<form method="post" action="rezultat.php">

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
            <!-- Shrani ID odgovora -->
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
