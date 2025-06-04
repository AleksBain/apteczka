<?php
session_start();
require_once __DIR__ . '/../baza.php';


$user_id = $_SESSION['user_id'];
$apteczka_id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM apteczka WHERE id_apteczki = ? AND wlasciciel_id = ?");
$stmt->bind_param("ii", $apteczka_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows === 0) {
    // Nie masz dostępu do tej apteczki!
    die("❌ Brak dostępu. Ta apteczka nie należy do Ciebie.");
}

$apteczka = $result->fetch_assoc();

if (!isset($_GET['id'])) {
    die("Brak ID apteczki.");
}
$apteczka_id = (int) $_GET['id'];

// Pobieranie nazwy apteczki
$stmt = $conn->prepare("SELECT nazwa_apteczki FROM apteczka WHERE id_apteczki = ?");
$stmt->bind_param("i", $apteczka_id);
$stmt->execute();
$stmt->bind_result($nazwa_apteczki);
$stmt->fetch();
$stmt->close();

// Pobieranie leków
$sql = "SELECT 
            i.inwentarz_id,
            i.apteczka_id,
            p.nazwa_handlowa, 
            p.substancja_czynna, 
            p.moc, 
            p.postac_farmaceutyczna, 
            i.ilosc, 
            i.cena, 
            i.termin_waznosci, 
            i.opisut
        FROM inwentarz i
        JOIN opakowania o ON i.opakowanie_id = o.opakowanie_id
        JOIN produkty p ON o.medicine_id = p.medicine_id
        WHERE i.apteczka_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $apteczka_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edytuj apteczkę</title>
    <link rel="stylesheet" href="/apteczka/css/n.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>✏️ Edytuj apteczkę: <?= htmlspecialchars($nazwa_apteczki) ?></h2>
    <button id="toggleFormBtn" class="btn btn-success mb-3">➕ Dodaj lek</button>

<form id="dodajLekForm" method="POST" action="/apteczka/scripts/dodaj_lek.php" class="row g-3" style="display: none;">
    <input type="hidden" name="id_apteczki" value="<?= $apteczka_id ?>">
    <input type="hidden" name="opakowanie_id" id="opakowanie_id">

    <div class="col-md-4 position-relative">
        <input type="text" name="nazwa" id="nazwa_input" class="form-control" placeholder="Nazwa leku" autocomplete="off" required>
        <ul id="suggestions" class="list-group position-absolute w-100 z-3"></ul>
    </div>
    <div class="col-md-2">
        <input type="text" name="dawka" id="dawka_input" class="form-control" placeholder="Dawka" readonly>
    </div>
    <div class="col-md-2">
        <input type="text" name="jednostka" id="jednostka_input" class="form-control" placeholder="Postać" readonly>
    </div>
    <div class="col-md-2">
        <input type="number" name="ilosc" class="form-control" placeholder="Ilość" required min="1">
    </div>
    <div class="col-md-2">
        <input type="number" step="0.01" name="cena" class="form-control" placeholder="Cena (opcjonalnie)">
    </div>
    <div class="col-md-3">
        <input type="date" name="termin" class="form-control" placeholder="Termin ważności" required>
    </div>
    <div class="col-md-2">
        <button type="submit" name="dodaj" class="btn btn-primary">💾 Zapisz</button>
    </div>
</form>


    <table class="table table-hover mt-4">
    <thead>
        <tr>
            <th>Nazwa handlowa</th>
            <th>Substancja czynna</th>
            <th>Moc</th>
            <th>Postać</th>
            <th>Ilość</th>
            <th>Cena</th>
            <th>Termin ważności</th>
            
            <th>Akcja</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nazwa_handlowa']) ?></td>
                <td><?= htmlspecialchars($row['substancja_czynna']) ?></td>
                <td><?= htmlspecialchars($row['moc']) ?></td>
                <td><?= htmlspecialchars($row['postac_farmaceutyczna']) ?></td>
                <td><?= htmlspecialchars($row['ilosc']) ?></td>
                <td><?= htmlspecialchars($row['cena']) ?> zł</td>
                <td><?= htmlspecialchars($row['termin_waznosci']) ?></td>
                

              <td style="white-space: nowrap; text-align: center; vertical-align: middle;">
    <form action="../scripts/usun_lek.php" method="POST" style="display: inline-block; margin: 0; padding: 0;">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <input type="hidden" name="id_apteczki" value="<?= $apteczka_id ?>">
        <button type="submit"
                class="btn btn-danger btn-sm"
                onclick="return confirm('Usunąć lek?')"
                style="padding: 2px 6px; font-size: 0.875rem; line-height: 1; border-radius: 4px;">
            🗑️ Usuń
        </button>
    </form>
</td>
<td style="white-space: nowrap; text-align: center;">
    <!-- Wydaj -->
    <form action="../scripts/wydaj_lek.php" method="POST" style="display: inline-block;">
    <input type="hidden" name="inwentarz_id" value="<?= $row['inwentarz_id'] ?>">
    <input type="hidden" name="id_apteczki" value="<?= $row['apteczka_id'] ?>">
    <input type="number" name="ilosc" min="1" max="<?= $row['ilosc'] ?>" placeholder="Ilość" required style="width: 60px;">
    <button type="submit" class="btn btn-success btn-sm">Wydaj</button>
</form>


    <!-- Utylizuj -->
    <form action="../scripts/utylizuj_lek.php" method="POST" style="display: inline-block;">
        <input type="hidden" name="inwentarz_id" value="<?= $row['inwentarz_id'] ?>">
        <input type="hidden" name="id_apteczki" value="<?= $row['apteczka_id'] ?>">
        <input type="number" name="ilosc" min="1" max="<?= $row['ilosc'] ?>" placeholder="Ilość" required style="width: 60px;">
        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Utylizować lek?')">Utylizuj</button>
    </form>
</td>



            </tr>
        <?php endwhile; ?>
    </tbody>
</table>


    <a href="/apteczka/apteczka.php" class="btn btn-secondary mt-3">← Powrót do apteczek</a>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("nazwa_input");
    const suggestions = document.getElementById("suggestions");

    input.addEventListener("input", function () {
        const q = this.value;
        if (q.length < 2) {
            suggestions.innerHTML = "";
            return;
        }

        fetch("/apteczka/scripts/ajax_szukaj_leki.php?q=" + encodeURIComponent(q))
            .then(res => res.json())
            .then(data => {
                suggestions.innerHTML = "";
                data.forEach(item => {
                    const li = document.createElement("li");
                    li.classList.add("list-group-item", "list-group-item-action");
                    li.textContent = `${item.nazwa_handlowa} (${item.opis})`;
                    li.addEventListener("click", function () {
                        input.value = item.nazwa_handlowa;
                        document.getElementById("dawka_input").value = item.moc;
                        document.getElementById("jednostka_input").value = item.postac_farmaceutyczna;
                        document.getElementById("opakowanie_id").value = item.opakowanie_id;
                        suggestions.innerHTML = "";
                    });
                    suggestions.appendChild(li);
                });
            });
    });

    // Ukryj listę po kliknięciu poza pole
    document.addEventListener("click", function (e) {
        if (!suggestions.contains(e.target) && e.target !== input) {
            suggestions.innerHTML = "";
        }
    });

    // Obsługa pokazywania formularza
    const toggleBtn = document.getElementById("toggleFormBtn");
    const form = document.getElementById("dodajLekForm");
    toggleBtn.addEventListener("click", function () {
        form.style.display = form.style.display === "none" ? "flex" : "none";
    });
});
</script>


</body>
</html>
