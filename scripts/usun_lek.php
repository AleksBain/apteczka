<?php
require_once __DIR__ . '/../baza.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['id_apteczki'])) {
    $inwentarz_id = (int) $_POST['id'];
    $apteczka_id = (int) $_POST['id_apteczki'];

    $stmt = $conn->prepare("DELETE FROM inwentarz WHERE inwentarz_id = ?");
    $stmt->bind_param("i", $inwentarz_id);

    if (!$stmt->execute()) {
        die("Błąd wykonania zapytania: " . $stmt->error);
    }

    $stmt->close();

    header("Location: /apteczka/views/edytuj_apteczke.php?id=" . $apteczka_id);
    exit();
} else {
    die("Nieprawidłowe dane lub metoda żądania.");
}
