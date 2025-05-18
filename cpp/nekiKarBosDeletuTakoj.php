<?php
require_once "povezava.php";
include_once 'seja.php';

$specific_slika_id = 18; // Your specific image ID

$vprasanja_sql = "SELECT v.*, s.slika FROM vprasanja v
        LEFT JOIN slike s ON v.slike_id = s.slike_id AND s.slike_id = $specific_slika_id
        WHERE v.slike_id = $specific_slika_id
        LIMIT 1";

$result = mysqli_query($link, $vprasanja_sql);
$vprasanje = mysqli_fetch_assoc($result);

if($vprasanje && !empty($vprasanje['slika'])) {
    echo '<img src="data:image/jpeg;base64,'.base64_encode($vprasanje['slika']).'" alt="Specific Image">';
    echo '<h3>'.htmlspecialchars($vprasanje['vprasanje']).'</h3>';
}
?>