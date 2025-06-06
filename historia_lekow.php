<?php
require_once "baza.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Brak dostępu. Zaloguj się.");
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT rodzina_id, username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($rodzina_id, $user_name);
$stmt->fetch();
$stmt->close();

$family_ids = [];
$stmt = $conn->prepare("SELECT id, imie, relacja FROM czlonkowie_rodziny WHERE rodzina_id = ?");
$stmt->bind_param("i", $rodzina_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $family_ids[$row['id']] = ['imie' => $row['imie'], 'relacja' => $row['relacja']];
}
$stmt->close();

$family_placeholders = implode(',', array_fill(0, count($family_ids), '?'));
$family_ids_array = array_keys($family_ids);

$sql = "
    SELECT 
        rl.typ,
        rl.ilosc,
        rl.data,
        rl.osoba_typ,
        rl.osoba_id,
        produkty.nazwa_handlowa AS nazwa_leku
    FROM rozchody_lekow rl
    LEFT JOIN inwentarz inv ON rl.inwentarz_id = inv.inwentarz_id
    LEFT JOIN opakowania opa ON inv.opakowanie_id = opa.opakowanie_id
    LEFT JOIN produkty ON opa.medicine_id = produkty.medicine_id
    WHERE 
        (rl.osoba_typ = 'user' AND rl.osoba_id = ?)";

if (!empty($family_ids_array)) {
    $sql .= " OR (rl.osoba_typ = 'family' AND rl.osoba_id IN ($family_placeholders))";
}

$sql .= " ORDER BY rl.data DESC";

$stmt = $conn->prepare($sql);
$param_types = 'i' . str_repeat('i', count($family_ids_array));
$stmt->bind_param($param_types, $user_id, ...$family_ids_array);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Historia leków</title>
    <link rel="stylesheet" href="/apteczka/css/n.css">

    <style>
    html, body {
        height: 100%;
        margin: 0;
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .content {
        flex: 1;
    }

    footer {
        background-color: #f8f9fa;
        padding: 10px;
        text-align: center;
    }
</style>

</head>
<body>
    <?php include "scripts/pasek_nawigacyjny.php"; ?>
    <div class="container mt-5">
        <h2 class="mb-4">🕓 Historia rozchodów leków</h2>

        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Nazwa leku</th>
                    <th scope="col">Ilość</th>
                    <th scope="col">Typ</th>
                    <th scope="col">Data</th>
                    <th scope="col">Osoba</th>
                </tr>
            </thead>
            <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['nazwa_leku']) ?></td>
            <td><?= htmlspecialchars($row['ilosc']) ?></td>
            <td>
                <?= $row['typ'] === 'wydanie' ? '📤 Wydane' : '🗑️ Zutylizowane' ?>
            </td>
            <td><?= date('Y-m-d H:i', strtotime($row['data'])) ?></td>
            <td>
                <?php if ($row['osoba_typ'] === 'user' && $row['osoba_id'] == $user_id): ?>
                    👤 <?= htmlspecialchars($user_name) ?> (Ja)
                <?php elseif ($row['osoba_typ'] === 'family' && isset($family_ids[$row['osoba_id']])): ?>
                    👥 <?= htmlspecialchars($family_ids[$row['osoba_id']]['imie']) ?>
                    <?= $family_ids[$row['osoba_id']]['relacja'] ? '(' . htmlspecialchars($family_ids[$row['osoba_id']]['relacja']) . ')' : '' ?>
                <?php else: ?>
                    ❓ Nieznana osoba
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>
        </table>
    

        <footer>
            <?php include "scripts/stopka.php"; ?>
        </footer>
</body>
</html>
