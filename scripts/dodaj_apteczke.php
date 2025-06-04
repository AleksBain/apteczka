<?php
require_once __DIR__ . '/../baza.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_apteczke'])) {
    $nazwa = $_POST['nazwa_apteczki'];
    $typ = $_POST['typ_apteczki'];
    $wlasciciel_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO apteczka (wlasciciel_id, nazwa_apteczki, typ) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Błąd przygotowania zapytania: " . $conn->error);
    }

    $stmt->bind_param("iss", $wlasciciel_id, $nazwa, $typ);

    if ($stmt->execute()) {
        header("Location: /apteczka/apteczka.php");
        exit();
    } else {
        die("Błąd wykonania zapytania: " . $stmt->error);
    }
}
?>
