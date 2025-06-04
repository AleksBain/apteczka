<h2>➕ Dodaj lek do apteczki</h2>

<form method="post" action="scripts/dodaj_lek.php">
    <label for="apteczka_id">Wybierz apteczkę:</label>
    <select name="apteczka_id" id="apteczka_id" required>
        <option value="">-- Wybierz apteczkę --</option>
        <?php foreach ($apteczki as $apteczka): ?>
            <option value="<?= $apteczka['id'] ?>">
                <?= htmlspecialchars($apteczka['nazwa']) ?> (<?= $apteczka['typ'] ?>)
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="opakowanie_id">Wybierz lek:</label>
    <input type="text" id="lek_szukaj" placeholder="Wpisz nazwę leku..." autocomplete="off">
    <input type="hidden" name="opakowanie_id" id="opakowanie_id">
    <div id="sugestie"></div>

    <label>Ilość:</label>
    <input type="number" name="ilosc" min="0" step="1" required><br>

    <label>Cena (zł):</label>
    <input type="number" name="cena" min="0" step="1" required><br>

    <label>Termin ważności:</label>
    <input type="date" name="termin"><br>

    <button type="submit" name="dodaj">💾 Dodaj</button>
</form>

<script>
document.getElementById("szukaj_leku").addEventListener("input", function() {
    const query = this.value;
    if (query.length < 2) return;

    fetch(`/apteczka/scripts/ajax_szukaj_leki.php?q=` + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById("suggestions");
            container.innerHTML = "";
            data.forEach(item => {
                const div = document.createElement("div");
                div.style.cursor = "pointer";
                div.style.padding = "4px";
                div.textContent = item.nazwa;

                div.onclick = () => {
                    document.querySelector("input[name='opakowanie_id']").value = item.opakowanie_id;
                    document.getElementById("szukaj_leku").value = item.nazwa;
                    container.innerHTML = "";
                };

                container.appendChild(div);
            });
        });
});

</script>
