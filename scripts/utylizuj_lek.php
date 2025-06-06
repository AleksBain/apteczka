<?php
require_once __DIR__ . '/../baza.php';
session_start(); 

$apteczka_id = intval($_POST['id_apteczki']);
$inwentarz_id = intval($_POST['inwentarz_id']);
$ilosc = intval($_POST['ilosc']);
$osoba_id = $_SESSION['user_id'] ?? null; 

if ($inwentarz_id > 0 && $ilosc > 0 && $osoba_id !== null) {
    $stmt = $conn->prepare("SELECT ilosc FROM inwentarz WHERE inwentarz_id = ?");
    $stmt->bind_param("i", $inwentarz_id);
    $stmt->execute();
    $stmt->bind_result($aktualna);
    $stmt->fetch();
    $stmt->close();

    if ($aktualna >= $ilosc) {
        $stmt = $conn->prepare("UPDATE inwentarz SET ilosc = ilosc - ? WHERE inwentarz_id = ?");
        $stmt->bind_param("ii", $ilosc, $inwentarz_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("
            INSERT INTO rozchody_lekow (inwentarz_id, typ, ilosc, data, osoba_typ, osoba_id)
            VALUES (?, 'utylizacja', ?, NOW(), 'user', ?)
        ");
        $stmt->bind_param("iii", $inwentarz_id, $ilosc, $osoba_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: ../views/edytuj_apteczke.php?id=$apteczka_id");
exit;
?>
