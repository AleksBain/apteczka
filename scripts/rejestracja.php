<?php
include "baza.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($username) || empty($password) || empty($email)) {
        echo "Wszystkie pola są wymagane.";
        exit;
    }

    // Sprawdź, czy użytkownik już istnieje
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "Taki użytkownik już istnieje.";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Domyślna rola: pending (lub np. 'child' jeśli wolisz)
    $default_role = 'pending';

    $stmt = $conn->prepare("INSERT INTO users (username, password, role, email, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssss", $username, $hashed_password, $default_role, $email);

    if ($stmt->execute()) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $default_role;
        header("Location: ../index.php");
        exit;
    } else {
        echo "Błąd: " . $stmt->error;
    }
}
?>

<!-- Formularz rejestracyjny -->
<form action="scripts/rejestracja.php" method="POST">
    <label for="username">Nazwa użytkownika:</label>
    <input type="text" name="username" id="username" required><br>

    <label for="password">Hasło:</label>
    <input type="password" name="password" id="password" required><br>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required><br>

    <input type="submit" value="Zarejestruj">
</form>
