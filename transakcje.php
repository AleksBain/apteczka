<?php
require_once "baza.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Brak dostępu. Zaloguj się.");
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT rodzina_id FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($rodzina_id);
$stmt->fetch();
$stmt->close();

$apteczki_id = [];
$stmt = $conn->prepare("SELECT id_apteczki FROM apteczka WHERE wlasciciel_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $apteczki_id[] = $row['id_apteczki'];
}
$stmt->close();


$liczba_specyfikow = 0;
if (!empty($apteczki_id)) {
    $placeholders = implode(',', array_fill(0, count($apteczki_id), '?'));

    $sql = "
        SELECT COUNT(DISTINCT produkty.medicine_id) AS liczba_specyfikow
        FROM inwentarz
        JOIN opakowania ON inwentarz.opakowanie_id = opakowania.opakowanie_id
        JOIN produkty ON opakowania.medicine_id = produkty.medicine_id
        WHERE inwentarz.apteczka_id IN ($placeholders)
    ";
    
    $stmt = $conn->prepare($sql);

    $types = str_repeat('i', count($apteczki_id));
    $stmt->bind_param($types, ...$apteczki_id);

    $stmt->execute();
    $stmt->bind_result($liczba_specyfikow);
    $stmt->fetch();
    $stmt->close();
}

$koszty = ['utylizacja' => 0, 'wydanie' => 0];
if (isset($_GET['start'], $_GET['end'])) {
    $start = $_GET['start'];
    $end = $_GET['end'];

    $sql = "
        SELECT rl.typ, rl.ilosc, inv.cena
        FROM rozchody_lekow rl
        JOIN inwentarz inv ON rl.inwentarz_id = inv.inwentarz_id
        WHERE rl.data BETWEEN ? AND ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        if (in_array($row['typ'], ['utylizacja', 'wydanie'])) {
            $koszty[$row['typ']] += $row['ilosc'] * $row['cena'];
        }
    }
    $stmt->close();
}

$leki = $conn->query("SELECT DISTINCT produkty.medicine_id, produkty.nazwa_handlowa
    FROM produkty
    JOIN opakowania ON produkty.medicine_id = opakowania.medicine_id
    JOIN inwentarz ON opakowania.opakowanie_id = inwentarz.opakowanie_id
    WHERE inwentarz.apteczka_id = $rodzina_id
    ORDER BY nazwa_handlowa ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Raporty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include "scripts/pasek_nawigacyjny.php"; ?>
<div class="container mt-4">
    <h2 class="mb-4">📊 Raporty z apteczki</h2>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">📦 Liczba różnych specyfików: <strong><?= $liczba_specyfikow ?></strong></h5>

       
        <?php if (!empty($historia_leku)): ?>
            <table class="table mt-3">
                <thead><tr><th>Nazwa</th><th>Typ</th><th>Ilość</th><th>Data</th></tr></thead>
                <tbody>
                    <?php foreach ($historia_leku as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nazwa_handlowa']) ?></td>
                            <td><?= $row['typ'] === 'wydanie' ? '📤 Wydanie' : '🗑️ Utylizacja' ?></td>
                            <td><?= $row['ilosc'] ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($row['data'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>


    <div class="card mb-4">
        <div class="card-body">
            <form method="get">
                <h5 class="card-title">💸 Raport kosztów</h5>
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="date" name="start" class="form-control" value="<?= $_GET['start'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-5">
                        <input type="date" name="end" class="form-control" value="<?= $_GET['end'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success w-100">Filtruj</button>
                    </div>
                </div>
            </form>
            <?php if (isset($_GET['start'], $_GET['end'])): ?>
                <ul class="mt-3 list-group">
                    <li class="list-group-item">🗑️ Utylizacja: <strong><?= number_format($koszty['utylizacja'], 2) ?> zł</strong></li>
                    <li class="list-group-item">📤 Wydanie: <strong><?= number_format($koszty['wydanie'], 2) ?> zł</strong></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
