<?php
require_once __DIR__ . '/../baza.php';

$id = intval($_POST['id']);
$ilosc = intval($_POST['ilosc']);

if ($id > 0 && $ilosc > 0) {
    // Pobierz aktualną ilość
    $stmt = $conn->prepare("SELECT ilosc FROM leki WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($aktualna);
    $stmt->fetch();
    $stmt->close();

    if ($aktualna >= $ilosc) {
        // Zmniejsz ilość
        $stmt = $conn->prepare("UPDATE leki SET ilosc = ilosc - ? WHERE id = ?");
        $stmt->bind_param("ii", $ilosc, $id);
        $stmt->execute();
        $stmt->close();

        // Zaloguj operację (opcjonalnie)
        $stmt = $conn->prepare("INSERT INTO rozchody_lekow (id_leku, typ, ilosc, data) VALUES (?, 'wydanie', ?, NOW())");
        $stmt->bind_param("ii", $id, $ilosc);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: ../strony/apteczka.php");
exit;
?>
