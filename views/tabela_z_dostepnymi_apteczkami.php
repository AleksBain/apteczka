<?php

require_once __DIR__ . '/../baza.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id_apteczki, nazwa_apteczki, typ FROM apteczka WHERE wlasciciel_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Twoje apteczki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-..." crossorigin="anonymous">
    <link rel="stylesheet" href="/apteczka/css/n.css">
</head>
<body class="container mt-4">
    <h1 class="mb-4">📦 Twoje apteczki</h1>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nazwa</th>
                    <th>Typ</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['nazwa_apteczki']) ?></td>
                        <td><?= htmlspecialchars($row['typ']) ?></td>
                        <td>
                            <a href="/apteczka/views/edytuj_apteczke.php?id=<?= htmlspecialchars($row['id_apteczki']) ?>" class="btn btn-sm btn-warning">✏️ Edytuj</a>

                            <a href="/apteczka/scripts/usun_apteczke.php?id=<?= $row['id_apteczki'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tę apteczkę?')">🗑️ Usuń</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">😕 Nie masz jeszcze żadnych apteczek. Utwórz pierwszą!</div>
    <?php endif;

    $stmt->close();
    $conn->close();
    ?>
</body>
</html>