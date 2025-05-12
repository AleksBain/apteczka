<?php
session_start();
require_once "baza.php";
require_once "pasek_nawigacyjny.php";

// Paginacja: ile rekordów na stronę?
$limit = 50;

// Oblicz numer strony
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max($page, 1); // zabezpieczenie

$offset = ($page - 1) * $limit;

// Liczba wszystkich rekordów
$total_sql = "SELECT COUNT(*) as total FROM produkty p JOIN opakowania o ON p.medicine_id = o.medicine_id";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Zapytanie z LIMIT i OFFSET
$sql = "SELECT 
            p.nazwa_handlowa,
            p.substancja_czynna,
            p.postac_farmaceutyczna,
            p.url_ulotka,
            o.kod_kreskowy,
            o.opis
        FROM produkty p
        JOIN opakowania o ON p.medicine_id = o.medicine_id
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

// Wyszukiwanie wszystkiego 

$q = $_GET['q'] ?? '';
$param = '%' . $q . '%';

$stmt = null;

if (!empty($q)) {
    $stmt = $conn->prepare("
        SELECT 
            p.nazwa_handlowa,
            p.substancja_czynna,
            p.postac_farmaceutyczna,
            p.url_ulotka,
            o.kod_kreskowy,
            o.opis
        FROM produkty p
        JOIN opakowania o ON p.medicine_id = o.medicine_id
        WHERE 
            p.nazwa_handlowa LIKE ? OR
            p.substancja_czynna LIKE ? OR
            o.kod_kreskowy LIKE ? OR
            o.opis LIKE ?
        LIMIT 100
    ");
    $stmt->bind_param("ssss", $param, $param, $param, $param);
} else {
    $stmt = $conn->prepare("
        SELECT 
            p.nazwa_handlowa,
            p.substancja_czynna,
            p.postac_farmaceutyczna,
            p.url_ulotka,
            o.kod_kreskowy,
            o.opis
        FROM produkty p
        JOIN opakowania o ON p.medicine_id = o.medicine_id
        LIMIT 100
    ");
}

$stmt->execute();
$result = $stmt->get_result();

?>


<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Spis leków</title>
    <link rel="stylesheet" href="/apteczka/css/n.css">
</head>
<body>

<div class="container">
    <h2>📋 Spis leków (strona <?= $page ?> z <?= $total_pages ?>)</h2>

    <form method="GET" action="">
        <input type="text" name="q" placeholder="Szukaj leku..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button type="submit">🔍 Szukaj</button>
    </form>


    <table>
        <thead>
            <tr>
                <th>Nazwa handlowa</th>
                <th>Substancja czynna</th>
                <th>Postać</th>
                <th>Opis opakowania</th>
                <th>Kod kreskowy</th>
                <th>Ulotka</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nazwa_handlowa']) ?></td>
                        <td><?= htmlspecialchars($row['substancja_czynna']) ?></td>
                        <td><?= htmlspecialchars($row['postac_farmaceutyczna']) ?></td>
                        <td><?= htmlspecialchars($row['opis']) ?></td>
                        <td><?= htmlspecialchars($row['kod_kreskowy']) ?></td>
                        <td>
                            <?php if (!empty($row['url_ulotka'])): ?>
                                <a href="<?= htmlspecialchars($row['url_ulotka']) ?>" target="_blank">🔗 Zobacz</a>
                            <?php else: ?>
                                Brak
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">Brak danych do wyświetlenia</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">← Poprzednia</a>
        <?php endif; ?>
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>">Następna →</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
