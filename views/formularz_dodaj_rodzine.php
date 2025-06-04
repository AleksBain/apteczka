<?php
require_once __DIR__ . '/../baza.php';
session_start();
?>

<DOCTYPE html>
<h3 class="form-heading">Dodaj członka rodziny</h3>
<form method="POST" action="/apteczka/scripts/api_dodaj_rodzine.php" class="form">
    <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">

    <div class="form-group">
        <label for="imie">Imię członka rodziny:</label>
        <input type="text" name="imie" class="form-input" required>
    </div>

    <div class="form-group">
        <label for="relacja">Relacja (np. mama, syn, dziadek):</label>
        <input type="text" name="relacja" class="form-input">
    </div>

    <div class="form-group">
        <button type="submit" class="form-button">👨‍👩‍👧‍👦 Dodaj członka</button>
    </div>
</form>
