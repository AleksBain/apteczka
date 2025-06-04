<?php
require_once __DIR__ . '/../baza.php';

$apteczka_id = intval($_POST['id_apteczki']);
$inwentarz_id = intval($_POST['inwentarz_id']);
$ilosc = intval($_POST['ilosc']);

if ($inwentarz_id > 0 && $ilosc > 0) {
    // Pobierz aktualną ilość
    $stmt = $conn->prepare("SELECT ilosc FROM inwentarz WHERE inwentarz_id = ?");
    $stmt->bind_param("i", $inwentarz_id);
    $stmt->execute();
    $stmt->bind_result($aktualna);
    $stmt->fetch();
    $stmt->close();

    if ($aktualna >= $ilosc) {
        // Zmniejsz ilość
        $stmt = $conn->prepare("UPDATE inwentarz SET ilosc = ilosc - ? WHERE inwentarz_id = ?");
        $stmt->bind_param("ii", $ilosc, $inwentarz_id);
        $stmt->execute();
        $stmt->close();

        // (Opcjonalnie) loguj rozchód
        $stmt = $conn->prepare("
            INSERT INTO rozchody_lekow (inwentarz_id, typ, ilosc, data)
            VALUES (?, 'wydanie', ?, NOW())
        ");
        $stmt->bind_param("ii", $inwentarz_id, $ilosc);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: ../views/edytuj_apteczke.php?id=$apteczka_id");
exit;

?>

