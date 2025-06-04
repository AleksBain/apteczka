<?php
require_once __DIR__ . '/../baza.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Brak prawidłowego ID.");
}

// Najpierw usuń rekordy z inwentarza powiązane z tą apteczką
$stmt1 = $conn->prepare("DELETE FROM inwentarz WHERE apteczka_id = ?");
$stmt1->bind_param("i", $id);
$stmt1->execute();
$stmt1->close();

// Teraz usuń samą apteczkę
$stmt2 = $conn->prepare("DELETE FROM apteczka WHERE id_apteczki = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$stmt2->close();

header("Location: /apteczka/apteczka.php");
exit;
