<?php
session_start();
require_once 'baza.php';
?>

<!DOCTYPE html>
<html lang="pl">
<style>
    .family-list form {
    display: inline;
    margin: 0;
    padding: 0;
}

.family-list .form-button {
    font-size: 0.9rem;
    padding: 4px 10px;
    margin-left: 10px;
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    border-radius: 6px;
    cursor: pointer;
    vertical-align: middle;
}

.family-list .form-button:hover {
    background-color: #e0e0e0;
}
</style>
<head>
    <meta charset="UTF-8">
    <title>Rodzina</title>
    <link rel="stylesheet" href="/apteczka/css/n.css">
</head>
<body>
<div class="wrapper">

<?php if (!isset($_SESSION['username'])): ?>
    <h2>Zaloguj się, aby zobaczyć swoją rodzinę.</h2>
    <?php
        include __DIR__ . "/../views/formularz_logowania.php";
        include __DIR__ . "/../views/formularz_rejestracji.php";
    ?>
<?php else: ?>

    <?php include "scripts/pasek_nawigacyjny.php"; ?>

    <div class="content">
        <h1>👨‍👩‍👧‍👦 Twoja rodzina</h1>

        <?php
        
        $user_id = $_SESSION['user_id'];

        
        $stmt = $conn->prepare("SELECT id, imie, relacja FROM czlonkowie_rodziny WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $czlonkowie = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        ?>

        <?php if (!empty($czlonkowie)): ?>
            <ul class="family-list">
                <?php foreach ($czlonkowie as $czlonek): ?>
                    <li>
                        <strong><?= htmlspecialchars($czlonek['imie']) ?></strong>
                        <?php if ($czlonek['relacja']): ?>
                            – <?= htmlspecialchars($czlonek['relacja']) ?>
                        <?php endif; ?>

                        <form action="/apteczka/scripts/usun_czlonka_rodziny.php" method="POST" class="inline-form">
                            <input type="hidden" name="czlonek_id" value="<?= $czlonek['id'] ?>">
                                <button type="submit" class="form-button" onclick="return confirm('Czy na pewno chcesz usunąć tego członka rodziny?');">🗑️ Usuń</button>
                        </form>

                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Nie dodałeś jeszcze żadnego członka rodziny.</p>
        <?php endif; ?>

        <hr>

        <h2>➕ Dodaj nowego członka rodziny</h2>
        <form action="/apteczka/scripts/api_dodaj_rodzine.php" method="POST" class="form">
            <div class="form-group">
                <label for="imie">Imię:</label>
                <input type="text" name="imie" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="relacja">Relacja (np. mama, brat, dziecko):</label>
                <input type="text" name="relacja" class="form-input">
            </div>
            <div class="form-group">
                <button type="submit" class="form-button">💾 Dodaj</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php include "scripts/stopka.php"; ?>
</div>
</body>
</html>
