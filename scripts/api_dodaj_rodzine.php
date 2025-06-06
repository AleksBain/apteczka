<?php
require_once __DIR__ . '/../baza.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Brak dostępu. Zaloguj się.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $imie = trim($_POST['imie']);
    $relacja = isset($_POST['relacja']) ? trim($_POST['relacja']) : null;

    if (empty($imie)) {
        die("Imię członka rodziny jest wymagane.");
    }

    $stmt = $conn->prepare("SELECT rodzina_id FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($rodzina_id);
    $stmt->fetch();
    $stmt->close();

    if (!$rodzina_id) {
        die("Nie znaleziono rodziny użytkownika.");
    }

    $stmt = $conn->prepare("INSERT INTO czlonkowie_rodziny (user_id, imie, relacja, rodzina_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $user_id, $imie, $relacja, $rodzina_id);

    if ($stmt->execute()) {
        header("Location: /apteczka/rodzina.php?status=sukces");
        exit;
    } else {
        echo "Błąd podczas zapisu: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Nieprawidłowe żądanie.";
}
?>
