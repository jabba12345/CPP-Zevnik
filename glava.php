<?php
include_once 'seja.php';

if (!isset($_SESSION['idu'])) {
    header("Location: prijava.php");
    exit(); 
}
?>

<style>
    .display_ime {
        position: absolute;
        top: 10px;
        left: 20px;
        color: black;
        font-weight: bold;
        font-size: 16px;
    }

    .odjava {
        position: absolute;
        top: 10px;
        right: 20px; /* Moves the button to the top-right corner */
    }

    .odjava button {
        background-color: #ff4444;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .odjava button:hover {
        background-color: #cc0000;
    }
</style>


<div>
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
