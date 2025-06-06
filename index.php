<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Apteczka domowa</title>
        <link rel="stylesheet" href="/apteczka/css/n.css">
    </head>
<body>
    <div class="wrapper">   
        <?php if (!isset($_SESSION['username'])) {
                    echo '<h2 style="text-align: center; margin-top: 40px;">Zaloguj się lub zarejestruj</h2>';
                        if (isset($_SESSION['error'])) {
                                echo '<div class="error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                                unset($_SESSION['error']);
                            }

            if (isset($_SESSION['success'])) {
                echo '<div class="success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
                }
        
            include "views/formularz_logowania.php";
            include "views/formularz_rejestracji.php";
            
                    } 
        else { ?>
            
               <?php include "scripts/pasek_nawigacyjny.php"; ?>

    <div class="content">
        <h1 style="text-align: center; margin-top: 40px;">
            Witaj, <?php echo htmlspecialchars($_SESSION['username']); ?>!
        </h1>

        <?php include "scripts/powiadomienia.php"; ?>
    </div> 

<?php } ?> 


    <?php include "scripts/stopka.php"; ?>


</div> 

</body>
</html>