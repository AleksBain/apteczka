<?php
include "baza.php";

// Obsługa usunięcia użytkownika
if (isset($_GET['usun_id'])) {
    $usun_id = intval($_GET['usun_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $usun_id);
    $stmt->execute();
    echo "<p>Użytkownik został usunięty.</p>";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['dodaj_uzytkownika'])) {
    $nowy_username = $_POST['nowy_username'];
    $rola = $_POST['rola'];
    $nowe_haslo = $_POST['nowe_haslo'];

    // Jeśli rola to 'pet', ustawiamy hasło na NULL, inaczej hasło musi być wymagane
    if ($rola === 'pet') {
        $haslo_hash = null; // Hasło nie jest wymagane dla zwierzaka
    } else {
        if (empty($nowe_haslo)) {
            echo "<p>Hasło jest wymagane dla użytkownika.</p>";
            exit;
        }
        $haslo_hash = password_hash($nowe_haslo, PASSWORD_DEFAULT); // Hasło dla innych ról
    }

    $email = $_POST['email']; // Zbieramy email z formularza

    // Przygotowanie zapytania SQL
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nowy_username, $haslo_hash, $email, $rola); // Dodajemy email jako parametr
    $stmt->execute();
    echo "<p>Dodano użytkownika $nowy_username</p>";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Użytkownicy</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        form.inline { display: inline; }
    </style>
</head>
<body>

<h1>Lista użytkowników</h1>

<table>
    <tr><th>Nazwa</th><th>Rola</th><th>Akcje</th></tr>
    <?php
    $result = $conn->query("SELECT id, username, role FROM users");
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $username = htmlspecialchars($row['username']);
        $role = htmlspecialchars($row['role']);

        echo "<tr><td>$username</td><td>$role</td><td>";

        // Opcje dla wszystkich użytkowników, w tym usuwanie
        echo "<a href='?usun_id=$id' onclick='return confirm(\"Na pewno usunąć?\")'>🗑 Usuń</a> ";
        echo "<a href='leki.php?user_id=$id'>🧪 Zażywane leki</a> ";
        echo "<a href='edytuj.php?id=$id'>✏️ Edytuj</a>";

        echo "</td></tr>";
    }
    ?>
</table>

<h2>Dodaj nowego użytkownika</h2>
<form method="POST">
    <label>Nazwa użytkownika: <input type="text" name="nowy_username" required></label><br>
    <label>Hasło (zostaw puste dla zwierzaka): <input type="password" name="nowe_haslo"></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Rola: 
        <select name="rola">
            <option value="user">user</option>
            <option value="pet">pet</option>
            <option value="admin">admin</option>
        </select>
    </label><br>
    <input type="submit" name="dodaj_uzytkownika" value="Dodaj użytkownika">
</form>

