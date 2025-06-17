<?php 
require_once "povezava.php";
include_once 'seja.php';

$kategorija = 0;
if (isset($_GET['kategorija'])) {
    $kategorija = (int)$_GET['kategorija'];
}



?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Izbira kategorije vozila</title>
    <?php include_once 'glava.php';?>
</head>
<body>
    <div class="kategorija-h1">
        <h1>Izberite kategorijo vozila</h1>
    </div>
    
    <div class="kategorije-wrapper">
    <!--Kategorija A-->
    <div class="kategorija-container">
        <img src="Slike/motor.png" alt="Motor">
        <div><p>Kategorija A</p></div>
        <button type="button" onclick="location.href='prejsni_testi.php?kategorija=1'">Izberi</button>
    </div>
    
    <!--Kategorija B-->
    <div class="kategorija-container">
        <img src="Slike/avto.png" alt="Osebna vozila">
        <div><p>Kategorija B</p></div>
        <button type="button" onclick="location.href='prejsni_testi.php?kategorija=2'">Izberi</button>
    </div>
    
    <!--Kategorija C-->
    <div class="kategorija-container">
        <img src="Slike/tovornjak.png" alt="Tovorna vozila">
        <div><p>Kategorija C</p></div>
        <button type="button" onclick="location.href='prejsni_testi.php?kategorija=3'">Izberi</button>
    </div>
    
    <!--Kategorija D-->
    <div class="kategorija-container">
        <img src="Slike/bus-20.png" alt="Avtobusi">
        <div><p>Kategorija D</p></div>
        <button type="button" onclick="location.href='prejsni_testi.php?kategorija=4'">Izberi</button>
    </div> <br>
    

</div>


</body>
<footer>
    <?php
        include_once 'noga.php';
    ?>
</footer>
</html>