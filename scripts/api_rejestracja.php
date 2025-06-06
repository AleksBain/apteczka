<?php
session_start();
require '../baza.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = $_POST['email'];

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Uzupełnij wszystkie pola!";
        header("Location: ../index.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Użytkownik już istnieje!";
        header("Location: ../index.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $familyName = $username . "'s Family";
    $stmt = $conn->prepare("INSERT INTO rodziny (nazwa) VALUES (?)");
    $stmt->bind_param("s", $familyName);
    $stmt->execute();
    $rodzina_id = $conn->insert_id;

    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role, rodzina_id) VALUES (?, ?, ?, 'user', ?)");
    $stmt->bind_param("sssi", $username, $hashedPassword, $email, $rodzina_id);
    $stmt->execute();

    $_SESSION['success'] = "Rejestracja zakończona pomyślnie!";
    header("Location: ../index.php");
    exit();
}
?>
