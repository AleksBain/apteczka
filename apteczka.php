<?php
session_start();
require_once "baza.php";
require_once "scripts/dodaj_lek.php";
require_once "scripts/wykonaj_rozchod.php";
require_once "scripts/pobierz_dane.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj'])) {
    $opakowanie_id = $_POST['opakowanie_id'];
    $ilosc = $_POST['ilosc'];
    $cena = $_POST['cena'];
    $termin = $_POST['termin'];

    $stmt = $conn->prepare("INSERT INTO inwentarz (inwentarz_opakowanie_id, ilosc, cena, termin_waznosci) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iids", $opakowanie_id, $ilosc, $cena, $termin);
    $stmt->execute();

    header("Location: apteczka.php");
    exit();

}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Twoja apteczka</title>
    <link rel="stylesheet" href="/apteczka/css/n.css">
</head>
<body>
<div class="wrapper">
    <?php include "scripts/nagl.php"; ?>

    <?php if (!isset($_SESSION['username'])) {
        echo "<h2>Musisz być zalogowany, aby zobaczyć swoją apteczkę</h2>";
        include "scripts/logowanie.php";
    } else { ?>
    
        <?php include "scripts/pasek_nawigacyjny.php"; ?>
        <button onclick="document.getElementById('dodaj-lek-form').style.display='block'">➕ Dodaj lek</button>
        <div id="dodaj-lek-form" style="display: none; margin-top: 20px;">
        <button onclick="document.getElementById('dodaj-lek-form').style.display='none'" style="float:right;">❌ Zamknij</button>

        <h3>Dodaj nowy lek do apteczki</h3>
        
        <form method="POST" action="">
        <label for="szukaj_leku">Nazwa leku:</label>
        <input type="text" id="szukaj_leku" placeholder="Wpisz np. ibuprom...">
    <div id="suggestions" style="border: 1px solid #ccc; max-height: 150px;overflow-y: auto;"></div>
    
        <label for="opakowanie_id">ID opakowania:</label>
        <input type="number" name="opakowanie_id" required><br>

        <label for="ilosc">Ilość:</label>
        <input type="number" name="ilosc" required><br>

        <label for="cena">Cena (PLN):</label>
        <input type="number" step="0.01" name="cena" required><br>

        <label for="termin">Termin ważności:</label>
        <input type="date" name="termin" required><br>

        <button type="submit" name="dodaj">💾 Zapisz</button>
    </form>
</div>

        <div class="content">
            <h1>📦 Twoja domowa apteczka</h1>

            <?php include "views/tabela_apteczki.php"; ?>
        </div>

    <?php } ?>

    <?php include "scripts/stopka.php"; ?>
</div>
</body>
</html>
<script>
document.getElementById("szukaj_leku").addEventListener("input", function() {
    const query = this.value;
    if (query.length < 2) return;

    fetch(`/apteczka/scripts/ajax_szukaj_leki.php?q=` + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById("suggestions");
            container.innerHTML = "";
            data.forEach(item => {
                const div = document.createElement("div");
                div.style.cursor = "pointer";
                div.style.padding = "4px";
                div.textContent = item.nazwa;

                div.onclick = () => {
                    document.querySelector("input[name='opakowanie_id']").value = item.opakowanie_id;
                    document.getElementById("szukaj_leku").value = item.nazwa;
                    container.innerHTML = "";
                };

                container.appendChild(div);
            });
        });
});

</script>
