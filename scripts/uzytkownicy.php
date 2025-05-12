<?php
include "baza.php";

// Usuwanie użytkownika
if (isset($_GET['usun_id'])) {
    $usun_id = intval($_GET['usun_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $usun_id);
    $stmt->execute();
    echo "<p>Użytkownik został usunięty.</p>";
}

// Dodawanie nowego użytkownika
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['dodaj_uzytkownika'])) {
    $nowy_username = htmlspecialchars($_POST['nowy_username']);
    $rola = $_POST['rola'];
    $nowe_haslo = $_POST['nowe_haslo'];
    $email = htmlspecialchars($_POST['email']);

    if ($rola === 'pet') {
        $haslo_hash = null;
    } else {
        if (empty($nowe_haslo)) {
            echo "<p>Hasło jest wymagane dla użytkownika.</p>";
            exit;
        }
        $haslo_hash = password_hash($nowe_haslo, PASSWORD_DEFAULT);
    }

    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nowy_username, $haslo_hash, $email, $rola);
    $stmt->execute();

    echo "<p>Dodano użytkownika $nowy_username</p>";
}

// Edycja użytkownika
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edytuj_uzytkownika'])) {
    $edytuj_id = intval($_POST['edytuj_id']);
    $nowa_nazwa = htmlspecialchars($_POST['edytuj_username']);
    $nowy_email = htmlspecialchars($_POST['edytuj_email']);
    $nowa_rola = $_POST['edytuj_rola'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $nowa_nazwa, $nowy_email, $nowa_rola, $edytuj_id);

    $stmt->execute();

    echo "<p>Użytkownik został zaktualizowany.</p>";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Użytkownicy</title>
    <link rel="stylesheet" href="/apteczka/css/n.css">
    <style>
        .hidden-form {
            display: none;
            margin-top: 1em;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include __DIR__ . "/pasek_nawigacyjny.php"; ?>

    <main>
        <h1 class="page-title">Użytkownicy</h1>

        <!-- Lista użytkowników -->
        <section>
            <table>
                <thead>
                    <tr>
                        <th>Nazwa</th>
                        <th>Rola</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT user_id, username, role FROM users");
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['user_id'];
                        $username = htmlspecialchars($row['username']);
                        $role = htmlspecialchars($row['role']);
                        echo "<tr>
                                <td>$username</td>
                                <td>$role</td>
                                <td>
                                    <a href='uzytkownicy.php?usun_id=$id' onclick='return confirm(\"Na pewno usunąć?\")'>🗑 Usuń</a>
                                    <button onclick='pokazFormularzEdycji($id)'>✏️ Edytuj</button>
                                    <a href='leki.php?user_id=$id'>🧪 Zażywane leki</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <!-- Przycisk pokaż formularz dodawania -->
        <section>
            <button onclick="document.getElementById('formularz-dodaj').style.display='block'">➕ Dodaj użytkownika</button>

            <div id="formularz-dodaj" class="hidden-form">
                <h2>Dodaj nowego użytkownika</h2>
                <form method="POST">
                    <label>
                        Nazwa użytkownika:
                        <input type="text" name="nowy_username" required>
                    </label><br>

                    <label>
                        Hasło (zostaw puste dla zwierzaka):
                        <input type="password" name="nowe_haslo">
                    </label><br>

                    <label>
                        Email:
                        <input type="email" name="email" required>
                    </label><br>

                    <label>
                        Rola: 
                        <select name="rola">
                            <option value="user">user</option>
                            <option value="pet">pet</option>
                            <option value="admin">admin</option>
                        </select>
                    </label><br>

                    <input type="submit" name="dodaj_uzytkownika" value="Dodaj użytkownika">
                </form>
            </div>
        </section>

        <!-- Formularz edycji (dynamiczny) -->
        <?php
        if (isset($_GET['edytuj_id'])) {
            $edytuj_id = intval($_GET['edytuj_id']);
            $stmt = $conn->prepare("SELECT username, email, role FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $edytuj_id);
            $stmt->execute();
            $stmt->bind_result($edytuj_username, $edytuj_email, $edytuj_rola);
            $stmt->fetch();
            $stmt->close();
        ?>
            <div id="formularz-edytuj" class="hidden-form" style="display: block;">
                <h2>Edytuj użytkownika</h2>
                <form method="POST">
                    <input type="hidden" name="edytuj_id" value="<?php echo $edytuj_id; ?>">
                    <label>
                        Nazwa użytkownika:
                        <input type="text" name="edytuj_username" value="<?php echo htmlspecialchars($edytuj_username); ?>" required>
                    </label><br>
                    
                    <label>
                        Email:
                        <input type="email" name="edytuj_email" value="<?php echo htmlspecialchars($edytuj_email); ?>" required>
                    </label><br>
                    
                    <label>
                        Rola: 
                        <select name="edytuj_rola">
                            <option value="user" <?php if ($edytuj_rola === 'user') echo 'selected'; ?>>user</option>
                            <option value="pet" <?php if ($edytuj_rola === 'pet') echo 'selected'; ?>>pet</option>
                            <option value="admin" <?php if ($edytuj_rola === 'admin') echo 'selected'; ?>>admin</option>
                        </select>
                    </label><br>
                    
                    <input type="submit" name="edytuj_uzytkownika" value="Zapisz zmiany">
                </form>
            </div>
        <?php } ?>

    </main>

    <?php include __DIR__ . "/stopka.php"; ?>
</div>

<script>
function pokazFormularzEdycji(id) {
    window.location.href = 'uzytkownicy.php?edytuj_id=' + id;
}
</script>

</body>
</html>
