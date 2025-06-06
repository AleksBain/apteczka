<?php
require_once "baza.php";
require_once "scripts/dodaj_lek.php";
require_once "scripts/dodaj_apteczke.php";
require_once "scripts/pobierz_dane.php";
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Twoje apteczki</title>
    <link rel="stylesheet" href="/apteczka/css/n.css">
</head>
<body>

    <?php include "scripts/pasek_nawigacyjny.php"; ?>

    <div class="wrapper">
        <div class="content">

            <div class="actions">
                <a href="/apteczka/views/formularz_utworz_apteczke.php" class="btn btn-primary">
                    ➕ Utwórz apteczkę
                </a>
            </div>

            <?php include "views/tabela_z_dostepnymi_apteczkami.php"; ?>

        </div>

        <?php include "scripts/stopka.php"; ?>
    </div>

</body>
</html>
