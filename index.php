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
    <?php include "scripts/nagl.php"; ?>

    <?php if (!isset($_SESSION['username'])) {
        echo "<h2>Zaloguj się lub zarejestruj</h2>";
        include "scripts/logowanie.php";
        include "scripts/rejestracja.php";
    } else { ?>
        
        <?php include "scripts/pasek_nawigacyjny.php"; ?>

        <div class="content">
            <h1>Witaj, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

            <div class="notifications">
                <h2>🔔 Powiadomienia</h2>
                <ul>
                    <li>Lek <strong>Ibuprofen</strong> kończy się za 2 dni.</li>
                    <li>Lek <strong>Paracetamol</strong> przekroczył datę ważności.</li>
                </ul>
            </div>
        </div>
</div>
<?php } ?>

<?php include "scripts/stopka.php"; ?>
</body>

