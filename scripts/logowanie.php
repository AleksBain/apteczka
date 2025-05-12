<?php
include "baza.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute() ;
    $stmt->bind_result($id, $hashedPassword, $role);

    if ($stmt->fetch() && password_verify($password, $hashedPassword)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        header("Location: /apteczka/index.php");
        exit;
        
        // Możesz przekierować np. do dashboardu:
        // header("Location: dashboard.php");
    } else {
        echo "Błędny login lub hasło.";
    }
}
?>

<form method="POST">
    <label for="username">Nazwa użytkownika:</label>
    <input type="text" name="username" required> <br>

    <label for="username">Hasło:</label>
    <input type="password" name="password" required><br>
    <input type="submit" value="Zaloguj">
</form>
