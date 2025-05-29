<h2>➕ Dodaj lek do apteczki</h2>
<form method="post">
    <label for="opakowanie_id">Wybierz lek:</label>
    <input type="text" id="lek_szukaj" placeholder="Wpisz nazwę leku..." autocomplete="off">
    <input type="hidden" name="opakowanie_id" id="opakowanie_id">
    <div id="sugestie"></div>

    <label>Ilość:</label>
    <input type="number" name="ilosc" min="1" required><br>

    <label>Cena (zł):</label>
    <input type="number" name="cena" step="0.01"><br>

    <label>Termin ważności:</label>
    <input type="date" name="termin"><br>

    <button type="submit" name="dodaj">💾 Dodaj</button>
</form>
