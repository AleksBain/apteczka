<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Apteczka domowa</title>
    <style>
        .button-container {
            margin: 20px 0;
        }
        .main-button {
            display: inline-block;
            margin: 10px;
            padding: 15px 30px;
            font-size: 18px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 10px;
        }
        .main-button:hover {
            background-color: #45a049;
        }
        .notifications {
            margin-top: 30px;
            padding: 20px;
            border: 2px dashed #aaa;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    
<?php
include "scripts/nagl.php";


if (!isset($_SESSION['username'])) {
    echo "<h2>Zaloguj się lub zarejestruj</h2>";
    include "scripts/logowanie.php";
    include "scripts/rejestracja.php";
} else {
    echo "<h1>Witaj, " . htmlspecialchars($_SESSION['username']) . "!</h1>";
    echo "<p>Twoja rola to: " . $_SESSION['role'] . "</p>";
    echo "<p>Dzisiaj jest: " . date("Y-m-d H:i:s") . "</p>";
    echo '<p><a href="scripts/wylogowanie.php">Wyloguj się</a></p>';
    ?>

    <div class="button-container">
        <a class="main-button" href="leki.php">📦 Inwentarz</a>
        <a class="main-button" href="scripts/uzytkownicy.php">👥 Użytkownicy</a>
        <a class="main-button" href="wszystkie_leki.php">📋 Spis leków</a>
    </div>

    <div class="notifications">
        <h2>🔔 Powiadomienia</h2>
        <ul>
            <li>Lek *Ibuprofen* kończy się za 2 dni.</li>
            <li>Lek *Paracetamol* przekroczył datę ważności.</li>
            <!-- tutaj będą dynamiczne powiadomienia w przyszłości -->
        </ul>
    </div>

<?php } ?>

<?php include "scripts/stopka.php"; ?>
</body>
</html>
