<?php
require_once __DIR__ . '/../baza.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /apteczka/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt_user = $conn->prepare("SELECT user_id, username FROM users WHERE user_id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$uzytkownik = $user_result->fetch_assoc();
$stmt_user->close();

$stmt_rodzina = $conn->prepare("SELECT id, imie FROM czlonkowie_rodziny WHERE user_id = ?");
$stmt_rodzina->bind_param("i", $user_id);
$stmt_rodzina->execute();
$rodzina_result = $stmt_rodzina->get_result();
$czlonkowie_rodziny = $rodzina_result->fetch_all(MYSQLI_ASSOC);
$stmt_rodzina->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Nowa apteczka</title>
    <link rel="stylesheet" href="/apteczka/css/n.css">
</head>
<body>
<?php include __DIR__ . "/../scripts/pasek_nawigacyjny.php"; ?>

<div id="nowa-apteczka" class="form-container">
    <h3 class="form-heading">Utwórz nową apteczkę</h3>

    <form method="POST" action="/apteczka/scripts/dodaj_apteczke.php" class="form">
        <div class="form-group">
            <label for="nazwa_apteczki">Nazwa apteczki:</label>
            <input type="text" name="nazwa_apteczki" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="typ_apteczki">Typ:</label>
            <select name="typ_apteczki" class="form-select" required>
                <option value="domowa">Domowa</option>
                <option value="podróżna">Podróżna</option>
                <option value="osobista">Osobista</option>
                <option value="inna">Inna</option>
            </select>
        </div>

        <div class="form-group">
            <label for="wlasciciel_id">Właściciel apteczki:</label>
            <select name="wlasciciel_id" class="form-select" required>
  
                <option value="<?= htmlspecialchars($uzytkownik['user_id']) ?>">
                    <?= htmlspecialchars($uzytkownik['username']) ?> (Ty)
                </option>
                

                <?php foreach ($czlonkowie_rodziny as $czlonek): ?>
                    <option value="rodzina_<?= htmlspecialchars($czlonek['id']) ?>">
                        <?= htmlspecialchars($czlonek['imie']) ?> (członek rodziny)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <button type="submit" name="dodaj_apteczke" class="form-button">📁 Zapisz apteczkę</button>
        </div>
    </form>
</div>

</body>
</html>
