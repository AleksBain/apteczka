<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moja Apteczka</title>
    <link rel="stylesheet" href="/apteczka/css/n.css">
</head>
<body>

<div class="container">
    <?php include __DIR__.'/formularz_dodaj_lek.php'; ?>
    <hr>
    <?php include __DIR__.'/tabela_apteczki.php'; ?>
</div>

<script>
document.getElementById('lek_szukaj').addEventListener('input', function () {
    const zapytanie = this.value;
    if (zapytanie.length < 2) return;

    fetch('/apteczka/ajax_szukaj_leki.php?q=' + encodeURIComponent(zapytanie))
        .then(res => res.json())
        .then(data => {
            const sugestie = document.getElementById('sugestie');
            sugestie.innerHTML = '';
            data.forEach(lek => {
                const opcja = document.createElement('div');
                opcja.textContent = lek.nazwa;
                opcja.dataset.id = lek.opakowanie_id;
                opcja.addEventListener('click', () => {
                    document.getElementById('lek_szukaj').value = lek.nazwa;
                    document.getElementById('opakowanie_id').value = lek.opakowanie_id;
                    sugestie.innerHTML = '';
                });
                sugestie.appendChild(opcja);
            });
        });
});
</script>

</body>
</html>
