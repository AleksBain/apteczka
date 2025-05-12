<?php
session_start();
require_once "baza.php";
require_once "pasek_nawigacyjny.php";

// Obsługa dodania nowego wpisu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj'])) {
    $opakowanie_id = $_POST['opakowanie_id'];
    $ilosc = $_POST['ilosc'];
    $cena = $_POST['cena'];
    $termin = $_POST['termin'];

    $stmt = $conn->prepare("INSERT INTO inwentarz (opakowanie_id, ilosc, cena, termin_waznosci) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iids", $opakowanie_id, $ilosc, $cena, $termin);
    $stmt->execute();
}

// Obsługa rozchodu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rozchod'])) {
    $id = $_POST['rozchod_id'];
    $rozchod = $_POST['rozchod_ilosc'];
    $typ = $_POST['typ'] ?? 'użycie';

    // zmniejsz ilość
    $stmt = $conn->prepare("UPDATE apteczka SET ilosc = ilosc - ? WHERE id = ? AND ilosc >= ?");
    $stmt->bind_param("iii", $rozchod, $id, $rozchod);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<p style='color:green;'>Rozchód wykonany.</p>";

        // Pobierz opakowanie_id z rekordu apteczki
        $stmt_op = $conn->prepare("SELECT opakowanie_id FROM apteczka WHERE id = ?");
        $stmt_op->bind_param("i", $id);
        $stmt_op->execute();
        $result = $stmt_op->get_result();
        $row = $result->fetch_assoc();
        $opakowanie_id = $row['opakowanie_id'];

        // Zapisz rozchód do tabeli rozchody
        $stmt_log = $conn->prepare("INSERT INTO rozchody (rozchody_apteczki_id, rozchody_opakowanie_id, ilosc, typ) VALUES (?, ?, ?, ?)");
        $stmt_log->bind_param("iiis", $id, $opakowanie_id, $rozchod, $typ);
        $stmt_log->execute();
    } else {
        echo "<p style='color:red;'>Nie można wykonać rozchodu (zbyt mała ilość).</p>";
    }
}

// Pobierz leki słownikowe do listy wyboru
$produkty = $conn->query("
    SELECT o.opakowanie_id, p.nazwa_handlowa, o.opis 
    FROM produkty p
    JOIN opakowania o ON p.medicine_id = o.medicine_id
    ORDER BY p.nazwa_handlowa
    LIMIT 10
");

// Pobierz zawartość apteczki
$apteczka = $conn->query("
    SELECT i.inwentarz_id, p.nazwa_handlowa, o.opis, i.ilosc, i.cena, i.termin_waznosci
    FROM inwentarz i
    JOIN opakowania o ON i.inwentarz_opakowanie_id = o.opakowanie_id
    JOIN produkty p ON o.medicine_id = p.medicine_id
    LIMIT 10
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
        <select name="opakowanie_id" required>
            <?php while ($row = $produkty->fetch_assoc()): ?>
                <option value="<?= $row['opakowanie_id'] ?>">
                    <?= htmlspecialchars($row['nazwa_handlowa']) ?> (<?= htmlspecialchars($row['opis']) ?>)
                </option>
            <?php endwhile; ?>
        </select><br>

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
                            <input type="hidden" name="rozchod_id" value="<?= $lek['inwentarz_id'] ?>">
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
