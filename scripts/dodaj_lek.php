<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj'])) {
    $opakowanie_id = $_POST['opakowanie_id'];
    $ilosc = $_POST['ilosc'];
    $cena = $_POST['cena'];
    $termin = $_POST['termin'];
    $apteczka_id = $_POST['apteczka_id']; // <-- NOWA WYMAGANA WARTOŚĆ

    $stmt = $conn->prepare("INSERT INTO inwentarz (inwentarz_apteczki_id, inwentarz_opakowanie_id, ilosc, cena, termin_waznosci) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiids", $apteczka_id, $opakowanie_id, $ilosc, $cena, $termin);
    $stmt->execute();
}
?>
