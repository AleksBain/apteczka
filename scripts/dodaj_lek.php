<?php
require_once __DIR__ . '/../baza.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['opakowanie_id'])) {
    $opakowanie_id = (int) $_POST['opakowanie_id'];
    $ilosc = (int) $_POST['ilosc'];
    $cena = $_POST['cena'] !== "" ? (float) $_POST['cena'] : null;
    $termin = $_POST['termin'];
    $apteczka_id = (int) $_POST['id_apteczki'];

    if (!$opakowanie_id || !$ilosc || !$termin || !$apteczka_id) {
        die("Brak wymaganych danych.");
    }

    $stmt = $conn->prepare("INSERT INTO inwentarz (opakowanie_id, apteczka_id, ilosc, cena, termin_waznosci) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiids", $opakowanie_id, $apteczka_id, $ilosc, $cena, $termin);
    if (!$stmt->execute()) {
    die("Błąd wykonania zapytania: " . $stmt->error);
}


    header("Location: /apteczka/views/edytuj_apteczke.php?id=" . $apteczka_id);
    exit();

}
?>
