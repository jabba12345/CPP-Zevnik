<?php
require_once "povezava.php";
include_once "seja.php";


$vprasanja_sql = "SELECT v.vprasanja_id, v.vprasanje FROM vprasanja v";
$vprasanja_result = mysqli_query($link, $vprasanja_sql);

if (isset($_POST['zbrisi_vse'])) {
    $sql = "DELETE FROM odgovori_uporabnikov";
    $result = mysqli_query($link, $sql);

    $sql = "DELETE FROM slike";
    $result = mysqli_query($link, $sql);

    $sql = "DELETE FROM odgovori";
    $result = mysqli_query($link, $sql);

    $sql = "DELETE FROM kategorije_vprasanja";
    $result = mysqli_query($link, $sql);

    $sql = "DELETE FROM vprasanja";
    $result = mysqli_query($link, $sql);

    $sql = "ALTER TABLE slike AUTO_INCREMENT = 1";
    $result = mysqli_query($link, $sql);

    $sql = "ALTER TABLE odgovori AUTO_INCREMENT = 1";
    $result = mysqli_query($link, $sql);

    $sql = "ALTER TABLE kategorije_vprasanja AUTO_INCREMENT = 1";
    $result = mysqli_query($link, $sql);

    $sql = "ALTER TABLE vprasanja AUTO_INCREMENT = 1";
    $result = mysqli_query($link, $sql);

    header("Location: brisanje_vprasanj.php");
    exit;
}

if (isset($_POST['zbrisi'])) {
    $vprasanja_id = intval($_POST['vprasanja_id']);

    $sql = "DELETE FROM slike WHERE vprasanja_id = $vprasanja_id";
    $result=mysqli_query($link, $sql);

    $sql = "DELETE FROM odgovori WHERE vprasanja_id = $vprasanja_id";
    $result=mysqli_query($link, $sql);

    $sql = "DELETE FROM kategorije_vprasanja WHERE vprasanja_id = $vprasanja_id";
    $result=mysqli_query($link, $sql);

    $sql = "DELETE FROM vprasanja WHERE vprasanja_id = $vprasanja_id";
    $result=mysqli_query($link, $sql);

    header("Location: brisanje_vprasanj.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Brisanje vprašanj</title>
    
</head>
<body>
    <?php include_once 'glava.php'; 

        $uporabnik_id = $_SESSION['idu'];

$sql = "SELECT * FROM uporabniki WHERE uporabniki_id = $uporabnik_id AND tip_uporabnika_id = 1";
$admin_result = mysqli_query($link, $sql);
if (mysqli_num_rows($admin_result) == 0) {
    header("Location: index.php");
    exit(); 
}
    ?>
    <div class="brisanje_container">
        <h2 class="brisanje_naslov">Brisanje vprašanj</h2>
        <div class="gumb-brisanje-vse">
            <form method="post" action="brisanje_vprasanj.php">
                <input type="submit" name="zbrisi_vse" value="Zbriši vsa vprašanja">
            </form>
        </div>
        <div class="search">
            <input type="text" name="search" placeholder="Poišči vprasanje">
        </div>
        <div class="tabela-container">
            <table class="tabela-vprasanja">
                <tr>
                    <th class="brisanje_th">ID vprašanja</th>
                    <th class="brisanje_th">Vprašanje</th>
                    <th class="brisanje_th">Izbriši vprašanje</th>
                    <th class="brisanje_th">Urejanje Vprasanj</th>
                </tr>
                <?php
            
            if ($vprasanja_result && mysqli_num_rows($vprasanja_result) > 0) {
                while ($row = mysqli_fetch_array($vprasanja_result)) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['vprasanja_id']) . "</td>
                        <td>" . htmlspecialchars($row['vprasanje']) . "</td>
                    <td>
                        <form method='post' action='' class='gumb-brisanje-posamezno'>
                            <input type='hidden' name='vprasanja_id' value='" . htmlspecialchars($row['vprasanja_id']) . "'>
                            <input type='submit' name='zbrisi' value='Zbriši'>
                        </form>
                    </td>

                    <td>
                        <form method='post' action='uredi-vprasanja.php' class='gumb-urejanje-posamezno'>
                            <input type='hidden' name='vprasanja_id' value='" . htmlspecialchars($row['vprasanja_id']) . "'>
                            <input type='submit' name='Uredi' value='Uredi' style='background-color: #47e1f6;'>
                        </form>
                    </td>
            </tr>";
                }
            }
?>

            </table>
        </div>
    </div>
</body>
<footer>
    <?php
        include_once 'noga.php';
    ?>
</footer>
</html>