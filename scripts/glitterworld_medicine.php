<?php
session_start();
require_once "baza.php";
require_once "pasek_nawigacyjny.php";

// Dodanie nowego wpisu do apteczki
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj'])) {
    $opakowanie_id = $_POST['opakowanie_id'];
    $ilosc = $_POST['ilosc'];
    $cena = $_POST['cena'];
    $termin = $_POST['termin'];

    $stmt = $conn->prepare("INSERT INTO apteczka (opakowanie_id, ilosc, cena, termin_waznosci) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iids", $opakowanie_id, $ilosc, $cena, $termin);
    $stmt->execute();
}

// Rozchód
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rozchod'])) {
    $id = $_POST['rozchod_id'];
    $rozchod = $_POST['rozchod_ilosc'];
    $typ = $_POST['typ'] ?? 'użycie';

    $stmt = $conn->prepare("UPDATE apteczka SET ilosc = ilosc - ? WHERE id = ? AND ilosc >= ?");
    $stmt->bind_param("iii", $rozchod, $id, $rozchod);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<p style='color:green;'>Rozchód wykonany.</p>";

        // Pobierz opakowanie_id
        $stmt_op = $conn->prepare("SELECT opakowanie_id FROM apteczki WHERE id = ?");
        $stmt_op->bind_param("i", $id);
        $stmt_op->execute();
        $result = $stmt_op->get_result();
        $row = $result->fetch_assoc();
        $opakowanie_id = $row['opakowanie_id'];

        // Zapisz do tabeli rozchody
        $stmt_log = $conn->prepare("INSERT INTO rozchody (rozchody_apteczki_id, rozchody_opakowanie_id, ilosc, typ) VALUES (?, ?, ?, ?)");
        $stmt_log->bind_param("iiis", $id, $opakowanie_id, $rozchod, $typ);
        $stmt_log->execute();
    } else {
        echo "<p style='color:red;'>Nie można wykonać rozchodu (zbyt mała ilość).</p>";
    }
}

// Lista leków do wyboru
$produkty = $conn->query("
    SELECT o.opakowanie_id, p.nazwa_handlowa, o.opis 
    FROM produkty p
    JOIN opakowania o ON p.medicine_id = o.medicine_id
    ORDER BY p.nazwa_handlowa
");

// Zawartość apteczki
$apteczki = $conn->query("
    SELECT a.id, p.nazwa_handlowa, o.opis, a.ilosc, a.cena, a.termin_waznosci
    FROM apteczka a
    JOIN opakowania o ON a.opakowanie_id = o.opakowanie_id
    JOIN produkty p ON o.medicine_id = p.medicine_id
");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moja Apteczka</title>
    <link rel="stylesheet" href="/apteczka/css/n.css">
</head>
<body>

<div class="container">
    <h2>➕ Dodaj lek do apteczki</h2>
    <form method="post">
        <label for="opakowanie_id">Wybierz lek:</label>
        <input type="text" id="lek_szukaj" placeholder="Wpisz nazwę leku..." autocomplete="off">
        <input type="hidden" name="opakowanie_id" id="opakowanie_id">
        <div id="sugestie"></div>
        <?php while ($row = $produkty->fetch_assoc()): ?>
            <option value="<?= $row['opakowanie_id'] ?>">
                <?= htmlspecialchars($row['nazwa_handlowa']) ?> (<?= htmlspecialchars($row['opis']) ?>)
            </option>
        <?php endwhile; ?>

        <label>Ilość:</label>
        <input type="number" name="ilosc" min="1" required><br>

        <label>Cena (zł):</label>
        <input type="number" name="cena" step="0.01"><br>

        <label>Termin ważności:</label>
        <input type="date" name="termin"><br>

        <button type="submit" name="dodaj">💾 Dodaj</button>
    </form>

    <hr>

    <h2>📦 Zawartość apteczki</h2>
    <table>
        <thead>
            <tr>
                <th>Lek</th>
                <th>Opis opakowania</th>
                <th>Ilość</th>
                <th>Cena</th>
                <th>Termin ważności</th>
                <th>Rozchód</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($lek = $apteczka->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($lek['nazwa_handlowa']) ?></td>
                    <td><?= htmlspecialchars($lek['opis']) ?></td>
                    <td><?= $lek['ilosc'] ?></td>
                    <td><?= $lek['cena'] ?> zł</td>
                    <td><?= $lek['termin_waznosci'] ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="rozchod_id" value="<?= $lek['id'] ?>">
                            <input type="number" name="rozchod_ilosc" min="1" max="<?= $lek['ilosc'] ?>" required>
                            <select name="typ" required>
                                <option value="użycie">użycie</option>
                                <option value="utylizacja">utylizacja</option>
                            </select>
                            <button type="submit" name="rozchod">➖ Wydaj</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<script>
document.getElementById('lek_szukaj').addEventListener('input', function () {
    const zapytanie = this.value;
    if (zapytanie.length < 2) return;

    fetch('/apteczka/ajax_szukaj_leki.php?q=' + encodeURIComponent(zapytanie))
        .then(res => res.json())
        .then(data => {
            const sugestie = document.getElementById('sugestie');
            sugestie.innerHTML = '';
            data.forEach(lek => {
                const opcja = document.createElement('div');
                opcja.textContent = lek.nazwa;
                opcja.dataset.id = lek.opakowanie_id;
                opcja.addEventListener('click', () => {
                    document.getElementById('lek_szukaj').value = lek.nazwa;
                    document.getElementById('opakowanie_id').value = lek.opakowanie_id;
                    sugestie.innerHTML = '';
                });
                sugestie.appendChild(opcja);
            });
        });
});
</script>
