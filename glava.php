<?php
include_once 'seja.php';

if (!isset($_SESSION['idu'])) {
    header("Location: prijava.php");
    exit(); 
}
?>

<div>
    <link rel="stylesheet" href="glava.css">
    <?php 

    if(isset($_SESSION['idu'])) {
        $ime = $_SESSION['name']; 
		
        if (!empty($ime)): ?>
            <div class="display_ime"><?php echo htmlspecialchars($ime); ?></div>
			<div class="odjava">
                <a href="odjava.php">
                    <button type="button">Odjava</button>
                </a>
			
            </div>
		
        <?php endif;
		
    } 
    ?>
</div>
