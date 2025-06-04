<?php
require_once __DIR__ . '/../baza.php';
session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    die("Brak dostępu. Zaloguj się.");
}

// Walidacja danych z formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $imie = trim($_POST['imie']);
    $relacja = isset($_POST['relacja']) ? trim($_POST['relacja']) : null;

    if (empty($imie)) {
        die("Imię członka rodziny jest wymagane.");
    }

    // Przygotowanie zapytania SQL
    $stmt = $conn->prepare("INSERT INTO czlonkowie_rodziny (user_id, imie, relacja) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $imie, $relacja);

    if ($stmt->execute()) {
        // Sukces – przekieruj np. z komunikatem
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
